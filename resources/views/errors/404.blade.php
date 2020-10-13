@extends('layouts.hero')

@section('main')
    <div class="content">
        <h1 class="is-large has-text-centered has-text-white">{{ __('error.notFound.title') }}</h1>
        <p>{{ __('error.notFound.explanation') }}</p>
        <p>
            {{ __('error.notFound.advice1.text1') }}
            <a class="has-text-link-dark" href="/dashboard">{{ __('error.notFound.advice1.link') }}</a>
            {{ __('error.notFound.advice1.text2') }}
        </p>
        <p>
            {{ __('error.notFound.advice2.text1') }}
            <a class="has-text-link-dark" href="/contact">{{ __('error.notFound.advice2.link') }}</a>
            {{ __('error.notFound.advice2.text2') }}
        </p>
    </div>
@endsection
