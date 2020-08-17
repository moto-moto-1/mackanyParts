<!-- <!DOCTYPE html> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mackany</title>

        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> -->

        <!-- Styles -->
        <style>
            /* html,  */
            body {
                background-color: #fff;
                /* color: #636b6f; */
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100%;
                width:100%;
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
                /* color: #636b6f; */
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>

<script>
var phoneregex="\\+995\\d{9}|\\+994\\d{9}|\\+977\\d{10}|\\+976\\d{8}|\\+974\\d{8}|\\+973\\d{8}|\\+972\\d{9}|\\+971\\d{9}|\\+970\\d{9}|\\+968\\d{8}|\\+967\\d{9}|\\+966\\d{9}|\\+965\\d{8}|\\+963\\d{9}|\\+960\\d{7}|\\+886\\d{9}|\\+880\\d{10}|\\+855\\d{9}|\\+852\\d{8}|\\+692\\d{7}|\\+691\\d{7}|\\+689\\d{6}|\\+687\\d{6}|\\+686\\d{8}|\\+685\\d{5}|\\+683\\d{4}|\\+682\\d{5}|\\+680\\d{7}|\\+677\\d{7}|\\+672\\d{9}|\\+672\\d{6}|\\+670\\d{8}|\\+596\\d{9}|\\+595\\d{9}|\\+594\\d{9}|\\+507\\d{8}|\\+506\\d{8}|\\+505\\d{8}|\\+504\\d{8}|\\+503\\d{8}|\\+502\\d{8}|\\+501\\d{7}|\\+500\\d{5}|\\+421\\d{9}|\\+420\\d{9}|\\+389\\d{8}|\\+387\\d{8}|\\+385\\d{9}|\\+383\\d{8}|\\+382\\d{8}|\\+381\\d{9}|\\+380\\d{9}|\\+375\\d{9}|\\+374\\d{6}|\\+373\\d{8}|\\+371\\d{8}|\\+370\\d{8}|\\+358\\d{10}|\\+357\\d{8}|\\+355\\d{9}|\\+353\\d{9}|\\+352\\d{9}|\\+351\\d{9}|\\+299\\d{6}|\\+298\\d{5}|\\+297\\d{7}|\\+290\\d{4}|\\+268\\d{8}|\\+262\\d{9}|\\+246\\d{7}|\\+241\\d{7}|\\+235\\d{8}|\\+234\\d{8}|\\+233\\d{9}|\\+231\\d{7}|\\+230\\d{8}|\\+228\\d{8}|\\+227\\d{8}|\\+226\\d{8}|\\+218\\d{10}|\\+216\\d{8}|\\+213\\d{9}|\\+95\\d{8}|\\+94\\d{7}|\\+93\\d{9}|\\+92\\d{10}|\\+91\\d{10}|\\+90\\d{7}|\\+90\\d{11}|\\+86\\d{11}|\\+84\\d{9}|\\+81\\d{11}|\\+66\\d{9}|\\+65\\d{8}|\\+63\\d{10}|\\+62\\d{9}|\\+61\\d{9}|\\+58\\d{7}|\\+57\\d{10}|\\+56\\d{9}|\\+55\\d{11}|\\+52\\d{10}|\\+51\\d{9}|\\+49\\d{10}|\\+48\\d{9}|\\+47\\d{8}|\\+46\\d{7}|\\+45\\d{8}|\\+44\\d{10}|\\+43\\d{10}|\\+41\\d{9}|\\+39\\d{10}|\\+39\\d{10}|\\+36\\d{9}|\\+34\\d{9}|\\+33\\d{9}|\\+32\\d{9}|\\+31\\d{9}|\\+30\\d{10}|\\+27\\d{9}|\\+20\\d{10}|\\+7\\d{10}|\\+1\\d{10}";
</script>
@if (strlen($theinitialstate ?? '')>10)  
<script>
const theinitialstate={!!$theinitialstate!!};
</script>
@endif
<!-- {{$paypalClientid ?? ''}} -->
@if (strlen($paypalClientid ?? '')>10)  
<script
    src="https://www.paypal.com/sdk/js?client-id={{$paypalClientid ?? '' }}">
     // Required. Replace SB_CLIENT_ID with your sandbox client ID.
  </script>
@endif

@if (strlen($checkoutMerchantid ?? '')>8)  
<script>

(function (document, src, libName, config) {
        var script             = document.createElement('script');
        script.src             = src;
        script.async           = true;
        var firstScriptElement = document.getElementsByTagName('script')[0];
        script.onload          = function () {
            for (var namespace in config) {
                if (config.hasOwnProperty(namespace)) {
                    window[libName].setup.setConfig(namespace, config[namespace]);
                }
            }
            window[libName].register();
        };

        firstScriptElement.parentNode.insertBefore(script, firstScriptElement);
    })(document, 'https://secure.avangate.com/checkout/client/twoCoInlineCart.js', 'TwoCoInlineCart',{"app":{"merchant":"{{$checkoutMerchantid ?? '' }}","iframeLoad":"checkout"},"cart":{"host":"https:\/\/secure.2checkout.com","customization":"inline"}});
</script>
@endif



    </head>
    <body>

        <div id="root">
        
        <div style="display:flex;justify-content:center;align-items:center;width:100%;height:100%;">
        <img src="/images/loader.gif" alt="loading">
        <div>Loading...</div>
        </div>
        </div>
    </body>

    <script src="/js/app.js"></script>


</html>
