@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <div id="dingxiangContainer"></div>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js"></script>
  <script>
    Dcat.ready(function () {
      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if (!captchaTokenCheck(false)) {
            captcha.show();
            return false;
          }
        },
        success: function () {
          captcha.hide()
        },
        error: function () {
          captcha.hide()
          $('#token').attr('value', '');
        }
      });

      let captcha = _dx.Captcha(document.getElementById('dingxiangContainer'),
        Object.assign({
            appId: '{{ $captchaAppid }}',
            style: '{{ $captchaStyle }}',
            width: 300,
            success: function (token) {
              $('#token').attr('value', token);
              $('#loginButton').click();
            }
          }, @json($extConfig)
        ));
    });
  </script>
@endsection