@extends('layouts.app')

@section('content')
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a href="/" class="navbar-item">
                        <i class="fas fa-hat-wizard"></i>&nbsp;BPIKA
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
                                            <a class="navbar-item" href="{{ route('register') }}">{{ __('Register') }}</a>
                                        </div>
                                    </div>
                                @else
                                    {{-- show only login --}}
                                    <a class="navbar-item" href="{{ route('login') }}">{{ __('Login') }}</a>
                                @endif
                            @else
                                <div class="navbar-item">
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
        <article class="">
            @yield('article')
        </article>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="content is-small has-text-centered">
                <div class="copyright">Copyright &copy; Burger Participatie in Klimaat Adaptatie (BPIKA) 2020</div>
            </div>
        </div>
    </footer>
@endsection
