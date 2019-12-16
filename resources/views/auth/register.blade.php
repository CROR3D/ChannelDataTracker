@extends('layout')

@section('content')
    <div class="row">
        <div id="register-wrapper" class="col-md-4 col-md-offset-4 text-center">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title auth-title text-center">Register An Account</p>
                </div>
                <div class="panel-body">
                    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.register.attempt') }}">
                    <fieldset>
                        <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                            <input class="form-control text-center" placeholder="E-mail" name="email" type="text" value="{{ old('email') }}">
                            {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <div class="form-group {{ ($errors->has('password')) ? 'has-error' : '' }}">
                            <input class="form-control text-center" placeholder="Password" name="password" type="password">
                            {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <div class="form-group {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
                            <input class="form-control text-center" placeholder="Confirm Password" name="password_confirmation" type="password">
                            {!! ($errors->has('password_confirmation') ? $errors->first('password_confirmation', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input class="btn-custom btn-custom-secondary btn-block my-4" type="submit" value="Sign Me Up!">
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
