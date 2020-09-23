@extends('layouts.hero')

@section('main')
    <div class="column is-6-tablet is-5-desktop is-4-widescreen">
        <div class="title">{{ __('Register') }}</div>
        <form method="POST" action="{{ route('register') }}" class="box">
            @csrf
            <div class="field">
                <label for="email" class="label">{{ __('E-Mail Address') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="email" name="email" class="input is-static @error('email') is-danger @enderror"
                           value="{{  $data['email'] ? $data['email'] : old('email') }}" readonly>
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
                <label for="name" class="label">{{ __('Name') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="name" name="name" placeholder="{{ __('e.g. John Doe') }}"
                           class="input @error('name') is-danger @enderror"
                           value="{{ $data['name'] ? $data['name'] : old('name') }}" required autocomplete="name" autofocus>
                    <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                </span>
                    @error('name')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('name')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="password" class="label">{{ __('Password') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="password" name="password"
                           class="input @error('password') is-danger @enderror"
                           value="{{ old('password') }}" required autocomplete="password" autofocus>
                    <span class="icon is-small is-left">
                    <i class="fas fa-key"></i>
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
                <label for="password_confirmation" class="label">{{ __('Confirm Password') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="password" name="password_confirmation"
                           class="input @error('password_confirmation') is-danger @enderror"
                           value="{{ old('password_confirmation') }}" required autocomplete="password_confirmation">
                    <span class="icon is-small is-left">
                    <i class="fas fa-key"></i>
                </span>
                    @error('password_confirmation')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('password_confirmation')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field is-grouped">
                {{-- Here are the form buttons: save, reset and cancel --}}
                <div class="control">
                    <button type="submit" class="button is-primary">{{ __('Register') }}</button>
                </div>
                <div class="control">
                    <button type="reset" class="button is-warning">{{ __('Reset') }}</button>
                </div>
                <div class="control">
                    <a type="button" href="/" class="button is-light">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
@endsection
