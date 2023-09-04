<?php

use Asundust\DcatAuthCaptcha\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('dcat-auth-captcha', Controllers\DcatAuthCaptchaController::class . '@index');

if (config('admin.auth.enable', true)) {
    Route::get('auth/login', Controllers\DcatAuthCaptchaController::class . '@getLogin');
    Route::post('auth/login', Controllers\DcatAuthCaptchaController::class . '@postLogin');
}
