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

    $sql_exist = "SELECT * FROM user WHERE user.login=?";
    $stm_exist = $conn->prepare($sql_exist);
    $stm_exist->bindValue(1,$login);
    $stm_exist->execute();
    $result_exist = $stm_exist->fetch(PDO::FETCH_ASSOC);

    if (!empty($result_exist)){

        ?>
        <script type="text/javascript">
            alert('Toto používatelské meno je už obsadené');
            window.location.href = "register.php";
        </script>
        <?php
    }
    $password = $_REQUEST['password'];
    $name = $_REQUEST['name'];
    $surname = $_REQUEST['surname'];
    $email = $_REQUEST['email'];
    $secret = $_REQUEST['secret'];
    $code = $_REQUEST['code'];
    $result = $ga->verifyCode($secret, $code);

    if ($result == 1) {
        try {
            $sql = "INSERT INTO user (login, password, name, surname, email, secret) VALUES (?,?,?,?,?,?)";
            $stm = $conn->prepare($sql);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stm->execute([$login, $hash, $name, $surname, $email, $secret]);

            ?>
            <script type="text/javascript">
                alert('Registrácia bola úspešná');
                window.location.href = "index.php";
            </script>
            <?php

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } else {
        echo "<script type='text/javascript'>alert('Zadali ste nesprávny kód z aplikácie');</script>";
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
        <form action="register.php" method="POST" >
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

            <!--            <div class="form-group input-group">-->
            <!--                <div class="input-group-prepend">-->
            <!--                    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>-->
            <!--                </div>-->
            <!--                <input class="form-control" placeholder="Repeat password" type="password">-->
            <!--            </div>-->

            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                </div>
                <input name="name" class="form-control" placeholder="Meno" type="text" required>
            </div> <!-- form-group// -->
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                </div>
                <input name="surname" class="form-control" placeholder="Priezvisko" type="text" required>
            </div> <!-- form-group// -->

            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                </div>
                <input name="email" class="form-control" placeholder="Email" type="email" required>
            </div> <!-- form-group// -->

            <div class="form-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-qrcode"></i> </span>

                <?php
                $secret = $ga->createSecret();
                $qrCodeUrl = $ga->getQRCodeGoogleUrl($websiteTitle, $secret);
                echo '<img src="'.$qrCodeUrl.'" />';
                ?>
                <input type="hidden" name="secret" value="<?php echo $secret;?>">
                </div>
            </div>
            <div class="form-group input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-mobile"></i> </span>
                </div>
                <input name="code" class="form-control" placeholder="Kód z aplikácie" type="number" required>
            </div> <!-- form-group// -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"> Vytvoriť účet  </button>
            </div> <!-- form-group// -->
            <p class="text-center">Máš už účet? <a href="login.php">Prihlásenie</a> </p>
        </form>
    </article>
</div>

</body>
</html>

