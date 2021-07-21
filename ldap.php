<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "Classes/helper/Database.php";
session_start();
$conn = (new Database())->getConnection();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// using ldap bind
    $ldapuid = $_REQUEST['login'];
    $ldappass = $_REQUEST['password'];

    $dn = 'ou=People, DC=stuba, DC=sk';
    $ldaprdn = "uid=$ldapuid, $dn";


// connect to ldap server
    $ldapconn = ldap_connect("ldap.stuba.sk")
    or die("Could not connect to LDAP server.");

    if ($ldapconn) {
        $set = ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        // binding to ldap server
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

        // verify binding
        if ($ldapbind) {
            $results=ldap_search($ldapconn,$dn,"uid=" ."*". $ldapuid ."*",array("givenname","employeetype","surname","mail","faculty","cn","uisid","uid"),0,5);
            $info=ldap_get_entries($ldapconn,$results);
            $timestamp = date('Y-m-d H:i:s', strtotime('2 hour'));
            $_SESSION['ldap']=array();

            $_SESSION['ldap']['username'] = $info[0]['givenname'][0] . ' ' . $info[0]['sn'][0];
            $_SESSION['ldap']['mail'] = $info[0]['mail'][0];




            $sql_exist = "SELECT * FROM user WHERE user.login=?";
            $stm_exist = $conn->prepare($sql_exist);
            $stm_exist->bindValue(1,$ldapuid);
            $stm_exist->execute();
            $result = $stm_exist->fetch(PDO::FETCH_ASSOC);
            $id = null;

            if ($result==0) {

                $sql_user = "INSERT INTO user (login, password, user.name, surname, email) VALUES (?,?,?,?,?)";
                $stm = $conn->prepare($sql_user);
                $hash = password_hash($ldappass, PASSWORD_DEFAULT);
                $stm->execute([$ldapuid, $hash, $info[0]['givenname'][0], $info[0]['sn'][0], $info[0]['mail'][0]]);
                $id = $conn->lastInsertId();

            }else{

            }

            if ($id == null){
                $id = $result['id'];
            }
            $_SESSION['ldap']['id'] = $id;
            $sql = "INSERT INTO logs (login_id, logdate, logs.type) VALUES (?,?,?)";
            $stm = $conn->prepare($sql);
            $stm->bindValue(1,$id);
            $stm->bindValue(2,$timestamp);
            $stm->bindValue(3,"ldap");
            $stm->execute();

            ldap_unbind($ldapconn);
            header("location: index.php");

        } else {
            echo "<script type='text/javascript'>alert('LDAP bind failed...');</script>";
        }

    }

}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">

    <title>Zadanie 3</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>
</head>
<body>

<div class="card bg-light">
    <article class="card-body mx-auto" style="max-width: 400px;">
        <h4 class="card-title mt-3 text-center">Prihlásenie cez STUBA</h4>
        <p class="text-center">Zadajte prihlasovacie údaje</p>

        <form action="ldap.php" method="POST">
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-user-circle"></i> </span>
                </div>
                <input name="login" class="form-control" placeholder="Používateľské meno" type="text" required>
            </div> <!-- form-group// -->

            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                </div>
                <input name="password" class="form-control" placeholder="Heslo" type="password" required>
            </div> <!-- form-group// -->

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"> Prihlásenie  </button>
            </div> <!-- form-group// -->
            <p class="text-center">Návrat na výber <a href="login.php">Prihlásenia</a> </p>
        </form>
    </article>
</div>

</body>
</html>
