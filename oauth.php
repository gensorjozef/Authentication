<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
define('MYDIR','../../google-api-php-client--PHP8.0/');
require_once "Classes/helper/Database.php";
require_once(MYDIR."vendor/autoload.php");

$redirect_uri = 'https://wt53.fei.stuba.sk/zad3/oauth.php';

$conn = (new Database())->getConnection();

$client = new Google_Client();
$client->setAuthConfig('../../configs/credentials.json');
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

$service = new Google_Service_Oauth2($client);

if(isset($_GET['code'])){
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token);
  $_SESSION['upload_token'] = $token;

  // redirect back to the example
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    //Get user profile data from google
    $UserProfile = $service->userinfo->get();

    if(!empty($UserProfile)){

        $_SESSION['google']=array();
        $_SESSION['google']['picture'] ='<img src="'.$UserProfile['picture'].'" class="rounded mx-auto d-block rounded-circle">';
        $_SESSION['google']['username'] =  $UserProfile['given_name'].' '.$UserProfile['family_name'];
        $_SESSION['google']['email'] = $UserProfile['email'];
        $_SESSION['google']['locate'] = $UserProfile['locate'];
        $timestamp = date('Y-m-d H:i:s', strtotime('2 hour'));
        $em = explode("@",$UserProfile['email']);
        $name = $em[0];

        $sql_exist = "SELECT * FROM user WHERE user.login=?";
        $stm_exist = $conn->prepare($sql_exist);
        $stm_exist->bindValue(1,$UserProfile['id']);
        $stm_exist->execute();
        $result = $stm_exist->fetch(PDO::FETCH_ASSOC);



        $id = null;


        if (empty($result)) {

            $sql_user = "INSERT INTO user (login, password, user.name, surname, email) VALUES (?,?,?,?,?)";
            $stm = $conn->prepare($sql_user);
            $hash = password_hash("google", PASSWORD_DEFAULT);
            $stm->execute([$UserProfile['id'], $hash,$UserProfile['given_name'] ,$UserProfile['family_name'] , $UserProfile['email']]);
            $id = $conn->lastInsertId();
        }

        if ($id == null){
            $id = $result['id'];
        }

        $_SESSION['google']['id'] = $id;
        $sql = "INSERT INTO logs (login_id, logdate, logs.type) VALUES (?,?,?)";
        $stm = $conn->prepare($sql);
        $stm->bindValue(1,$id);
        $stm->bindValue(2,$timestamp);
        $stm->bindValue(3,"google");
        $stm->execute();

        header("location: index.php");
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
} else {
    $authUrl = $client->createAuthUrl();

    header("location: " . filter_var($authUrl, FILTER_SANITIZE_URL));

}
function Googlehash($s){
    $len = strlen($s);
    $i = 0;
    $result = 0;
    for($i = 0; $i < $len-1; $i=$i+2) {
        $result ^= ((256*$s[$i])+$s[$i+1]);
    }
    if($s[$i]!=0) {
        $result ^= (256*$s[$i]);
    }
    return $result;
}
?>



