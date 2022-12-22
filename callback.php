<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

$config = include('config.php');

session_start();

function processCallbackCode()
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

    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

    /*
     * Update the OAuth2Token
    */
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
    $dataService->updateOAuth2Token($accessToken);


    /*
     * Setting the accessToken for session variable
     */
    $_SESSION['sessionAccessToken'] = $accessToken;
}

processCallbackCode();
header('location:http://localhost:3000');

?>