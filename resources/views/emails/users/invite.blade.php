@component('mail::message')
# Introduction

You received this mail because you are invited for an account on {{ config('app.name') }}.

You have three days to accept the invitation.

@component('mail::button', ['url' => $url])
{{__('Accept invitation and register your account now')}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
