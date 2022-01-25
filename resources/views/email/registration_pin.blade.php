@component('mail::message')
<p>
    Here is your registration pin: {{$code}}
</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent