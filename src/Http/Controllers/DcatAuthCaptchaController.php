<?php

namespace Asundust\DcatAuthCaptcha\Http\Controllers;

use Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider;
use Asundust\DcatAuthCaptcha\Http\Middleware\DcatAuthCaptchaThrottleMiddleware;
use Asundust\DcatAuthCaptcha\Http\Middleware\DcatAuthCaptchaThrottleMiddlewareBelow8;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AuthController as BaseAuthController;
use Dcat\Admin\Layout\Content;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;

class DcatAuthCaptchaController extends BaseAuthController
{
    public $captchaProvider;

    public $captchaAppid;

    public $captchaSecret;

    public $captchaStyle;

    private $providerStyles = [
        'dingxiang' => [
            'popup' => 'login',
            'embed' => 'login_style',
            'inline' => 'login_style',
            'oneclick' => 'login_style',
        ],
        'geetest' => [
            'bind' => 'login',
            'float' => 'login_style',
            'popup' => 'login_style',
            'custom' => 'login_style',
        ],
        'hcaptcha' => [
            'invisible' => 'login',
            'display' => 'login_style',
        ],
        'recaptchav2' => [
            'invisible' => 'login',
            'display' => 'login_style',
        ],
        'recaptcha' => [
            'default' => 'login',
        ],
        'tencent' => [
            'popup' => 'login',
        ],
        'verify5' => [
            'default' => 'login_style',
        ],
        'vaptcha' => [
            'invisible' => 'login',
            'click' => 'login_style',
            'embed' => 'login_style',
        ],
        'wangyi' => [
            'popup' => 'login',
            'float' => 'login_style',
            'embed' => 'login_style',
            'bind' => 'login',
            '' => 'login_style',
        ],
        'yunpian' => [
            'flat' => 'login_style',
            'float' => 'login_style',
            'dialog' => 'login_style',
            'external' => 'login_style',
        ],
    ];

    /**
     * AuthCaptchaController constructor.
     */
    public function __construct()
    {
        $this->captchaProvider = DcatAuthCaptchaServiceProvider::setting('provider');
        $this->captchaAppid = DcatAuthCaptchaServiceProvider::setting('appid');
        $this->captchaSecret = DcatAuthCaptchaServiceProvider::setting('secret');
        $this->captchaStyle = DcatAuthCaptchaServiceProvider::setting('style');

        $throttle = DcatAuthCaptchaServiceProvider::setting('login_try_throttle');
        if ($throttle) {
            $version = app()->make('\Mnabialek\LaravelVersion\Version');
            $arr = explode('.', $version->get());
            if ($arr[0] >= 8) {
                $this->middleware(DcatAuthCaptchaThrottleMiddleware::class . ':' . $throttle)->only('postLogin');
            } else {
                $this->middleware(DcatAuthCaptchaThrottleMiddlewareBelow8::class . ':' . $throttle)->only('postLogin');
            }
        }
    }

