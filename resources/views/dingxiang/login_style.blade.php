@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <fieldset class="form-label-group form-group position-relative has-icon-left">
    <div id="dingxiangContainer" style="max-width: 300px"></div>
  </fieldset>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js"></script>
  <script>
    Dcat.ready(function () {
      // ajax表单提交
      $('#login-form').form({
        validate: true,
        before: function (param) {
          return captchaTokenCheck(true);
        }
      });

      let captcha = _dx.Captcha(document.getElementById('dingxiangContainer'),
        Object.assign({
            appId: '{{ $captchaAppid }}',
            style: '{{ $captchaStyle }}',
            width: 300,
            success: function (token) {
              $('#token').attr('value', token);
            }
          }, @json($extConfig)
        ));
    });
  </script>
@endsection