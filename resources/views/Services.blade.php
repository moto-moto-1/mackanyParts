<!-- <!DOCTYPE html> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mackany</title>

        <style>
        
#rootlanding{
    display:flex;
    flex-direction: column;
    justify-content: center;
  align-items: center;
  background: white;
  width:100%;
  /* height:90%; */
  font-size:120%;
  font-weight:bolder;
  /* position:fixed; */
  overflow:auto;

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
    /* height:90%; */
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


        </style>
    </head>
    <body>
    <script
    src="https://www.paypal.com/sdk/js?client-id=AXsubZ_zvqDVCBLVIQYX9g7uTVLyiktZx3nDpNvXoFcxjUeeyngQjmbNisJr1AYXNhZrPbZwoSj-N3a7"> // Required. Replace SB_CLIENT_ID with your sandbox client ID.
  </script>
    <a href="https://mackany.com">Mackany</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/register">register</a>&nbsp;&nbsp;&nbsp;
    <a href="https://mackany.com/login">login</a>&nbsp;&nbsp;&nbsp;
    
    
    @isset($username)
    <a href="https://mackany.com/services">Services</a>&nbsp;&nbsp;&nbsp;
    <a  onclick="logout()" href="/login" >logout</a>&nbsp;&nbsp;&nbsp;
        <spane>Welcome, {{$username}}</spane>
@endisset
        <div id="rootlanding">

       
        <form action="/services" method="post">
        @csrf
        <div id="Newsitetitle"><h2>Choose your extra services here</h2></div>

        @isset($jwt_token)
        <input type="hidden" name="token" value={{$jwt_token}}>
        @endisset

<center>And notify your clients</center>
<center>and yourself by</center>
<center>
<spane>SMS:&nbsp;&nbsp;	&nbsp;</spane>

<select onchange="totalprice()" name="sms" id="sms">
<option  value="sms0price0">none</option>
@foreach ($smsplan as $sms)
  <option value="sms{{$sms['plan']}}price{{$sms['priceUS']}}">{{$sms['plan']}} sms @ {{$sms['priceUS']}}USD</option>
  @endforeach
  <!-- <option value="sms200price150">100 sms @ 150EGP</option>
  <option value="sms400price600">400 sms @ 600EGP</option>
  <option value="sms1000price1500">1000 sms @ 1500EGP</option>
  <option value="sms10000price15000">10000 sms @ 15000EGP</option> -->
</select>&nbsp;&nbsp;<small>(Current SMS available: {{$current_sms}})</small>
<br>
<!-- <center>&</center> -->

<spane>E-mail:&nbsp; </spane>
<select onchange="totalprice()" name="email" id="email">
  <option value="email0price0">none</option>

  @foreach ($emailplan as $email)
  <option value="email{{$email['plan']}}price{{$email['priceUS']}}">{{$email['plan']}} emails @ {{$email['priceUS']}}USD</option>
  @endforeach

<!-- 
  <option value="email1000price40">1000 emails @ 40EGP</option>
  <option value="email4000price160">4000 emails @ 160EGP</option>
  <option value="email10000price400">10000 emails @ 400EGP</option> -->
</select>&nbsp;&nbsp;<small>(Current Email available: {{$current_email}})</small>
<br>
<br>
<center>Put extra images in your site</center>
<spane>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Images: </spane>
<select onchange="totalprice()" name="image" id="image">
  <option value="image0price0">none</option>
  @foreach ($storageplan as $storage)
  <option value="image{{$storage['plan']}}price{{$storage['priceUS']}}">{{$storage['plan']}} images/month @ {{$storage['priceUS']}}USD</option>
  @endforeach
  <!-- <option value="image1000price40">1000 images/month @ 40EGP</option>
  <option value="image4000price160">4000 images/month @ 160EGP</option>
  <option value="image5000price400">10000 images/month @ 400EGP</option> -->
</select>&nbsp;&nbsp;<small>(Current image available: {{$current_image}})</small>
<br>
<br>
<spane>Total price:&nbsp; </spane><spane id="totalPrice">0</spane><spane>USD</spane>

</center>
           
<br>
        <div id="NewsiteInput">
        <!-- <input id="NewsiteInputButton" value="Buy" type="submit"> -->
        <div id="paypal-button-container"></div>
        </div>
        @isset($status)
        <center style="color:red;">name already exists</center>
        @endisset

        <br>

        <!-- <div id="radiobtn"><input type="radio" name="type" value="both">Products and services</input></div><br><br> -->
