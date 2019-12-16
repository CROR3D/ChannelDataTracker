@extends('layout')

@section('content')
    <div class="row">
        <div id="login-wrapper" class="col-md-4 text-center mx-auto">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title auth-title text-center">YT Statistics - Login</p>
                </div>
                <div class="panel-body">
                    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.login.attempt') }}">
                    <fieldset>
                        <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                            <input id="login-email" class="form-control text-center" placeholder="E-mail" name="email" type="text" value="{{ old('email') }}">
                            {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <div class="form-group  {{ ($errors->has('password')) ? 'has-error' : '' }}">
                            <input id="login-password" class="form-control text-center" placeholder="Password" name="password" type="password" value="">
                            {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="remember" type="checkbox" value="true" {{ old('remember') == 'true' ? 'checked' : ''}}> Remember Me
                            </label>
                        </div>
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input class="btn-custom btn-custom-secondary btn-block my-3" type="submit" value="Login">
                        <a class="btn-custom btn-custom-secondary btn-block text-center my-3" href="{{ route('channels') }}">Continue As Guest</a>
                        <a class="option-links" href="{{ route('auth.password.request.form') }}" type="submit">Forgot your password?</a>
                        <a class="option-links" href="{{ route('auth.register.form') }}" type="submit">Create an account</a>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
