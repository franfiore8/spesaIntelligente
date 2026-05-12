<?php
    require 'config.php';
    session_start();

    if (!isset($_SESSION['codiceCarta'])) {
        header("Location: login.php");
        exit;
    }

    $codiceCarta = $_SESSION['codiceCarta'];
    $nCassa = rand(1,5);
    $sql = $pdo->prepare('INSERT INTO scontrino (codiceCarta, dataOra, numeroCassa, importoTotale) VALUES (:codiceCarta, NOW(), :numeroCassa, 0)');
    $sql->bindParam(':codiceCarta', $codiceCarta);
    $sql->bindParam(':numeroCassa', $nCassa);
    $sql->execute();
    $codiceScontrino = $pdo->lastInsertId();
    $_SESSION['codiceScontrino'] = $codiceScontrino;

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Carrello - SpesaIntelligente</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            margin:0;
        }

        .box{
            background:white;
            padding:40px;
            border-radius:20px;
            text-align:center;
            box-shadow:0 10px 25px rgba(0,0,0,0.1);
        }

        h1{
            color:#0D472E;
            margin-bottom:20px;
        }

        a{
            display:inline-block;
            margin-top:20px;
            padding:12px 20px;
            background:#E9661B;
            color:white;
            text-decoration:none;
            border-radius:12px;
        }

        a:hover{
            background:#c94f11;
        }
    </style>
</head>

<body>

<div class="box">
    <h1>🛒 Carrello Attivo</h1>
    <p>Il tuo scontrino è stato creato o recuperato.</p>
    <p><strong>ID Scontrino:</strong> <?= $codiceScontrino ?></p>
    <a href="shop.php">Vai ai prodotti</a>

</div>

</body>
</html>