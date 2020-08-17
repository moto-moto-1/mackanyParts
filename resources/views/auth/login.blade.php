<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
.wholepage{
    position:relative;
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

<a href="https://mackany.com">Mackany</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/register">register</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/login">login</a>&nbsp;&nbsp;&nbsp;

<div class="wholepage">


<div class="formarea">
<center><h3>Login</h3></center>
<form style="display:flex;flex-direction:column;" method="POST" action="/api/auth/login">
@csrf
<input type="email"  name="email" placeholder="email"/>
@if ($errors->has('email'))
    <span class="invalid-feedback">
        <strong>{{ $errors->first('email') }}</strong>
    </span>
@endif
<input type="password" name="password" placeholder="password"/>
@if ($errors->has('password'))
    <span class="invalid-feedback">
        <strong>{{ $errors->first('password') }}</strong>
    </span>
@endif
<input type="hidden" name="source" value="web">
<input type="submit" value="Submit">

</form>

<center><a href="/password/reset">forgot password?</a></center>
@if (session('message'))
<center style="color:red;">{{ Session::get('message') }}</center>
@endif

</div>
</div>
