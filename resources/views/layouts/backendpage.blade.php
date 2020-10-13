@extends('layouts.page')

@section('main')
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
@endsection
