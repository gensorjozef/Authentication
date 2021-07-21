<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "Classes/helper/Database.php";
require_once 'PHPGangsta/GoogleAuthenticator.php';

$websiteTitle = 'Zadanie 3';

$ga = new PHPGangsta_GoogleAuthenticator();

session_start();

$conn = (new Database())->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_REQUEST['login'];
    $password = $_REQUEST['password'];
    $code = $_REQUEST['code'];

    try {
        $sql = "SELECT * FROM user WHERE login=?";
        $stm = $conn->prepare($sql);
        $stm->execute([$login]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password,$user['password'])){
            $result = $ga->verifyCode($user['secret'], $code);
            if ($result == 1){
                $timestamp = date('Y-m-d H:i:s', strtotime('2 hour'));
                $sql = "INSERT INTO logs (login_id, logdate, logs.type) VALUES (?,?,?)";
                $stm = $conn->prepare($sql);
                $stm->bindValue(1,$user['id']);
                $stm->bindValue(2,$timestamp);
                $stm->bindValue(3,"registracia");
                $stm->execute();
                $_SESSION['2FA']['email'] = $user['email'];
                $_SESSION['2FA']['username'] = $user['name'] . " " . $user['surname'];
                $_SESSION['2FA']['id'] = $user['id'];
                header("location: index.php");
            } else{
                echo "<script type='text/javascript'>alert('Zadali ste nesprávny kód z aplikácie');</script>";
            }

        }else{
            echo "<script type='text/javascript'>alert('Nesprávne heslo');</script>";
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
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
        <h4 class="card-title mt-3 text-center">Prihlásenie</h4>
        <p class="text-center">Vyberte si spôsob prihlásenia</p>
        <p>
            <a href="ldap.php" class="btn btn-block btn-stuba"> <i class="fab fa-stripe-s"></i>   Prihlásenie cez STUBA</a>
            <a href="oauth.php" class="btn btn-block btn-google"> <i class="fab fa-google"></i>   Prihlásenie cez Google</a>
        </p>
        <p class="divider-text">
            <span class="bg-light">ALEBO</span>
        </p>
        <form action="login.php" method="POST">
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
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-mobile"></i> </span>
                </div>
                <input name="code" class="form-control" placeholder="Kód z aplikácie" type="number" required>
            </div> <!-- form-group// -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block "> Prihlásenie  </button>
            </div> <!-- form-group// -->
            <p class="text-center">Nemáš ešte účet? <a href="register.php">Registrácia</a> </p>
        </form>
    </article>
</div>
</body>
</html>