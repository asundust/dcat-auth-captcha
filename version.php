<?php

return [
    '2.0.0' => [
        '移植[https://github.com/asundust/auth-captcha](https://github.com/asundust/auth-captcha)第一版',
        '大版本号跟随dcat-admin版本号走',
    ],
    '2.0.1' => [
        '兼容guzzlehttp/guzzle 7.*版本',
    ],
    '2.0.2' => [
        '添加验证时请求超时配置(可选配置)',
        '添加尝试登录限流功能（兼容Laravel8和Laravel8以下的限流中间件）',
    ],
];
