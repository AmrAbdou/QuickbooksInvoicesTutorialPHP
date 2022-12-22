<?php
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
//Import Facade classes you are going to use here
use QuickBooksOnline\API\Facades\Invoice;

session_start();

function getInvoice($id = null)
{
      // Create SDK instance
   $config = include('config.php');
   $dataService = DataService::Configure(array(
     'auth_mode' => 'oauth2',
     'ClientID' => $config['client_id'],
     'ClientSecret' =>  $config['client_secret'],
     'RedirectURI' => $config['oauth_redirect_uri'],
     'scope' => $config['oauth_scope'],
     'baseUrl' => "development"
  ));

       /*
        * Retrieve the accessToken value from session variable
        */
       $accessToken = $_SESSION['sessionAccessToken'];
       $dataService->throwExceptionOnError(true);
       /*
        * Update the OAuth2Token of the dataService object
        */
       $dataService->updateOAuth2Token($accessToken);

       $invoicesArray = $dataService->Query("select * from invoice");
       //$customerArray = $dataService->Query("select * from invoice where Id='" . $customerName . "'");

       $error = $dataService->getLastError();
       if ($error) {
        var_dump($error);
     } else {
        if (is_array($invoicesArray) && sizeof($invoicesArray) > 0) {
         //var_dump($invoicesArray);
         $result = json_encode($invoicesArray, JSON_PRETTY_PRINT);
         print_r($result . "\n\n\n");
      }      
   }

}

getInvoice();