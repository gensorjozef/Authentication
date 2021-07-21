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


<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    session_start();

    if (isset($_SESSION['2FA']) && !empty($_SESSION['2FA'])){
        echo '<h4 class="card-title mt-3 text-center">' . "Vítaj " ."{$_SESSION['2FA']['username']}" . '</h4>';
        echo  '<h6 class="text-center">' . $_SESSION['2FA']['email']. '</h6>'  ;
        $_SESSION['id'] = $_SESSION['2FA']['id'];
        ?>

        <?php
    }elseif (isset($_SESSION['google']) && !empty($_SESSION['google'])){

         echo '<h4 class="card-title mt-3 text-center">' . "Vítaj " ."{$_SESSION['google']['username']}" . '</h4>';
         echo $_SESSION['google']['picture'];
        echo "<br>";
         echo  '<h6 class="text-center">' . $_SESSION['google']['email']. '</h6>'  ;
        $_SESSION['id'] = $_SESSION['google']['id'];


    }elseif (isset($_SESSION['ldap']) && !empty($_SESSION['ldap'])){
        echo '<h4 class="card-title mt-3 text-center">' . "Vítaj " ."{$_SESSION['ldap']['username']}" . '</h4>';
        echo  '<h6 class="text-center">' . $_SESSION['ldap']['mail']. '</h6>'  ;
        $_SESSION['id'] = $_SESSION['ldap']['id'];
    }else{
        header("location: login.php");
    }
?>
        <a href="logs.php" class="btn btn-info btn-lg active rounded mx-auto d-block" role="button" aria-pressed="true">Minulé prihlásenia</a>

        <p class="text-center">
            <a href="logout.php" >
                Odhásenie
            </a>
        </p>
    </article>
</div>