</div>
  </form>     
    
        
        </div>

        <!-- <div id="rootlandingfirst">
<div class="landfirstcontent">&#9651;<br>Increase customer base</div>
<div class="landfirstcontent">&#9671;<br>Better time to market</div>
<div class="landfirstcontent">&#9675;<br>Automate your business</div>
        </div> -->
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

            var totalamount=0;
            var orderDescription="";
            var smsValue=document.getElementById("sms").value ;
            var emailValue=document.getElementById("email").value;
            var imageValue=document.getElementById("image").value;
            

            var smsPrice=String(smsValue).split("price")[1];
            var emailPrice=String(emailValue).split("price")[1];
            var imagePrice=String(imagePrice).split("price")[1];

            var smsPlan=String(smsValue).split("price")[0];
            var emailPlan=String(emailValue).split("price")[0];
            var imagePlan=String(imagePrice).split("price")[0];

            var smsAmount=String(smsPlan).split("sms")[1];
            var emailAmount=String(emailPlan).split("email")[1];
            var imageAmount=String(imagePlan).split("image")[1];

function totalprice(){
            smsValue=document.getElementById("sms").value ;
            emailValue=document.getElementById("email").value;
            imageValue=document.getElementById("image").value;
            

            smsPrice=String(smsValue).split("price")[1];
            emailPrice=String(emailValue).split("price")[1];
            imagePrice=String(imageValue).split("price")[1];

            smsPlan=String(smsValue).split("price")[0];
            emailPlan=String(emailValue).split("price")[0];
            imagePlan=String(imageValue).split("price")[0];

            smsAmount=String(smsPlan).split("sms")[1];
            emailAmount=String(emailPlan).split("email")[1];
            imageAmount=String(imagePlan).split("image")[1];

  document.getElementById("totalPrice").innerHTML=Number(Number(smsPrice)+Number(emailPrice)+Number(imagePrice));

  totalamount=Number(Number(smsPrice)+Number(emailPrice)+Number(imagePrice));
orderDescription=smsPlan+" & "+emailPlan+" & "+imagePlan;

}
           
            
            // Number(Number(smsPrice)+Number(emailPrice)+Number(imagePrice));

            // var smsPlan=;
            // var emailPlan=;

    paypal.Buttons({
    createOrder: function(data, actions) {
      // This function sets up the details of the transaction, including the amount and line item details.
      return actions.order.create({
        purchase_units: [{

          description: orderDescription,
          amount: {
                    value: totalamount,
                    currency_code: 'USD',
                    breakdown: {
                        item_total: {value: totalamount, currency_code: 'USD'}
                    },
                
                // invoice_id: 'muesli_invoice_id',
                items: [
                  {name:"sms",unit_amount:{value: smsPrice, currency_code: 'USD'},quantity:smsAmount},
                  {name:"email",unit_amount:{value: emailPrice, currency_code: 'USD'},quantity:emailAmount},
                  {name:"image",unit_amount:{value: imagePlan, currency_code: 'USD'},quantity:imageAmount},
                      ]
                }
            }]
      });
    },
    onApprove: function(data, actions) {
    
      return fetch('https://mackany.com/capture-paypal-transaction',{
  method: 'POST', // or 'PUT'
  headers: {
    'Content-Type': 'application/json',
  },
    body: JSON.stringify({
      orderID: data.orderID,
     
      order:JSON.stringify({
        price:totalamount,
        sms:  smsAmount,
        email:emailAmount,
        image:imageAmount,
      }),
      token:'{{$jwt_token}}',
      _token:'{{ csrf_token() }}'
    })
  }).then(function(res) {
    //  console.log(res.data);
     return res.json();
    // alert(res.json())
  }).then(function(details) {
   alert(details);
    // alert('Transaction funds captured from ' + details.payer_given_name);
  }); 
      
      // This function captures the funds from the transaction.
      // return actions.order.capture().then(function(details) {
        // This function shows a transaction success message to your buyer.
        // console.log(details);
        // alert('Transaction completed by ' + details.payer.name.given_name+"and will be reviewed by us");
      // });
    }
  }).render('#paypal-button-container');
    // This function displays Smart Payment Buttons on your web page.
  </script>


    </body>

    <!-- <script src="js/app.js"></script> -->
</html>
