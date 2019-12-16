@extends('layout')

@section('content')
    <div class="row">
        <div id="reset-wrapper" class="col-md-4 col-md-offset-4 text-center">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title auth-title text-center">Reset Your Password</p>
                </div>
                <div class="panel-body">
                    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.password.request.attempt') }}">
                    <fieldset>
                        <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                            <input class="form-control text-center" placeholder="E-mail" name="email" type="text" value="{{ old('email') }}">
                            {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                        </div>
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input class="btn-custom btn-custom-secondary btn-block my-3" type="submit" value="Help!">
                        <a class="option-links text-center" href="{{ route('auth.activation.request') }}" type="submit">Resend Account Activation</a>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
