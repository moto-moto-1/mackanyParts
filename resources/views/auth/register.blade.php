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

<body>
    <a  href="https://mackany.com">Mackany</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/register">register</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/login">login</a>&nbsp;&nbsp;&nbsp;


<div class="wholepage">

  
<div class="formarea">
<center><h3>Register</h3></center>
<form style="display:flex;flex-direction:column;" method="POST" action="register">
@csrf
<input type="text" name="name" placeholder="name" pattern="[a-zA-Z ]{5,25}"
title="Name should be more than 3 characters"
 required/>
<input type="email"  name="email" placeholder="email" required/>
<input type="password" name="password" placeholder="password"
title="password should be more than 8"
 pattern=".{8,25}" required/>
<input type="password" name="password_confirmation" placeholder="confirm password" required/>

<input type="tel" id="phone" name="telephone" placeholder="telephone +10123478965" 
title="Telephone should be in the international format e.g +10123478965"
pattern="\+995\d{9}|\+994\d{9}|\+977\d{10}|\+976\d{8}|\+974\d{8}|\+973\d{8}|\+972\d{9}|\+971\d{9}|\+970\d{9}|\+968\d{8}|\+967\d{9}|\+966\d{9}|\+965\d{8}|\+963\d{9}|\+960\d{7}|\+886\d{9}|\+880\d{10}|\+855\d{9}|\+852\d{8}|\+692\d{7}|\+691\d{7}|\+689\d{6}|\+687\d{6}|\+686\d{8}|\+685\d{5}|\+683\d{4}|\+682\d{5}|\+680\d{7}|\+677\d{7}|\+672\d{9}|\+672\d{6}|\+670\d{8}|\+596\d{9}|\+595\d{9}|\+594\d{9}|\+507\d{8}|\+506\d{8}|\+505\d{8}|\+504\d{8}|\+503\d{8}|\+502\d{8}|\+501\d{7}|\+500\d{5}|\+421\d{9}|\+420\d{9}|\+389\d{8}|\+387\d{8}|\+385\d{9}|\+383\d{8}|\+382\d{8}|\+381\d{9}|\+380\d{9}|\+375\d{9}|\+374\d{6}|\+373\d{8}|\+371\d{8}|\+370\d{8}|\+358\d{10}|\+357\d{8}|\+355\d{9}|\+353\d{9}|\+352\d{9}|\+351\d{9}|\+299\d{6}|\+298\d{5}|\+297\d{7}|\+290\d{4}|\+268\d{8}|\+262\d{9}|\+246\d{7}|\+241\d{7}|\+235\d{8}|\+234\d{8}|\+233\d{9}|\+231\d{7}|\+230\d{8}|\+228\d{8}|\+227\d{8}|\+226\d{8}|\+218\d{10}|\+216\d{8}|\+213\d{9}|\+95\d{8}|\+94\d{7}|\+93\d{9}|\+92\d{10}|\+91\d{10}|\+90\d{7}|\+90\d{11}|\+86\d{11}|\+84\d{9}|\+81\d{11}|\+66\d{9}|\+65\d{8}|\+63\d{10}|\+62\d{9}|\+61\d{9}|\+58\d{7}|\+57\d{10}|\+56\d{9}|\+55\d{11}|\+52\d{10}|\+51\d{9}|\+49\d{10}|\+48\d{9}|\+47\d{8}|\+46\d{7}|\+45\d{8}|\+44\d{10}|\+43\d{10}|\+41\d{9}|\+39\d{10}|\+39\d{10}|\+36\d{9}|\+34\d{9}|\+33\d{9}|\+32\d{9}|\+31\d{9}|\+30\d{10}|\+27\d{9}|\+20\d{10}|\+7\d{10}|\+1\d{10}"
 required>

<input type="text" name="address" pattern=".{5,40}" placeholder="address" required/>
<input type="hidden" name="source" value="web">
<input type="submit" value="Submit">

</form>

</div>
</div>
</body>