@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script>
    Dcat.ready(function () {
      initGeetest(Object.assign({
          width: '300px',
          gt: '{{ $extConfig['initData']['gt'] }}',
          challenge: '{{ $extConfig['initData']['challenge'] }}',
          new_captcha: {{ $extConfig['initData']['new_captcha'] }},
          product: 'bind',
          offline: !{{ $extConfig['initData']['success'] }}
        }, @json(\Illuminate\Support\Arr::except($extConfig, ['initData']))
      ), function (captchaObj) {
        captchaObj.onReady(function () {
          // ajax表单提交
          let loginForm = $('#login-form').form({
            validate: true,
            before: function (param) {
              if (!captchaTokenCheck(false)) {
                captchaObj.verify();
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
        }).onSuccess(function () {
          captchaObj.bindForm('#login-form');
          $('#token').attr('value', 1);
          $('#loginButton').click();
        }).onError(function () {
          $('#token').attr('value', '');
        })
      });
    });
  </script>
@endsection