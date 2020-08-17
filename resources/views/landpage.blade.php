<!-- <!DOCTYPE html> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mackany</title>
        <style>
            /* html, 
            body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100%;
                width:100%
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            } */
#rootlanding{
    display:flex;
    flex-direction: column;
    justify-content: center;
  align-items: center;
  background: white;
  width:100%;
  height:90%;
  font-size:120%;
  font-weight:bolder;
  position:fixed;

}

#Newsitetitle{
display:block;
margin:5px;
text-align: center;
}

#NewsiteInput{
    display:block;
    margin:5px;
    text-align: center;


}

#NewsiteInputField{
    font-size:100%;
    text-align:center;
    width:50%;

}

#rootlandingfirst{
    position:absolute;
    top:90%;
    left:0%;
    width:100%;
    height:90%;
    background:black;
}

#NewsiteInputButton{
    font-size:100%;
    background:black;
    color:white;
    border:1px solid gray;
    border-radius:3px;
    padding:10px;
    cursor:pointer;
    display:inline;
    text-align: center;

    
}

#NewsiteInputButton:hover{
    background:gray;
}

.webTypeTitle{
    
    text-align: center;


}

.radiogroup{
    display:flex;
    flex-flow:row wrap;
}
#radiobtn{
    margin:5px;
}

#rootlandingfirst,.radiogroup{
    display:flex;
    height:100%;
    flex-flow:wrap row;
    justify-content: center;
  align-items: center;
}

.landfirstcontent{
    color:white;
    text-align:center;
    font-size:200%;
    margin:auto;
    word-wrap: break-word;
    width:20%;
    min-width:4cm;
    
}

.legal{
    color:white;
    position:absolute;
    bottom:15px;
}

#hyp{
    color:inherit;
}


        </style>
    </head>
    <body>
    <a href="https://mackany.com">Mackany</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/register">register</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/login">login</a>&nbsp;&nbsp;&nbsp;
    
    @isset($username)
    <a href="https://mackany.com/services">Services</a>&nbsp;&nbsp;&nbsp;
    <a  onclick="logout()" href="/login" >logout</a>&nbsp;&nbsp;&nbsp;
        <spane>Welcome, {{$username}}</spane>
@endisset
        <div id="rootlanding">
        <form action="/sites" method="post">
        @csrf
        <div id="Newsitetitle">Start your web presence here</div>

        @isset($jwt_token)
        <input type="hidden" name="token" value={{$jwt_token}}>
@endisset

            <div id="NewsiteInput">
        <input id="NewsiteInputField" autofocus name="siteurl" type='text' placeholder="enter your site name" > .mackany.com
        </div>
<br>
        <div id="NewsiteInput">
        <input id="NewsiteInputButton" value="Start" type="submit">
        </div>
        @isset($status)
        <center style="color:red;">{{$status}}</center>
        @endisset

        <br>
<div class="webTypeTitle" style="text-align:center">Website language</div>
        
        <div class="radiogroup">
        <div id="radiobtn"><input type="radio" name="type" value="en" checked>English</input></div>
        <div id="radiobtn"><input type="radio" name="type" value="ar">عربى</input></div>
        <!-- <div id="radiobtn"><input type="radio" name="type" value="both">Products and services</input></div><br><br> -->
</div>
  </form>       
        
        </div>

        <div id="rootlandingfirst">
<div class="landfirstcontent">&#9651;<br>Increase customer base</div>
<div class="landfirstcontent">&#9671;<br>Better time to market</div>
<div class="landfirstcontent">&#9675;<br>Automate your business</div>

<div class="legal"><center>
    <a id="hyp" href="privacy">Privacy</a>&nbsp;.&nbsp;
    <a id="hyp" href="terms">Terms</a>&nbsp;.&nbsp;
    <a id="hyp" href="helpEn">Help</a>&nbsp;.&nbsp;
    <a id="hyp" href="helpAr">المساعدة</a>
</center></div>
        </div>
    </body>

    <script>
        function logout() {
  // console.log("clicked");

  var formData = new FormData();
  formData.append('token', '{{$jwt_token}}');
  formData.append('_token', '{{ csrf_token() }}');
  formData.append('source', 'web');
  

  return fetch('https://mackany.com/api/auth/logout',{
  method: 'POST', // or 'PUT'
 
 body:formData

}).then(function(res) {});

}
</script>

    <!-- <script src="js/app.js"></script> -->
</html>
