<?php

namespace App\Http\Controllers;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;



ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

class PayPalClient
{
    /**
     * Returns PayPal HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PayPal APIs, provided the
     * credentials have access.
     */
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
     */
    public static function environment()
    {
      $clientId = getenv("CLIENT_ID") ;
      $clientSecret = getenv("CLIENT_SECRET");
      return new SandboxEnvironment($clientId, $clientSecret);
      
      //   $clientId = getenv("CLIENT_ID") ?: "AWicqWxIxPbW-ZAz8JpkJScDrqSOyxqkZgYIOeUer9AROGXFL0y6-bDELeVapEZnpwYEF2v6r2g-R68n";
      // $clientSecret = getenv("CLIENT_SECRET") ?: "EJHFjtilGxJaOPpwGXGYtRexIcm9FJAZVOhZslM6aDCFg0u7lTzF9DSLqJ2ZjpplX1bewpX3zdTaZT01";
      //   return new ProductionEnvironment($clientId, $clientSecret);
    }
}