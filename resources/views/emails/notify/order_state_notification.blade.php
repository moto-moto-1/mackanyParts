@component('mail::message')
# Hello {{$name}},

Your order [ID:{{$clientid}}] is now ready.

@component('mail::button', ['url' => $order_url."/order/".$id])
Go to order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

