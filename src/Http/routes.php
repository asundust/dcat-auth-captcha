<?php

use Asundust\DcatAuthCaptcha\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('dcat-auth-captcha', Controllers\DcatAuthCaptchaController::class . '@index');

if (config('admin.auth.enable', true)) {
    $authController = config('admin.extensions.dcat-auth-captcha.controller', Controllers\DcatAuthCaptchaController::class);
    Route::get('auth/login', $authController . '@getLogin');
    Route::post('auth/login', $authController . '@postLogin');
}
