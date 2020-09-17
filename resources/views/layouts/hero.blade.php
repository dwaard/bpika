@extends('layouts.app')

@section('content')
    <div class="hero is-primary is-bold is-fullheight">
        <!-- Hero head: will stick at the top -->
        <div class="hero-head">
            <nav class="navbar">
                <div class="container">
                    <div class="navbar-brand">
                        <a href="{{ url('dashboard') }}" class="navbar-item">
                            <strong>{{ config('app.name', 'BPiKA') }}</strong>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Hero content: will be in the middle -->
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-6-tablet is-5-desktop is-4-widescreen">
                        @include('layouts.notifications')
                    </div>
                </div>
                <div class="columns is-centered">
                    @yield('main')
                </div>
            </div>
        </div>

        <!-- Hero footer: will stick at the bottom -->
        <div class="hero-foot">
            <div class="container">
                <div class="content is-small has-text-centered">
                    <div class="copyright">Copyright &copy; Burger Participatie in Klimaat Adaptatie (BPIKA) 2020</div>
                </div>
            </div>
        </div>
    </div>
@endsection

