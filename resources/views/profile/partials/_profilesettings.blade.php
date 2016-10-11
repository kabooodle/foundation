<div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Name</label>
    <div class="col-sm-6">
        {{ Form::text('name', $_user ? $_user->name : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has('email') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Email</label>
    <div class="col-sm-6">
        {{ Form::text('email', $_user ? $_user->email : null, ['class' => 'form-control'])  }}
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
<div class="form-group row {{ $errors->has('timezone') ? 'has-danger' : null }}">
    <label class="control-label col-sm-3">Select Timezone</label>
    <div class="col-sm-6">
        {{ Form::select('timezone', $_timezone, $_user->timezone, ['class' => 'form-control']) }}
    </div>
</div>