@extends('layouts.page')

@section('article')
    <div class="column is-6-tablet is-6-desktop is-5-widescreen">
        <form method="POST" action="{{ route('users.store') }}" class="box">
            @csrf
            <div class="field">
                <label for="name" class="label">{{ __('Name') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="name" name="name" placeholder="{{ __('e.g. John Doe') }}"
                           class="input @error('name') is-danger @enderror"
                           value="{{ old('name') }}" required autocomplete="name" autofocus>
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
            <div class="field is-grouped">
                {{-- Here are the form buttons: save, reset and cancel --}}
                <div class="control">
                    <button type="submit" class="button is-primary">{{ __('Send invitation') }}</button>
                </div>
                <div class="control">
                    <button type="reset" class="button is-warning">{{ __('Reset') }}</button>
                </div>
                <div class="control">
                    <a type="button" href="{{ route('users.index') }}" class="button is-light">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
@endsection
