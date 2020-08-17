@component('mail::message')
# Hello {{$name}},

You have a new reservation [ID:{{$id}}].

@component('mail::button', ['url' => $order_url."/reservation/".$id])
Go to reservation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

