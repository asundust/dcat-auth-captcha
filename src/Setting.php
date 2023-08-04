<?php

namespace Asundust\DcatAuthCaptcha;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function title()
    {
        return $this->toTrans('config');
    }

    public function form()
    {
        $providers = [
            'dingxiang' => $this->toTrans('providers.dingxiang'),
            'geetest' => $this->toTrans('providers.geetest'),
            'hcaptcha' => $this->toTrans('providers.hcaptcha'),
            'recaptchav2' => $this->toTrans('providers.recaptchav2'),
            'recaptcha' => $this->toTrans('providers.recaptcha'),
            'tencent' => $this->toTrans('providers.tencent'),
            'verify5' => $this->toTrans('providers.verify5'),
            // 'vaptcha' => $this->toTrans('providers.vaptcha'),
            // 'wangyi' => $this->toTrans('providers.wangyi'),
            // 'yunpian' => $this->toTrans('providers.yunpian'),
        ];

        $documentHelp = $this->toTrans('parameters_document');
        $documentAddress = ' <a href="https://github.com/asundust/dcat-auth-captcha/blob/master/README.md#%E8%8E%B7%E5%8F%96%E5%AF%86%E9%92%A5%E5%8F%82%E6%95%B0%E9%85%8D%E7%BD%AE" target="_blank">' . $this->toTrans('document_address') . '</a>';

        $this->select('provider', $this->toTrans('provider'))
            ->options($providers)
            ->required();
        $this->text('appid', $this->toTrans('appid'))->required();
        $this->text('secret', $this->toTrans('secret'))->required();
        $this->text('style', $this->toTrans('style'))
            ->help($documentHelp . $documentAddress);
        $this->text('timeout', $this->toTrans('timeout'))
            ->help($this->toTrans('timeout_help'));
        $this->text('secret_key', $this->toTrans('secret_key'))
            ->help($documentHelp . $documentAddress);
        $this->text('host', $this->toTrans('host'))
            ->help($documentHelp . $documentAddress);
        $this->text('domain', $this->toTrans('domain'))
            ->help($documentHelp . $documentAddress);
        $this->text('score', $this->toTrans('score'))
            ->help($documentHelp . $documentAddress);
        $this->textarea('ext_config', $this->toTrans('ext_config'))
            ->help($this->toTrans('tip_json'));
        if (in_array(config('app.locale'), ['zh-CN', 'zh_CN'])) {
            $throttleHref = 'https://learnku.com/docs/laravel/7.x/routing/7458#5c3711';
        } else {
            $throttleHref = 'https://laravel.com/docs/7.x/routing#rate-limiting';
        }
        $throttleDocumentAddress = ' <a href="' . $throttleHref . '" target="_blank">' . $this->toTrans('document_address') . '</a>';
        $this->text('login_try_throttle', $this->toTrans('login_try_throttle'))
            ->help($documentHelp . $throttleDocumentAddress);
    }

    /**
     * @param $key
     */
    public function toTrans($key): string
    {
        return $this->trans('dcat-auth-captcha.' . $key);
    }
}
