@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  <button type="submit" class="btn btn-primary float-right login-btn h-captcha" id="loginButton"
          data-sitekey="{{ $captchaAppid }}"
          data-callback="hCaptchaCallback">
    {{ __('admin.login') }}
    <i class="feather icon-arrow-right"></i>
  </button>
@endsection
@section('js')
  <script src="https://hcaptcha.com/1/api.js" async defer></script>
  <script>
    function hCaptchaCallback(token) {
      $('#token').attr('value', token);
      $('#loginButton').click();
    }

    Dcat.ready(function () {
      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if (!captchaTokenCheck(false)) {
            hcaptcha.execute();
            return false;
          }
        },
        success: function () {
          //
        },
        error: function () {
          hcaptcha.reset();
          $('#token').attr('value', '');
        }
      });
    });
  </script>
@endsection