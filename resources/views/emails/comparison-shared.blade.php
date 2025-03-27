@component('mail::message')
# Property Comparison Shared With You

Someone has shared a property comparison with you!

@if($message)
Message:
{{ $message }}
@endif

The comparison includes {{ count($comparison['properties']) }} properties.

@component('mail::button', ['url' => $shareUrl])
View Comparison
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 