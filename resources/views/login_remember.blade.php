<div class="form-group d-flex justify-content-between align-items-center">
  <div class="text-left">
    @if(config('admin.auth.remember'))
      <fieldset class="checkbox">
        <div class="vs-checkbox-con vs-checkbox-primary">
          <input id="remember" name="remember" value="1"
                 type="checkbox" {{ old('remember') ? 'checked' : '' }}>
          <span class="vs-checkbox">
            <span class="vs-checkbox--check">
              <i class="vs-icon feather icon-check"></i>
            </span>
          </span>
          <span> {{ trans('admin.remember_me') }}</span>
        </div>
      </fieldset>
    @endif
  </div>
</div>