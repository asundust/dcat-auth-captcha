@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <fieldset class="form-label-group form-group position-relative has-icon-left">
    <div
        v5-config="{name: 'token', host: '{{ $extConfig['initData']['host'] }}', token: '{{ $extConfig['token'] ?? '' }}'}"></div>
  </fieldset>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="verify5_token" name="verify5_token" value="{{ $extConfig['token'] ?? '' }}">
  <button type="submit" class="btn btn-primary btn-block btn-flat" id="loginButton">
    {{ trans('admin.login') }}
    <i class="feather icon-arrow-right"></i>
  </button>
@endsection
@section('js')
  <script src="https://s.verify5.com/assets/latest/v5.js" type="text/javascript"></script>
  <script>
    Dcat.ready(function () {
      let v5 = new com.strato.Verify5({
        host: "{{ $extConfig['initData']['host'] }}",
        token: "{{ $extConfig['token'] ?? '' }}"
      });

      // ajax表单提交
      let loginForm = $('#login-form').form({
        validate: true,
        before: function (param) {
          if (!$('input[name=token]').attr('value')) {
            v5.verify(function (result) {
              var success = result.success;
              if (success) {
                $('input[name=token]').attr('value', result.verifyId);
                $('#loginButton').click();
                return true;
              }
            });
            return false;
          }
        },
        success: function () {
          //
        },
        error: function () {
          $('input[name=token]').attr('value', '');
        }
      });
    });
  </script>
@endsection