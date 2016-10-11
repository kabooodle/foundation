<div class="form-group row {{ $errors->has($_key.'.company') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">Company Name<small class="block text-muted">(Optional)</small></label>
    <div class="col-sm-6">
        {{ Form::text($_key.'[company]', $_from ? $_from->company : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.street1') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">Street 1</label>
    <div class="col-sm-6">
        {{ Form::text($_key.'[street1]', $_from ? $_from->street1 : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.street2') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">Street 2<small class="block text-muted">(Optional)</small></label>
    <div class="col-sm-6">
        {{ Form::text($_key.'[street2]', $_from ? $_from->street2 : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.city') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">City</label>
    <div class="col-sm-6">
        {{ Form::text($_key.'[city]', $_from ? $_from->city : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.state') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">State</label>
    <div class="col-sm-3">
        {{ Form::select($_key.'[state]', getStateAbbrevs(), $_from ? $_from->state : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.zip') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">Zip</label>
    <div class="col-sm-3">
        {{ Form::text($_key.'[zip]', $_from ? $_from->zip : null, ['class' => 'form-control'])  }}
    </div>
</div>
<div class="form-group row {{ $errors->has($_key.'.phone') ? 'has-danger' : null }}">
    <label class="form-control-label col-sm-3">Phone <small class="block text-muted">(Optional)</small></label>
    <div class="col-md-4">
        {{ Form::text($_key.'[phone]', $_from ? $_from->phone : null, ['class' => 'form-control']) }}
    </div>
</div>
<?php $_key = $_from = null ?>