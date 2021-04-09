@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <fieldset class="form-label-group form-group position-relative has-icon-left">
    <div class="h-captcha" data-sitekey="{{ $captchaAppid }}"
         data-callback="hCaptchaCallback" style="text-align: center;width: 300px"></div>
  </fieldset>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://hcaptcha.com/1/api.js" async defer></script>
  <script>
    function hCaptchaCallback(token) {
      $('#token').attr('value', token);
    }

    Dcat.ready(function () {
      // ajax表单提交
      $('#login-form').form({
        validate: true,
        before: function (param) {
          return captchaTokenCheck(true);
        },
        success: function () {
          //
        },
        error: function () {
          hcaptcha.reset()
          $('#token').attr('value', '');
        }
      });
    });
  </script>
@endsection