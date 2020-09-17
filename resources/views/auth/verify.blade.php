@extends('layouts.hero')

@section('main')
    <div class="column is-6-tablet is-5-desktop is-4-widescreen">
        <div class="title">{{ __('Verify Your Email Address') }}</div>
        <div class="box tile is-parent is-vertical">
            @if (session('resent'))
                <div class="tile is-child notification is-success">
                    <button class="delete"></button>
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif
            <div class="tile is-child">
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit"
                            class="button is-primary">{{ __('click here to request another') }}</button>
                    .
                </form>
            </div>
            <p> </p>
            <div class="tile is-child notification is-warning">
                <strong>Opmerking: </strong>Sommige e-mail providers, waaronder GMail herkennen deze mail
                onterecht als spam. Daar kunnen wij helaas op dit moment helaas nog niets aan doen.
                Als u nog geen mail heeft ontvangen, controleer dan eerst uw spam-folder.
            </div>
        </div>
    </div>
@endsection
