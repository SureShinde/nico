<?php
error_reporting("^All");
#ini_set('display_errors', 1);
$host = 'https://www.halocigs.com/';
$consumerKey = '78defeb06d466cd7c7f5c8a73adb252b';
$consumerSecret = 'e2d3bef1a98c48e99fa6428e67777de7';

$callbackUrl = "https://www.halocigs.com/oauth_admin.php";
$temporaryCredentialsRequestUrl = "https://www.halocigs.com/initiate?oauth_callback=".urlencode('https://www.halocigs.com/oauth_admin.php');
$adminAuthorizationUrl = $host . "admin/oauth_authorize";
//$adminAuthorizationUrl = $host . "oauth/authorize";

$accessTokenRequestUrl = $host . "oauth/token";
$apiUrl = $host . "api/rest";
echo "<pre/>";

session_start();

if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) {
    $_SESSION['state'] = 0;
}
try {
    $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
    $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
    $oauthClient->enableDebug();

    if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
        $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
        $_SESSION['secret'] = $requestToken['oauth_token_secret'];
        $_SESSION['state'] = 1;
        header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
        exit;
    } else if ($_SESSION['state'] == 1) {
        $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
        $_SESSION['state'] = 2;
        $_SESSION['token'] = $accessToken['oauth_token'];
        $_SESSION['secret'] = $accessToken['oauth_token_secret'];
        header('Location: ' . $callbackUrl);
        exit;
    } else {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);

        $resourceUrl = "$apiUrl/products";
       // $oauthClient->fetch($resourceUrl, array(), 'GET', array('Content-Type' => 'application/json'));
        $oauthClient->fetch($resourceUrl, array(), 'GET', array('Content-Type' => 'application/xml'));
        $productsList = json_decode($oauthClient->getLastResponse());
        print_r($productsList);
    }
} catch (OAuthException $e) {
    print_r($e->getMessage());
    echo "&lt;br/&gt;";
    print_r($e->lastResponse);
}
