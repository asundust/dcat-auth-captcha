<?php

namespace Asundust\DcatAuthCaptcha;

use Dcat\Admin\Extend\ServiceProvider;

class DcatAuthCaptchaServiceProvider extends ServiceProvider
{
    protected $js = [
        'js/geetest/gt.js',
    ];

    public function register()
    {
        //
    }

    public function init()
    {
        parent::init();

        //

    }

    public function settingForm()
    {
        return new Setting($this);
    }
}
