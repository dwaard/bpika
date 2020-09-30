@extends('layouts.backendpage')

@section('article')
    <section class="hero  is-medium  is-bold is-primary">
        <div class="hero-body" style="
            background: url('/img/20200430_133650.png') no-repeat center bottom;
            background-size: cover;"
        ></div>
    </section>

    <div class="column is-offset-3-desktop is-6-desktop is-12-tablet">
        <div class="title">{{ __('Update your account profile') }}</div>
        <form method="POST" action="{{ route('account.update') }}" class="box">
            @csrf
            {{method_field('PATCH')}}
            <div class="field">
                <label for="name" class="label">{{ __('Name') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="text" name="name" placeholder="{{ $user->name }}"
                           class="input @error('name') is-danger @enderror"
                           value="{{ $user->name }}" required autocomplete="name" autofocus>
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
            <div class="field is-grouped">
                {{-- Here are the form buttons: save, reset and cancel --}}
                <div class="control">
                    <button type="submit" class="button is-primary">{{ __('Update') }}</button>
                </div>
                <div class="control">
                    <button type="reset" class="button is-warning">{{ __('Reset') }}</button>
                </div>
                <div class="control">
                    <a type="button" href="{{route('home')}}" class="button is-light">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
@endsection