    /**
     * Get Login.
     *
     * @return Content|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLogin(Content $content)
    {
        if ($this->guard()->check()) {
            return redirect($this->getRedirectPath());
        }

        $extConfig = [];
        $json = DcatAuthCaptchaServiceProvider::setting('ext_config');
        if ($json) {
            $result = json_decode($json, true);
            if ($result) {
                $extConfig = $result;
            }
        }

        switch ($this->captchaProvider) {
            case 'dingxiang':
            case 'tencent':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'popup';
                }

                break;
            case 'geetest':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'bind';
                }
                Admin::headerJs('vendor/dcat-admin-extensions/asundust/dcat-auth-captcha/js/geetest/gt.js');
                $extConfig['initData'] = $this->getGeetestStatus();

                break;
            case 'hcaptcha':
            case 'vaptcha':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'invisible';
                }

                break;
            case 'recaptchav2':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'invisible';
                    $extConfig['initData']['domain'] = DcatAuthCaptchaServiceProvider::setting('domain');
                }

                break;
            case 'recaptcha':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'default';
                    $extConfig['initData']['domain'] = DcatAuthCaptchaServiceProvider::setting('domain');
                }

                break;
            case 'verify5':
                $extConfig['token'] = $this->getVerify5Token();
                $this->captchaStyle = 'default';
                $extConfig['initData']['host'] = DcatAuthCaptchaServiceProvider::setting('host');

                break;
            case 'wangyi':
                if (null === $this->captchaStyle) {
                    $this->captchaStyle = 'popup';
                }

                break;
            case 'yunpian':
                if (!$this->captchaStyle) {
                    $this->captchaStyle = 'dialog';
                }

                break;

            default:
                break;
        }

        return $content->full()->body(view(DcatAuthCaptchaServiceProvider::instance()->getName() . '::' . $this->captchaProvider . '.' . $this->providerStyles[$this->captchaProvider][$this->captchaStyle], [
            'captchaAppid' => $this->captchaAppid,
            'captchaStyle' => $this->captchaStyle,
            'extConfig' => $extConfig,
        ]));
    }

    /**
     * Get Geetest Status.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getGeetestStatus(): array
    {
        $clientType = Agent::isMobile() ? 'h5' : 'web';
        session(['GeetestAuth-client_type' => $clientType]);
        $params = [
            'client_type' => $clientType,
            'gt' => $this->captchaAppid,
            'ip_address' => request()->ip(),
            'new_captcha' => 1,
            'user_id' => '',
        ];
        $url = 'http://api.geetest.com/register.php?' . http_build_query($params);
        $response = $this->captchaHttp()->get($url);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->geetestFailProcess();
        }
        if (32 != strlen($contents)) {
            return $this->geetestFailProcess();
        }

        return $this->geetestSuccessProcess($contents);
    }

    /**
     * Geetest Success Process.
     *
     * @param $challenge
     */
    private function geetestSuccessProcess($challenge): array
    {
        $challenge = md5($challenge . $this->captchaSecret);
        $result = [
            'success' => 1,
            'gt' => $this->captchaAppid,
            'challenge' => $challenge,
            'new_captcha' => 1,
        ];
        session(['GeetestAuth-gtserver' => 1, 'GeetestAuth-user_id' => '']);

        return $result;
    }

    /**
     * Geetest Fail Process.
     */
    private function geetestFailProcess(): array
    {
        $rnd1 = md5(rand(0, 100));
        $rnd2 = md5(rand(0, 100));
        $challenge = $rnd1 . substr($rnd2, 0, 2);
        $result = [
            'success' => 0,
            'gt' => $this->captchaAppid,
            'challenge' => $challenge,
            'new_captcha' => 1,
        ];
        session(['GeetestAuth-gtserver' => 0, 'GeetestAuth-user_id' => 0]);

        return $result;
    }

