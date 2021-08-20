<?php

namespace Asundust\DcatAuthCaptcha\Http\Middleware;

use Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider;
use Closure;
use Dcat\Admin\Traits\HasFormResponse;
use Illuminate\Routing\Middleware\ThrottleRequests;

class DcatAuthCaptchaThrottleMiddleware extends ThrottleRequests
{
    use HasFormResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param array $limits
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    protected function handleRequest($request, Closure $next, array $limits)
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                return $this->buildException($request, $limit->key, $limit->maxAttempts, $limit->responseCallback);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @param int $maxAttempts
     * @param callable|null $responseCallback
     * @return \Illuminate\Http\Exceptions\HttpResponseException|\Illuminate\Http\Exceptions\ThrottleRequestsException|\Illuminate\Http\JsonResponse
     */
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
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
