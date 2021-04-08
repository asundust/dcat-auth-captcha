@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  <button type="submit" class="btn btn-primary float-right login-btn g-recaptcha" id="loginButton"
          data-sitekey="{{ $captchaAppid }}"
          data-callback="recaptchaCallback">
    {{ trans('admin.login') }}
  </button>
@endsection
@section('js')
  <script
      src="{{ rtrim($extConfig['initData']['domain'] ?? 'https://recaptcha.net') }}/recaptcha/api.js?render={{ $captchaAppid }}"></script>
  <script>
    function recaptchaCallback(token) {
      $('#token').attr('value', token);
      $('#loginButton').click();
    }

    Dcat.ready(function () {
      grecaptcha.ready(function () {
        // ajax表单提交
        let loginForm = $('#login-form').form({
          validate: true,
          before: function (param) {
            if (!captchaTokenCheck(false)) {
              grecaptcha.execute();
              return false;
            }
          },
        });
      });
    });
  </script>
@endsection