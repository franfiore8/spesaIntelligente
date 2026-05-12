<?php
    require 'config.php';
    session_start();

    if (!isset($_SESSION['codiceCarta'])) {
        header("Location: login.php");
        exit;
    }

    $codiceCarta = $_SESSION['codiceCarta'];
    $codiceScontrino = $_SESSION['codiceScontrino'];

    $sql = $pdo->prepare('SELECT prodotto.codiceBarre, nomeProdotto, descrizione, prezzo, nomeReparto, numeroCorsia FROM prodotto, reparto where reparto.codiceReparto = prodotto.codiceReparto');
    $sql->execute();
    $ris = $sql->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $codiceBarre = $_POST['codiceBarre'] ?? '';
        $quantita = $_POST['quantita'] ?? 1;

        $sql = $pdo->prepare('INSERT INTO scontrino_prodotto (codiceScontrino, codiceBarre, quantita) VALUES (:codiceScontrino, :codiceBarre, :quantita) ON DUPLICATE KEY UPDATE quantita = quantita + VALUES(quantita)');
        $sql->bindParam(':codiceScontrino', $codiceScontrino);
        $sql->bindParam(':codiceBarre', $codiceBarre);
        $sql->bindParam(':quantita', $quantita);
        $sql->execute();

        $sql = $pdo->prepare('SELECT prezzo FROM prodotto where codiceBarre = :codiceBarre');
        $sql->bindParam(':codiceBarre', $codiceBarre);
        $sql->execute();

        $prodotto = $sql->fetch();
        $totaleDaAggiungere = $prodotto['prezzo'] * $quantita;

        $sql = $pdo->prepare('UPDATE scontrino SET importoTotale = importoTotale + :totale where codiceScontrino = :codiceScontrino');

        $sql->bindParam(':totale', $totaleDaAggiungere);
        $sql->bindParam(':codiceScontrino', $codiceScontrino);
        $sql->execute();

        header("Location: shop.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Shop</title>

<style>
body{
    font-family: Arial;
    background:#f3f4f6;
    padding:20px;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:12px;
    overflow:hidden;
}

th{
    background:#0D472E;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}

button{
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    opacity:0.85;
}

input{
    width:60px;
    padding:5px;
}
</style>
</head>

<body>

<div class="top-bar">

    <a href="order.php">Conferma pagamento</a>

    <form action="logout.php" method="POST">
        <button type="submit" style="background:#b91c1c;">
            🚪 Logout
        </button>
    </form>

</div>

<h2>🛒 SHOP PRODOTTI</h2>

<table>
<thead>
<tr>
    <th>Prodotto</th>
    <th>Descrizione</th>
    <th>Prezzo</th>
    <th>Reparto</th>
    <th>Corsia</th>
    <th>Quantità</th>
</tr>
</thead>

<tbody>

<?php foreach($ris as $el): ?>
<tr>
    <td><?= $el['nomeProdotto'] ?></td>
    <td><?= $el['descrizione'] ?></td>
    <td><?= $el['prezzo'] ?> €</td>
    <td><?= $el['nomeReparto'] ?></td>
    <td><?= $el['numeroCorsia'] ?></td>

    <td>
        <form method="POST">
            <input type="hidden" name="codiceBarre" value="<?= $el['codiceBarre'] ?>">
            <input type="number" name="quantita" min="1" value="1">
            <button type="submit" style="background:#E9661B;">Aggiungi</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</body>
</html>