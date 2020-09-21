@extends('layouts.hero')

@section('main')
    <div class="column is-5-tablet is-4-desktop is-3-widescreen">

        <div class="title">{{ config('app.name', 'BPiKA') }}</div>
        <form method="POST" action="{{ route('login') }}" class="box">
            @csrf
            <div class="field">
                <label for="email" class="label">{{ __('E-Mail Address') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="email" name="email" placeholder="{{ __('e.g. bobsmith@gmail.com') }}"
                           class="input @error('email') is-danger @enderror"
                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <span class="icon is-small is-left">
                    <i class="fas fa-envelope"></i>
                </span>
                    @error('email')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('email')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password" class="label">{{ __('Password') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input id="password" type="password"
                           class="input @error('password') is-danger @enderror"
                           name="password" required autocomplete="current-password"/>
                    <span class="icon is-small is-left">
                    <i class="fa fa-lock"></i>
                </span>
                    @error('password')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('password')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label class="checkbox" for="remember">
                    <input type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    {{ __('Remember Me') }}
                </label>
            </div>
            <div class="field">
                <button class="button is-success">
                    {{ __('Login') }}
                </button>
            </div>
            <div class="field">
                @if (Route::has('password.request'))
                    <a class="btn btn-link level-right" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection

