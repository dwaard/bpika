@extends('layouts.backendpage')

@section('article')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('content.dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('content.logged_in') }}!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
