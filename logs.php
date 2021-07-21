<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "Classes/helper/Database.php";
session_start();
$conn = (new Database())->getConnection();
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
    <article class="card-body mx-auto" style="max-width: 800px;">
        <?php
            $sql = "SELECT * FROM user left join logs on logs.login_id = user.id WHERE user.id=?";
            $stm = $conn->prepare($sql);
            $stm->execute([$_SESSION['id']]);
            $logs = $stm->fetchAll(PDO::FETCH_ASSOC);


            $sql_ldap = "SELECT COUNT(*) FROM logs WHERE type = 'ldap'";
            $stm_ldap = $conn->prepare($sql_ldap);
            $stm_ldap->execute();
            $ldap_count = $stm_ldap->fetch(PDO::FETCH_ASSOC);
//            echo $ldap_count['COUNT(*)'];
            $sql_reg = "SELECT COUNT(*) FROM logs WHERE type = 'registracia'";
            $stm_reg = $conn->prepare($sql_reg);
            $stm_reg->execute();
            $reg_count = $stm_reg->fetch(PDO::FETCH_ASSOC);
//            echo $reg_count['COUNT(*)'];
            $sql_google = "SELECT COUNT(*) FROM logs WHERE type = 'google'";
            $stm_google = $conn->prepare($sql_google);
            $stm_google->execute();
            $google_count = $stm_google->fetch(PDO::FETCH_ASSOC);
//            echo $google_count['COUNT(*)'];
        ?>
        <h4 class="card-title mt-3 text-center">Minulé prihlásenia užívateľa <?php  echo $logs[0]['name']. " " .  "{$logs[0]['surname']}" ?></h4>

        <table id="myTable2" class="display">
            <thead>
            <tr>
                <th>Meno užívateľa</th>
                <th>Username</th>
                <th>Čas prihlásenia</th>
                <th>Spôsob prihlásenia</th>
            </tr>
            </thead>
            <tbody>
            <?php
            echo '<br>';
                foreach ($logs as $log){
                    echo '<tr>';
                    echo '<td>' . "{$log['name']}" . " " .  "{$log['surname']}" . '</td>';
                    echo '<td>' . "{$log['login']}" .  '</td>';
                    echo '<td>' . "{$log['logdate']}" .  '</td>';
                    echo '<td>' . "{$log['type']}" . '</td>';
                    echo '</tr>';

                }

            ?>
            </tbody>
        </table>
        <br>
        <br>
        <h4 class="card-title mt-3 text-center">Štatistika prihlasovaní</h4>

        <table id="myTable3" class="display">
            <thead>
            <tr>
                <th>Spôsob prihlásenia</th>
                <th>Počet prihlásení</th>
            </tr>
            </thead>
            <tbody>
            <?php
            echo '<br>';

                echo '<tr>';
                echo '<td>' . "LDAP" . '</td>';
                echo '<td>' . "{$ldap_count['COUNT(*)']}" .  '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td>' . "Google" . '</td>';
                echo '<td>' . "{$google_count['COUNT(*)']}" .  '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td>' . "Registrácia" . '</td>';
                echo '<td>' . "{$reg_count['COUNT(*)']}" .  '</td>';
                echo '</tr>';



            ?>
            </tbody>
        </table>
        <p class="text-center">Späť na <a href="index.php">úvod</a> </p>
    </article>
</div>
<script>
    $(document).ready( function () {
        $('#myTable2').DataTable({
            "order": [[ 2, "desc" ]]
        } );
    } );
    $(document).ready( function () {
        $('#myTable3').DataTable({
            "order": [[ 1, "desc" ]]
        } );
    } );
</script>
</body>
</html>


