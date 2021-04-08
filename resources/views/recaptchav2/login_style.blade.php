@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <div class="g-recaptcha" data-sitekey="{{ $captchaAppid }}"
       data-callback="recaptchaCallback" style="text-align: center;margin-bottom: 11px; width: 300px !important"></div>
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="{{ rtrim($extConfig['initData']['domain'] ?? 'https://recaptcha.net') }}/recaptcha/api.js"
          async defer></script>
  <script>
    function recaptchaCallback(token) {
      $('#token').attr('value', token);
    }

    // ajax表单提交
    let loginForm = $('#login-form').form({
      validate: true,
      before: function (param) {
        return captchaTokenCheck(true);
      },
    });
  </script>
@endsection