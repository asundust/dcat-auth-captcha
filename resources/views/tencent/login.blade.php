@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="ticket" name="ticket" value="">
  <input type="hidden" id="randstr" name="randstr" value="">
  <div id="TencentCaptcha"></div>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
  <script>
    Dcat.ready(function () {
      let captcha = new TencentCaptcha('{{ $captchaAppid }}', function (res) {
        if (res.ret === 0) {
          $('#ticket').attr('value', res.ticket);
          $('#randstr').attr('value', res.randstr);
          $('#loginButton').click();
        }
      });

      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if ($('#ticket').attr('value').length === 0 || $('#randstr').attr('value').length === 0) {
            captcha.show();
            return false;
          }
          return true;
        },
        success: function () {
          //
        },
        error: function () {
          captcha.destroy();
          $('#ticket').attr('value', '');
          $('#randstr').attr('value', '');
        }
      });
    });
  </script>
@endsection