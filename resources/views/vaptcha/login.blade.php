@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <div id="dingxiangContainer"></div>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://v.vaptcha.com/v3.js"></script>
  <script>
    vaptcha(Object.assign({
        vid: '{{ $captchaAppid }}',
        type: 'invisible',
        offline_server: 'v.vaptcha.com'
      }, @json($extConfig)
    )).then(function (vaptchaObj) {
      vaptchaObj.listen('pass', function () {
        $('#token').attr('value', vaptchaObj.getToken());
        $('#loginButton').click();
      });
      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if (!captchaTokenCheck(false)) {
            vaptchaObj.validate();
            return false;
          }
        },
      });
      $('#reset').on('click', function () {
        vaptchaObj.reset();
      });
    });
  </script>
@endsection