    /**
     * Get Verify5 Token.
     *
     * @return mixed|string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getVerify5Token()
    {
        $params = [
            'appid' => $this->captchaAppid,
            'timestamp' => now()->timestamp . '000',
        ];
        $params['signature'] = $this->getSignature($this->captchaSecret, $params);
        $url = 'https://' . DcatAuthCaptchaServiceProvider::setting('host') . '/openapi/getToken?' . http_build_query($params);
        $response = $this->captchaHttp()->get($url);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return '';
        }
        $result = json_decode($contents, true);
        if (true != $result['success']) {
            return '';
        }

        return $result['data']['token'];
    }

    /**
     * Post Login.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|mixed|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postLogin(Request $request)
    {
        switch ($this->captchaProvider) {
            case 'dingxiang':
                return $this->captchaValidateDingxiang($request);

                break;
            case 'geetest':
                return $this->captchaValidateGeetest($request);

                break;
            case 'hcaptcha':
                return $this->captchaValidateHCaptcha($request);

                break;
            case 'recaptchav2':
            case 'recaptcha':
                return $this->captchaValidateRecaptcha($request);

                break;
            case 'tencent':
                return $this->captchaValidateTencent($request);

                break;
            case 'verify5':
                return $this->captchaValidateVerify5($request);

                break;
            case 'vaptcha':
                return $this->captchaValidateVaptcha($request);

                break;
            case 'wangyi':
                return $this->captchaValidateWangyi($request);

                break;
            case 'yunpian':
                return $this->captchaValidateYunpian($request);

                break;

            default:
                return back()->withInput()->withErrors(['captcha' => $this->toTrans('config')]);

                break;
        }
    }

    /**
     * Dingxiang Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateDingxiang(Request $request)
    {
        $token = $request->input('token', '');
        if (!$token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $tokenArr = array_filter(explode(':', $token));
        if (2 != count($tokenArr)) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'appKey' => $this->captchaAppid,
            'constId' => $tokenArr[1],
            'sign' => md5($this->captchaSecret . $tokenArr[0] . $this->captchaSecret),
            'token' => $tokenArr[0],
        ];

        $url = 'https://cap.dingxiang-inc.com/api/tokenVerify';
        $response = $this->captchaHttp()->get($url . '?' . http_build_query($params));
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (true === $result['success']) {
            return $this->loginValidate($request);
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * Geetest Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateGeetest(Request $request)
    {
        $geetestChallenge = $request->input('geetest_challenge', '');
        $geetestValidate = $request->input('geetest_validate', '');
        $geetestSeccode = $request->input('geetest_seccode', '');
        if (!$geetestChallenge || !$geetestValidate || !$geetestSeccode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        if (1 != session('GeetestAuth-gtserver')) {
            if (md5($geetestChallenge) == $geetestValidate) {
                return $this->loginValidate($request);
            }

            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'challenge' => $geetestChallenge,
            'client_type' => session('GeetestAuth-client_type'),
            'gt' => $this->captchaAppid,
            'ip_address' => $request->ip(),
            'json_format' => 1,
            'new_captcha' => 1,
            'sdk' => 'php_3.0.0',
            'seccode' => $geetestSeccode,
            'user_id' => session('GeetestAuth-user_id'),
            'validate' => $geetestValidate,
        ];

        $url = 'http://api.geetest.com/validate.php';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (is_array($result) && $result['seccode'] == md5($geetestSeccode)) {
            return $this->loginValidate($request);
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * HCaptcha Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateHCaptcha(Request $request)
    {
        $token = $request->input('token', '');
        if (!$token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'secret' => $this->captchaSecret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ];

        $url = 'https://hcaptcha.com/siteverify';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (true === $result['success']) {
            return $this->loginValidate($request);
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * Recaptcha Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateRecaptcha(Request $request)
    {
        $token = $request->input('token', '');
        if (!$token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'secret' => $this->captchaSecret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ];

        $url = rtrim(DcatAuthCaptchaServiceProvider::setting('domain') ?? 'https://recaptcha.net') . '/recaptcha/api/siteverify';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if ('recaptcha' == $this->captchaProvider) {
            if (true === $result['success'] && $result['score'] >= DcatAuthCaptchaServiceProvider::setting('score') ?? 0.7) {
                return $this->loginValidate($request);
            }
        } else {
            if (true === $result['success']) {
                return $this->loginValidate($request);
            }
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * Tencent Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateTencent(Request $request)
    {
        $ticket = $request->input('ticket', '');
        $randstr = $request->input('randstr', '');
        if (!$ticket || !$randstr) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'aid' => $this->captchaAppid,
            'AppSecretKey' => $this->captchaSecret,
            'Ticket' => $ticket,
            'Randstr' => $randstr,
            'UserIP' => $request->getClientIp(),
        ];

        $url = 'https://ssl.captcha.qq.com/ticket/verify';
        $response = $this->captchaHttp()->get($url . '?' . http_build_query($params));
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (1 != $result['response']) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        return $this->loginValidate($request);
    }

    /**
     * Verify5 Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateVerify5(Request $request)
    {
        $token = $request->input('token', '');
        $verify5Token = $request->input('verify5_token', '');
        if (!$token || !$verify5Token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'host' => DcatAuthCaptchaServiceProvider::setting('host'),
            'verifyid' => $token,
            'token' => $verify5Token,
            'timestamp' => now()->timestamp . '000',
        ];
        $params['signature'] = $this->getSignature($this->captchaSecret, $params);
        $url = 'https://' . DcatAuthCaptchaServiceProvider::setting('host') . '/openapi/verify?' . http_build_query($params);
        $response = $this->captchaHttp()->get($url);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (true != $result['success']) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        return $this->loginValidate($request);
    }

    /**
     * Vaptcha Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateVaptcha(Request $request)
    {
        $token = $request->input('token', '');
        if (!$token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $params = [
            'id' => $this->captchaAppid,
            'secretkey' => $this->captchaSecret,
            'token' => $token,
            'ip' => $request->ip(),
        ];

        $url = 'http://0.vaptcha.com/verify';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (1 != $result['success']) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        return $this->loginValidate($request);
    }

    /**
     * Wangyi Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateWangyi(Request $request)
    {
        $token = $request->input('token', '');
        if (!$token) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $secretKey = config('admin.extensions.dcat-auth-captcha.secret_key', '');
        if (!$secretKey) {
            return back()->withInput()->withErrors(['captcha' => $this->toTrans('config')]);
        }

        $params = [
            'captchaId' => $this->captchaAppid,
            'validate' => $token,
            'user' => '',
            'secretId' => $this->captchaSecret,
            'version' => 'v2',
            'timestamp' => now()->timestamp . '000',
            'nonce' => Str::random(),
        ];

        $params['signature'] = $this->getSignature($secretKey, $params);

        $url = 'http://c.dun.163yun.com/api/v2/verify';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (true === $result['result']) {
            return $this->loginValidate($request);
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * Yunpian Captcha.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function captchaValidateYunpian(Request $request)
    {
        $token = $request->input('token', '');
        $authenticate = $request->input('authenticate', '');
        if (!$token || !$authenticate) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }

        $secretKey = config('admin.extensions.dcat-auth-captcha.secret_key', '');
        if (!$secretKey) {
            return back()->withInput()->withErrors(['captcha' => $this->toTrans('config')]);
        }

        $params = [
            'authenticate' => $authenticate,
            'captchaId' => $this->captchaAppid,
            'token' => $token,
            'secretId' => $this->captchaSecret,
            'user' => '',
            'version' => '1.0',
            'timestamp' => now()->timestamp . '000',
            'nonce' => Str::random(),
        ];

        $params['signature'] = $this->getSignature($secretKey, $params);

        $url = 'https://captcha.yunpian.com/v1/api/authenticate';
        $response = $this->captchaHttp()->post($url, [
            'form_params' => $params,
        ]);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        if (200 != $statusCode) {
            return $this->captchaErrorResponse($this->toTrans('fail'));
        }
        $result = json_decode($contents, true);
        if (0 === $result['code'] && 'ok' == $result['msg']) {
            return $this->loginValidate($request);
        }

        return $this->validationErrorsResponse([
            'captcha' => $this->toTrans('fail'),
        ]);
    }

    /**
     * 生成签名信息.
     *
     * @param $secretKey
     * @param $params
     */
    private function getSignature($secretKey, $params): string
    {
        ksort($params);
        $str = '';
        foreach ($params as $key => $value) {
            $str .= $key . $value;
        }
        $str .= $secretKey;

        return md5($str);
    }

    /**
     * Login Validate.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function loginValidate(Request $request)
    {
        $credentials = $request->only([$this->username(), 'password']);
        $remember = (bool) $request->input('remember', false);

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($credentials, [
            $this->username() => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorsResponse($validator);
        }

        if ($this->guard()->attempt($credentials, $remember)) {
            return $this->sendLoginResponse($request);
        }

        return $this->validationErrorsResponse([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Http.
     */
    private function captchaHttp(): Client
    {
        return new Client([
            'timeout' => DcatAuthCaptchaServiceProvider::setting('timeout', 5),
            'verify' => false,
            'http_errors' => false,
        ]);
    }

    /**
     * getErrorMessage.
     *
     * @param $type
     */
    private function toTrans($type): ?string
    {
        return DcatAuthCaptchaServiceProvider::trans('dcat-auth-captcha.messages.' . $type);
    }

    /**
     * CaptchaErrorResponse.
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function captchaErrorResponse($message)
    {
        return $this->response()
            ->error($message)
            ->locationToIntended($this->getRedirectPath())
            ->send();
    }
}
