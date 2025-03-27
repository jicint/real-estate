@component('mail::message')
# Property Comparison Shared With You

Someone has shared a property comparison with you!

@if($message)
**Message from sender:**
{{ $message }}
@endif

**Comparison Details:**
- Name: {{ $comparison['name'] }}
- Properties: {{ count($comparison['properties']) }} properties
@if(isset($comparison['notes']))
- Notes: {{ $comparison['notes'] }}
@endif

@component('mail::button', ['url' => $shareUrl])
View Comparison
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 