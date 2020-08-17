@component('mail::message')
# Hello {{$name}},

You have received a new order [ID:{{$clientid}}].

@component('mail::button', ['url' => $order_url."/order/".$id])
Go to order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

