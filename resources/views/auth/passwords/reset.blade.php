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
</style>
</head>


<div class="wholepage">
<div class="links"><a href="https://mackany.com/">mackany</a> . 
    <a href="https://mackany.com/register">register</a></div>

<div class="formarea">
<center><h3>Reset password</h3></center>
<form style="display:flex;flex-direction:column;" method="POST" action="{{ route('password.update') }}">
@csrf
<input type="hidden" name="token" value="{{ $token }}">
<input id="email" placeholder="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
@error('email')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
<input id="password" placeholder="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
@error('password')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
<input id="password-confirm" placeholder="confirm password" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
<button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>

</form>

<center><a href="/password/reset">forgot password?</a></center>
@if (session('message'))
<center style="color:red;">{{ Session::get('message') }}</center>
@endif

</div>
</div>





