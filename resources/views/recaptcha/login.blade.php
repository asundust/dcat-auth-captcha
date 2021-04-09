@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script
      src="{{ rtrim($extConfig['initData']['domain'] ?? 'https://recaptcha.net') }}/recaptcha/api.js?render={{ $captchaAppid }}"></script>
  <script>
    Dcat.ready(function () {
      grecaptcha.ready(function () {
        // ajax表单提交
        let loginForm = $('#login-form').form({
          validate: true,
          before: function (param) {
            if (!captchaTokenCheck(false)) {
              grecaptcha.execute('{{ $captchaAppid }}', {action: 'login'}).then(function (token) {
                $('#token').attr('value', token);
                $('#loginButton').click();
              });
              return false;
            }
          },
          success: function () {
            //
          },
          error: function () {
            $('#token').attr('value', '');
          }
        });
      });
    });
  </script>
@endsection