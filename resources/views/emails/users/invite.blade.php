@component('mail::message')
# @lang('Account invitation')

@lang('You are receiving this email because we invited you for an account.')

@lang('You have three days to accept the invitation.')

@component('mail::button', ['url' => $url])
{{__('Accept invitation and register your account now')}}
@endcomponent

@lang('Thanks'),<br>
{{ config('app.name') }}
@endcomponent
