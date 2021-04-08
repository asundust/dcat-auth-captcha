@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('css')
  <style>
    .yidun_intellisense {
      width: 300px;
    }
  </style>
@endsection
@section('content')
  <div id="captchaError" class="form-group has-feedback {!! !$errors->has('captcha') ?: 'has-error' !!}"
       style="margin-bottom: 0;">
    @if($errors->has('captcha'))
      @foreach($errors->get('captcha') as $message)
        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{ $message }}
        </label><br>
      @endforeach
    @endif
  </div>
  <div class="form-group row">
    <div class="col-xs-4" id="captchaContainer"></div>
  </div>

@endsection
@section('js')
  <script src="//cstaticdun.126.net/load.min.js?t={{ now()->format('YmdHi') }}"></script>
  <script>
    let captchaIns = null;
    initNECaptcha(Object.assign({
        captchaId: '{{ $captchaAppid }}',
        element: '#captchaContainer',
        mode: '{{ $captchaStyle }}',
        width: '300px',
        feedbackEnable: false,
        onVerify: function (err, data) {
          if (err) {
            captchaIns.refresh();
            return;
          }
          $('#token').attr('value', data.validate);
        }
      }, @json($extConfig)
      ), function onload(instance) {
        captchaIns = instance;
      },
      function onerror(err) {
        console.log(err);
      },
    );

    $('#loginButton').on('click', function (event) {
      formValidate();
    });

    $('#login-form').on('keyup', function (event) {
      if (event.keyCode === 13) {
        formValidate();
      }
    });
  </script>
@endsection