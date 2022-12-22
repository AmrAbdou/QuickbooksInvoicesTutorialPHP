<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
//Import Facade classes you are going to use here
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Invoice;

session_start();

function createInvoice()
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
    
    /*
     * Update the OAuth2Token of the dataService object
     */
    $dataService->updateOAuth2Token($accessToken);

    /*
     * 1. Create a Customer
     */
    $customerName = 'John-Doe';
    $customerArray = $dataService->Query("select * from Customer where DisplayName='" . $customerName . "'");
    $error = $dataService->getLastError();
    if ($error) {
        var_dump($error);
    } else {
        if (is_array($customerArray) && sizeof($customerArray) > 0) {
            // Assign Customer Object & Print ID
            $customerRef =  current($customerArray);
            echo "Found Customer with Id={$customerRef->Id}.\n\n";
        } else {
            // Create Customer
            $customerRequestObj = Customer::create([
                "DisplayName" => $customerName
            ]);
            $customerResponseObj = $dataService->Add($customerRequestObj);
            $error = $dataService->getLastError();
            if ($error) {
                logError($error);
            } else {
                echo "Created Customer with Id={$customerResponseObj->Id}.\n\n";
                $customerRef = $customerResponseObj;
            }
        }
    }

    /*
     * 2. Create Invoice using the CustomerRef and ItemRef
     */
    $invoiceObj = Invoice::create([
        "Line" => [
            "Amount" => 1.00,
            "DetailType" => "SalesItemLineDetail",
            "SalesItemLineDetail" => [
                "Qty" => 2,
                "ItemRef" => [
                  "value" => 1,
                  "name" => "My Service"
              ]
          ]
      ],
      "CustomerRef"=> [
        "value"=> $customerRef->Id
    ]
]);
    $resultingInvoiceObj = $dataService->Add($invoiceObj);
    $invoiceId = $resultingInvoiceObj->Id;
    echo "Created invoice Id={$invoiceId}. Reconstructed response body below:\n";
    $result = json_encode($resultingInvoiceObj, JSON_PRETTY_PRINT);
    print_r($result . "\n\n\n");
}


createInvoice();