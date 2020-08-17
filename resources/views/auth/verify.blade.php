
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
.wholepage{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
display:flex;
justify-content: center;
  align-items: center;
}
.formarea{
    padding:20px;
    box-shadow:0px 0px 1px gray;
    
}

input{
    margin:10px;

}
.links{
    position:absolute;
    top:10px;
    left:10px;
}
.coloredRed{
    color:red;
}
</style>
</head>


<div class="wholepage">
<div class="links"><a href="https://mackany.com/">mackany</a> . 
    <a href="https://mackany.com/register">register</a></div>

<div class="formarea">
<center><h3>e-mail verification</h3></center>

<center><p>you need to verify your e-mail address in order to proceed</p></center>
<center><p>check your e-mail inbox for confirmation mail, if not found</p></center>
<center><p>your can resend again</p></center>

@if (session('resent'))
    <div class="alert alert-success coloredRed" role="alert">
        {{ __('A fresh verification link has been sent to your email address.') }}
    </div>
@endif

<form style="display:flex;flex-direction:column;" method="POST" action="{{ route('verification.resend') }}">
@csrf
<button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
<input type="hidden" name="source" value="web">

</form>

</div>
</div>
