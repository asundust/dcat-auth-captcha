@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <fieldset class="form-label-group form-group position-relative has-icon-left">
    <div id="yunpianContainer"></div>
  </fieldset>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="token" name="token" value="">
  <input type="hidden" id="authenticate" name="authenticate" value="">
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_button')
@endsection
@section('js')
  <script src="https://www.yunpian.com/static/official/js/libs/riddler-sdk-0.2.2.js"></script>
  <script>
    let messagesFail = '{{ Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::trans('dcat-auth-captcha.messages.fail') }}';

    window.onload = function () {
      // 初始化
      new YpRiddler(Object.assign({
        mode: '{{ $captchaStyle }}',
        winWidth: 300,
        container: $('#yunpianContainer'),
        appId: '{{ $captchaAppid }}',
        version: 'v1',
        onError: function (param) {
          console.error(param);
          toastr.error(messagesFail);
        },
        onSuccess: function (validInfo, close, useDefaultSuccess) {
          $('#token').attr('value', validInfo.token);
          $('#authenticate').attr('value', validInfo.authenticate);
          useDefaultSuccess(true);
          close();
        },
        onFail: function (code, msg, retry) {
          toastr.error(messagesFail);
          retry();
        }
      }, @json($extConfig)));

      // ajax表单提交
      $('#login-form').form({
        validate: true,
        before: function (param) {
          return captchaTokenCheck(true);
        }
      });
    };
  </script>
@endsection