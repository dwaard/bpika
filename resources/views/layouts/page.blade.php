@extends('layouts.app')

@section('content')
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a href="/" class="navbar-item">
                        <i class="fas fa-hat-wizard"></i>&nbsp;{{ __('content.navbar_home') }}
                    </a>
                    <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false"
                       data-target="navMenu">
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                    </a>
                </div>
                <div class="navbar-menu" id="navMenu">
                    {{-- Display page title in header --}}
                    <div class="navbar-start">
                        <h1 class="navbar-item has-text-weight-bold has-text-primary">@yield('page_title')</h1>
                        @auth
                            <a class="navbar-item" href="{{ route('stations.index') }}">
                                <i class="fas fa-home"></i>&nbsp;{{ __('Stations') }}
                            </a>
                            <a class="navbar-item" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i>&nbsp;{{ __('Users') }}
                            </a>
                        @endauth
                    </div>
                    <div class="navbar-end">
                        @if(Route::has('login'))
                            @guest
                                @if (Route::has('register'))
                                    {{-- show a dropdown to choose between login and register --}}
                                    <div class="navbar-item has-dropdown is-hoverable">
                                        <a class="navbar-link">{{ __('Login') }}</a>

                                        <div class="navbar-dropdown">
                                            <a class="navbar-item" href="{{ route('login') }}">{{ __('Login') }}</a>
                                            <a class="navbar-item"
                                               href="{{ route('register') }}">{{ __('Register') }}</a>
                                        </div>
                                    </div>
                                @else
                                    {{-- show only login --}}
                                    <a class="navbar-item" href="{{ route('login') }}">{{ __('Login') }}</a>
                                @endif
                            @else
                                <div class="navbar-item">
                                    @php($user = Auth::user())
                                    <div class="navbar-item has-dropdown is-hoverable">
                                        <a class="navbar-link">{{ $user->name }}</a>

                                        <div class="navbar-dropdown">
                                            @if($user->isAdmin)
                                                <a class="navbar-item" href="/admin">
                                                    <span class="icon"><i class="fas fa-toolbox"></i></span>
                                                    <span>{{__('Admin')}}</span>
                                                </a>
                                            @endif
                                            <a class="navbar-item" href="{{route('account.edit')}}">
                                                <span class="icon"><i class="fas fa-user-cog"></i></span>
                                                <span>{{__('Profile')}}</span>
                                            </a>
                                            <hr class="dropdown-divider">
                                            <a class="navbar-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                                                <span>{{__('Logout')}}</span>
                                            </a>
                                            <form id="frm-logout" action="{{ route('logout') }}" method="POST"
                                                  style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endguest
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="">
            <article class="container">
                <section class="section">
                    <div class="columns">

                        <div class="column is-offset-2-widescreen is-8-widescreen
                                           is-offset-1-desktop is-10-desktop
                                           is-12-tablet">
                            <aside class="mb-2">
                                @include('layouts.notifications')
                            </aside>

                            <section class="content">@yield('article')</section>
                        </div>
                    </div>
                </section>
            </article>
        </div>
    </main>
    <footer class="footer columns is-vcentered">
            <div class="column columns is-vcentered">
                <div class="column">
                    <img class="img-fluid" src="{{ asset('img/HUISSTIJL_HZ_LOGO_960x593.jpg/') }}" alt="">
                </div>
                <div class="column">
                    <img class="img-fluid" src="{{ asset('img/Hanzehogeschool.png/') }}" alt="">
                </div>
            </div>
            <div class="column">
                <div class="content is-small has-text-centered">
                    <div class="copyright">{{ __('content.copyright') }}</div>
                </div>
            </div>
            <div class="column columns is-vcentered">
                <div class="column">
                    <img class="img-fluid" src="{{ asset('img/logo-hogeschool-rotterdam.png/') }}" alt="">
                </div>
                <div class="column">
                    <img class="img-fluid" src="{{ asset('img/vhl_logo_kleur_rgb_voetje.jpg/') }}" alt="">
                </div>
            </div>
    </footer>
@endsection
