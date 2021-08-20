<?php

namespace Asundust\DcatAuthCaptcha\Http\Middleware;

use Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider;
use Closure;
use Dcat\Admin\Traits\HasFormResponse;
use Illuminate\Routing\Middleware\ThrottleRequests;

class DcatAuthCaptchaThrottleMiddlewareBelow8 extends ThrottleRequests
{
    use HasFormResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|string               $maxAttempts
     * @param float|int                $decayMinutes
     * @param string                   $prefix
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        $key = $prefix.$this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildException($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param string $key
     * @param int    $maxAttempts
     *
     * @return \Illuminate\Http\Exceptions\ThrottleRequestsException|\Illuminate\Http\JsonResponse
     */
    protected function buildException($key, $maxAttempts)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return $this->captchaErrorResponse($this->getMessage());
    }

    /**
     * @return array|string|null
     */
    private function getMessage()
    {
        return DcatAuthCaptchaServiceProvider::trans('dcat-auth-captcha.messages.login_try_throttle_error');
    }

    /**
     * CaptchaErrorResponse.
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function captchaErrorResponse($message)
    {
        return $this->response()
            ->error($message)
            ->send();
    }
}
