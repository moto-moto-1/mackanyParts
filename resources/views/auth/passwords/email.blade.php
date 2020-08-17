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
@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif
<form style="display:flex;flex-direction:column;" method="POST" action="{{ route('password.email') }}">
@csrf
<input id="email" type="email" placeholder="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
@error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

<input type="hidden" name="source" value="web">
<button type="submit" class="btn btn-primary">{{ __('Send Reset Link') }}</button>
</form>

@if (session('message'))
<center style="color:red;">{{ Session::get('message') }}</center>
@endif

</div>
</div>



<!-- 

@if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form> -->