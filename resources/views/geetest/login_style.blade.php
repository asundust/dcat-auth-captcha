@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <fieldset class="form-label-group form-group position-relative has-icon-left">
    <div id="geetestContainer" style="max-width: 300px"></div>
  </fieldset>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script>
    Dcat.ready(function () {
      initGeetest(Object.assign({
          width: '300px',
          next_width: '300px',
          gt: '{{ $extConfig['initData']['gt'] }}',
          challenge: '{{ $extConfig['initData']['challenge'] }}',
          new_captcha: {{ $extConfig['initData']['new_captcha'] }},
          product: '{{ $captchaStyle }}',
          offline: !{{ $extConfig['initData']['success'] }}
        }, @json(\Illuminate\Support\Arr::except($extConfig, ['initData']))
      ), function (captchaObj) {
        captchaObj.appendTo("#geetestContainer");
        captchaObj.onReady(function () {
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
              $('#token').attr('value', '');
            }
          });
        }).onSuccess(function () {
          $('#token').attr('value', 1);
          captchaObj.bindForm('#login-form');
        }).onError(function () {
          $('#token').attr('value', '');
        })
      });
    });
  </script>
@endsection