@extends(Asundust\DcatAuthCaptcha\DcatAuthCaptchaServiceProvider::instance()->getName().'::login_base')
@section('css')
  <style>
    #vaptchaContainer {
      width: 300px;
      height: {{ ($captchaStyle ?? 'click') == 'click' ? '36px' : '184px' }};
    }

    .vaptcha-init-main {
      display: table;
      width: 100%;
      height: 100%;
      background-color: #eeeeee;
    }

    .vaptcha-init-loading {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
    }

    .vaptcha-init-loading > a {
      display: inline-block;
      width: 18px;
      height: 18px;
      border: none;
    }

    .vaptcha-init-loading > a img {
      vertical-align: middle;
    }

    .vaptcha-init-loading .vaptcha-text {
      font-family: sans-serif;
      font-size: 12px;
      color: #cccccc;
      vertical-align: middle;
    }
  </style>
@endsection
@section('content')
  <div id="vaptchaContainer" style="width: 300px;height: 36px;">
    <div class="vaptcha-init-main">
      <div class="vaptcha-init-loading">
        <a href="/" target="_blank">
          <img src="https://r.vaptcha.net/public/img/vaptcha-loading.gif"/>
        </a>
        <span class="vaptcha-text">VAPTCHA启动中...</span>
      </div>
    </div>
  </div>
@endsection
@section('js')
  <script src="https://v.vaptcha.com/v3.js"></script>
  <script>
    vaptcha(Object.assign({
        vid: '{{ $captchaAppid }}',
        type: '{{ $captchaStyle }}',
        container: '#vaptchaContainer',
        offline_server: 'v.vaptcha.com'
      }, @json($extConfig)
    )).then(function (vaptchaObj) {
      vaptchaObj.render();
      vaptchaObj.listen('pass', function () {
        $('#token').attr('value', vaptchaObj.getToken());
      });
      // ajax表单提交
      $('#login-form').form({
        validate: true,
        before: function (param) {
          return captchaTokenCheck(true);
        }
      });
      // 重置
      $('#reset').on('click', function () {
        vaptchaObj.reset();
      });
    });
  </script>
@endsection