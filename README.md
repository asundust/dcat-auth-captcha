dcat-admin登陆 滑动验证插件 多平台支持
======

![StyleCI build status](https://github.styleci.io/repos/355441406/shield)

dcat-admin登陆 滑动验证插件 多平台支持

> 另有 [laravel-admin版](https://github.com/asundust/auth-captcha)

### Demo演示

[~~演示站点~~](https://captcha.leeay.com)（暂时无，目前地址为laravel-admin版的演示地址）

### 支持(按照字母顺序)

- [顶象](https://www.dingxiang-inc.com/business/captcha):heavy_check_mark:
- [极验](http://www.geetest.com):heavy_check_mark:
- [hCaptcha(和谷歌Recaptcha v2一样)](https://www.hcaptcha.com):heavy_check_mark:（**免费，速度一般**）
- [Recaptcha v2(谷歌)](https://developers.google.com/recaptcha):heavy_check_mark:（**国内可用，完全免费**）
- [Recaptcha v3(谷歌)](https://developers.google.com/recaptcha):heavy_check_mark:（**国内可用，完全免费**）
- [~~数美(暂不支持网页)~~](https://www.ishumei.com/product/bs-post-register.html)
- [腾讯防水墙](https://cloud.tencent.com/document/product/1110/36839):heavy_check_mark:
- [同盾](https://x.tongdun.cn/product/captcha)
- [V5验证](https://www.verify5.com/index)（**免费版日限500次**）
- [Vaptcha](https://www.vaptcha.com)（**不完全免费，不过该验证码使用难度相对较高**）
- [网易](http://dun.163.com/product/captcha)
- [云片](https://www.yunpian.com/product/captcha)
- 有主流的未发现的、额外有需求的请[issue](https://github.com/asundust/dcat-auth-captcha/issues)

> 受限制于有些验证码密钥是收费版，目前代码不能做到完全兼容 如果有好心人士提供密码 我将严格保密 仅用于开发工作

> 目前不打算开发兼容1.x版本的代码

### 截图

![img](https://user-images.githubusercontent.com/6573979/94363320-123b0000-00b1-11eb-9357-4dfdcf88960b.gif)

### 安装

```
composer require asundust/dcat-auth-captcha
```

### 获取密钥

#### 顶象

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'dingxiang',
    'style' => 'popup', // 弹出式: popup 嵌入式: embed 内联式: inline 触发式: oneclick (不填写默认popup)
    'appid' => '{AppID}',
    'secret' => '{AppSecret}',
    'ext_config' => [],
],
```

- 访问 [https://www.dingxiang-inc.com/business/captcha](https://www.dingxiang-inc.com/business/captcha)
- [官网文档配置DEMO](https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/demo)
- [官网文档地址](https://www.dingxiang-inc.com/docs/detail/captcha)

#### 极验

- **需要发布配置文件**，命令如下

```
 php artisan vendor:publish --provider="Asundust\AuthCaptcha\AuthCaptchaServiceProvider"
```

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'geetest',
    'style' => 'bind', // 隐藏式: bind 弹出式: popup 浮动式: float 自定区域浮动式(与popup类似，由于登录页面无需自定区域，故效果和popup一样的): custom (不填写默认bind)
    'appid' => '{ID}',
    'secret' => '{KEY}',
    'ext_config' => [],
],
```

- 访问 [https://www.dingxiang-inc.com/business/captcha](https://www.dingxiang-inc.com/business/captcha)
- [官网文档地址](http://docs.geetest.com/sensebot/deploy/server/php)

#### hCaptcha

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'hcaptcha',
    'style' => 'invisible', // 隐藏式: invisible 复选框: display (不填写默认invisible)
    'appid' => '{sitekey}',
    'secret' => '{secret}',
],
```

- 访问 [https://dashboard.hcaptcha.com/overview](https://dashboard.hcaptcha.com/overview)
- [官网文档地址(前端)显示](https://docs.hcaptcha.com/configuration)
- [官网文档地址(前端)隐藏](https://docs.hcaptcha.com/invisible)
- [官网文档地址(后端)](https://docs.hcaptcha.com)

#### Recaptcha v2(谷歌)

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'recaptchav2',
    'style' => 'invisible', // 隐藏式: invisible 复选框: display (不填写默认invisible)
    'appid' => '{site_key}',
    'secret' => '{secret}',
    // 'domain' => 'https://www.google.com', // 服务域名，可选，无此选项默认为 https://recaptcha.net
],
```

- 访问 [https://www.google.com/recaptcha/admin/create](https://www.google.com/recaptcha/admin/create) 选择v2版
- 管理面板 [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)
- [官网文档地址(前端)显示](https://developers.google.com/recaptcha/docs/display)
- [官网文档地址(前端)隐藏](https://developers.google.com/recaptcha/docs/invisible)
- [官网文档地址(后端)](https://developers.google.com/recaptcha/docs/verify/)

#### Recaptcha v3(谷歌)

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'recaptcha',
    'appid' => '{site_key}',
    'secret' => '{secret}',
    // 'domain' => 'https://www.google.com', // 服务域名，可选，无此选项默认为 https://recaptcha.net
    // 'score' => '0.5', // 可信任分数，可选，无此选项默认为 0.7
],
```

- 访问 [https://www.google.com/recaptcha/admin/create](https://www.google.com/recaptcha/admin/create) 选择v3版
- 管理面板 [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)
- [官网文档地址(前端)](https://developers.google.com/recaptcha/docs/v3)
- [官网文档地址(后端)](https://developers.google.com/recaptcha/docs/verify/)

#### 腾讯防水墙

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'tencent',
    'appid' => '{AppID}',
    'secret' => '{AppSecretKey}',
],
```

- 新用户购买 [https://cloud.tencent.com/product/captcha](https://cloud.tencent.com/product/captcha)
- 新用户[官方使用文档地址](https://cloud.tencent.com/document/product/1110/36839)
- 老用户[官方使用文档地址](https://007.qq.com/captcha/#/gettingStart)
- [关于腾讯防水墙收费的声明(新用户终身免费5万次)](https://007.qq.com/help.html?ADTAG=index.head)

#### V5验证

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'verify5',
    'appid' => '{APP ID}',
    'secret' => '{APP Key}',
    'host' => '{Host}',
],
```

- 访问 [https://www.verify5.com/console/app/list](https://www.verify5.com/console/app/list)
- 访问 [官方使用文档地址](https://www.verify5.com/doc/reference)

#### Vaptcha

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'vaptcha',
    'style' => 'invisible', // 隐藏式: invisible 点击式: click 嵌入式: embed (不填写默认invisible)
    'appid' => '{VID}',
    'secret' => '{Key}',
    'ext_config' => [],
],
```

- 访问 [https://www.vaptcha.com](https://www.vaptcha.com)
- 访问 [官方使用文档地址](https://www.vaptcha.com/document/install)

#### 网易易盾

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'wangyi',
    'style' => '', // 注意后台申请的类型！！！ 常规弹出式: popup 常规嵌入式: embed 常规触发式: float 无感绑定按钮：bind 无感点击式: ''(留空，奇葩设定) (不填写默认popup)
    'appid' => '{captchaId}',
    'secret' => '{secretId}',
    'secret_key' => '{secretKey}', // 这里多了一个额外参数，请注意！！！
    'ext_config' => [],
],
```

- 访问 [http://dun.163.com/product/captcha](http://dun.163.com/product/captcha)
- 访问 [官方使用文档地址](http://support.dun.163.com/documents/15588062143475712?docId=150401879704260608)

#### 云片

- 配置代码如下

```
'auth-captcha' => [
    'enable' => true,
    'provider' => 'yunpian',
    'style' => '', // flat: 直接嵌入 float: 浮动 dialog: 对话框 external: 外置滑动(拖动滑块时才浮现验证图片，仅适用于滑动拼图验证) (不填写默认dialog) PS：flat和external貌似存在回调bug，不推荐使用
    'appid' => '{APPID}',
    'secret' => '{Secret Id}',
    'secret_key' => '{Secret Key}', // 这里多了一个额外参数，请注意！！！
    'ext_config' => [],
],
```

- 访问 [https://www.yunpian.com/console/#/captcha/product](https://www.yunpian.com/console/#/captcha/product)
- 访问 [官方使用文档地址](https://www.yunpian.com/official/document/sms/zh_CN/captcha/captcha_service)

### 使用

在浏览器里打开dcat-admin登陆页

### 未来

- 加入更多滑动验证码（持续添加ing）:heavy_check_mark:
- ~~验证码功能模块化，提供给Laravel项目内使用（该想法实现有点难度，看着办吧）~~

### 升级注意事项

[UPGRADE.md](UPGRADE.md)

### 更新日志

[CHANGE_LOG.md](CHANGE_LOG.md)

### 支持

如果觉得这个项目帮你节约了时间，不妨支持一下呗！

![alipay](https://user-images.githubusercontent.com/6573979/91679916-2c4df500-eb7c-11ea-98a7-ab740ddda77d.png)
![wechat](https://user-images.githubusercontent.com/6573979/91679913-2b1cc800-eb7c-11ea-8915-eb0eced94aee.png)

### License

[The MIT License (MIT)](https://opensource.org/licenses/MIT)