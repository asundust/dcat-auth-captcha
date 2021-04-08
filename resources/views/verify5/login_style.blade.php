@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('content')
  <div
      v5-config="{name: 'token', host: '{{ config('admin.extensions.dcat-auth-captcha.host') }}', token: '{{ $extConfig['token'] ?? '' }}'}"></div>
  @include(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_remember')
  <input type="hidden" id="verify5_token" name="verify5_token" value="{{ $extConfig['token'] ?? '' }}">
  <button type="button" class="btn btn-primary btn-block btn-flat" id="loginButton">
    {{ trans('admin.login') }}
  </button>
@endsection
@section('js')
  <script src="https://s.verify5.com/assets/latest/v5.js" type="text/javascript"></script>
  <script>
    let v5 = new com.strato.Verify5({
      host: "{{ config('admin.extensions.dcat-auth-captcha.host') }}",
      token: "{{ $extConfig['token'] ?? '' }}"
    });

    $('#loginButton').on('click', function () {
      if ($('input[name=token]').attr('value')) {
        $('#loginButton').click();
      } else {
        v5.verify(function (result) {
          var success = result.success;
          if (success) {
            $('input[name=token]').attr('value', result.verifyId);
            $('#loginButton').click();
          }
        });
      }
    });

    $('#login-form').on('keyup', function (event) {
      if (event.keyCode === 13) {
        $('#loginButton').click();
      }
    });
  </script>
@endsection