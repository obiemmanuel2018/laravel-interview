@component('mail::message')
# {{ $details['title'] }}

{{ $details['body'] }}

@component('mail::button', ['url' => $url])
Register Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent