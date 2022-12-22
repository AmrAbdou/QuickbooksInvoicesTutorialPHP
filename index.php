<?php
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

$config = include('config.php');

session_start();

// Configure Data Service
$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $config['client_id'],
    'ClientSecret' =>  $config['client_secret'],
    'RedirectURI' => $config['oauth_redirect_uri'],
    'scope' => $config['oauth_scope'],
    'baseUrl' => "development"
));

//set the access token using the auth object
if (isset($_SESSION['sessionAccessToken'])) {

	$accessToken = $_SESSION['sessionAccessToken'];
	$accessTokenJson = array('token_type' => 'bearer',
		'access_token' => $accessToken->getAccessToken(),
		'refresh_token' => $accessToken->getRefreshToken(),
		'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
		'expires_in' => $accessToken->getAccessTokenExpiresAt()
	);
	//$dataService->updateOAuth2Token($accessToken);
	//$oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
	/*$CompanyInfo = $dataService->getCompanyInfo();*/
}
?>

<!DOCTYPE html>
<html>
<head>

</head>
<body>
	<p><a href="connectCompany.php">OAuth 2.0 Login</a></p>
	<p><a href="createInvoice.php">Create Invoice</a></p>
	<p><a href="getInvoices.php">Fetch Invoices</a></p>

	<p><strong>Access Token:</strong></p>
	<code>
		<?php
		$displayString = isset($accessTokenJson) ? $accessTokenJson : "No Access Token Generated Yet";
		echo json_encode($displayString); 
		?>
	</code>
</body>
