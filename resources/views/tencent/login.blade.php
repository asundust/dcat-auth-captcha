@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="ticket" name="ticket" value="">
  <input type="hidden" id="randstr" name="randstr" value="">
  <button type="submit"
          class="btn btn-primary float-right login-btn"
          id="TencentCaptcha"
          data-appid="{{ $captchaAppid }}"
          data-cbfn="tencentCallback"
  >{{ trans('admin.login') }}</button>
@endsection
@section('js')
  <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
  <script>
    window.tencentCallback = function (res) {
      if (res.ret === 0) {
        $('#ticket').attr('value', res.ticket);
        $('#randstr').attr('value', res.randstr);
        $('#loginButton').click();
      }
    };

    Dcat.ready(function () {
      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if ($('#ticket').attr('value').length === 0 || $('#randstr').attr('value').length === 0) {
            return false;
          }
          return true;
        },
      });
    });
  </script>
@endsection