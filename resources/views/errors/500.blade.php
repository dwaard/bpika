@extends('layouts.hero')

@section('main')
    <div class="content">
        <h1 class="is-large has-text-centered has-text-white">{{ __('error.internalServerError.title') }}</h1>
        <p>{{ __('error.internalServerError.explanation') }}</p>
        <p>
            {{ __('error.internalServerError.advice1.text1') }}
            <a class="has-text-link-dark" href="/dashboard">{{ __('error.internalServerError.advice1.link') }}</a>
            {{ __('error.internalServerError.advice1.text2') }}
        </p>
        <p>
            {{ __('error.internalServerError.advice2.text1') }}
            <a class="has-text-link-dark" href="/contact">{{ __('error.internalServerError.advice2.link') }}</a>
            {{ __('error.internalServerError.advice2.text2') }}
        </p>
    </div>
@endsection
