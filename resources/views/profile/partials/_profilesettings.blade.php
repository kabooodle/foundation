<div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
    <div class="col-sm-3 clearfix">
        <div class="avatar_container _64 pull-right  avatar-thumbnail" >
            @if($_user->avatar)
                <img src="{{ $_user->avatar }}">
                <input type="hidden" name="avatar" value="{{ $_user->avatar }}">
            @endif
        </div>
    </div>
    <div class="col-sm-6">
            <file-upload
                    outer_class="pull-left"
                    :button_title="avatar ? 'Replace profile image' : 'Add profile image'"
                    multiple="false"
                    user_hash="{{ $_user->public_hash }}"
                    s3_key_url="{{ route('api.files.sign') }}"
            ></file-upload>
            <button
                    @click="removeAvatar"
                    type="button"
                    class="btn white btn-sm pull-left"
                    v-if="avatar">Remove</button>
    </div>
</div>
<div class="form-group row {{ $errors->has('first_name') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">First Name</label>
    <div class="col-sm-6">
        {{ Form::text('first_name', $_user ? $_user->first_name : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has('last_name') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Last Name</label>
    <div class="col-sm-6">
        {{ Form::text('last_name', $_user ? $_user->last_name : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has('username') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Username</label>
    <div class="col-sm-6">
        {{ Form::text('username', $_user ? $_user->username : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has('timezone') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Select Timezone</label>
    <div class="col-sm-6">
        {{ Form::select('timezone', $_timezone, $_user->timezone, ['class' => 'form-control']) }}
    </div>
</div>
<div class="form-group row {{ $errors->has('password') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Current Password</label>
    <div class="col-sm-6">
        {{ Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) }}
    </div>
</div>
<div class="form-group row {{ $errors->has('newPassword') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">New Password</label>
    <div class="col-sm-6">
        {{ Form::password('newPassword', array('placeholder' => 'New Password','class' => 'form-control')) }}
    </div>
</div>
<div class="form-group row {{ $errors->has('newPassword_confirmation') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Confirm New Password</label>
    <div class="col-sm-6">
        {{ Form::password('newPassword_confirmation', array('placeholder' => 'Confirm New Password','class' => 'form-control')) }}
    </div>
</div>

@push('footer-scripts')
<script src="/assets/js/settings.js"></script>
@endpush