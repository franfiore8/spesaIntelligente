<?php
    require 'config.php';
    session_start();

    $codiceCarta = $_SESSION['codiceCarta'];
    $codiceScontrino = $_SESSION['codiceScontrino'];

    $sql = $pdo->prepare('SELECT scontrino.codiceScontrino, dataOra, importoTotale, nomeProdotto, prezzo, quantita, stato.stato, citta, indirizzo FROM scontrino, prodotto, scontrino_prodotto, consegna, punto_vendita, stato where scontrino.codiceScontrino = scontrino_prodotto.codiceScontrino and prodotto.codiceBarre = scontrino_prodotto.codiceBarre and scontrino.codiceScontrino = consegna.codiceScontrino and stato.idStato = consegna.idStato and punto_vendita.codiceNegozio = consegna.codiceNegozio and scontrino.codiceCarta = :codiceCarta and scontrino.codiceScontrino = :codiceScontrino');
    $sql->bindParam(':codiceCarta', $codiceCarta);
    $sql->bindParam(':codiceScontrino', $codiceScontrino);
    $sql->execute();
    $ris = $sql->fetchAll();

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dettaglio Ordine</title>

<style>
:root {
    --brand-green: #0D472E;
    --brand-orange: #E9661B;
    --bg-color: #f3f4f6;
}

body {
    font-family: Arial;
    background-color: var(--bg-color);
    padding: 40px 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.card {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 10px 25px -5px rgba(13, 71, 46, 0.1);
    position: relative;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, var(--brand-green), var(--brand-orange));
}

h2 {
    text-align: center;
    color: var(--brand-green);
    margin-bottom: 25px;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 18px;
    overflow: hidden;
}

th {
    background: var(--brand-green);
    color: white;
    padding: 14px;
}

td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f9fafb;
}

td:nth-child(3) {
    font-weight: bold;
    color: var(--brand-green);
}

td:nth-child(7) {
    color: var(--brand-orange);
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">

<div class="card">

<h2>📦 Dettaglio Ordine</h2>

<table>
    <tr>
        <th>Codice scontrino</th>
        <th>Data</th>
        <th>Importo</th>
        <th>Prodotto</th>
        <th>Prezzo</th>
        <th>Quantità</th>
        <th>Stato</th>
        <th>Città</th>
        <th>Indirizzo</th>
    </tr>

    <?php if(count($ris) > 0): ?>
        <?php foreach($ris as $el): ?>
        <tr>
            <td><?= $el['codiceScontrino'] ?></td>
            <td><?= $el['dataOra'] ?></td>
            <td><?= $el['importoTotale'] ?> €</td>
            <td><?= $el['nomeProdotto'] ?></td>
            <td><?= $el['prezzo'] ?> €</td>
            <td><?= $el['quantita'] ?></td>
            <td><?= $el['stato'] ?></td>
            <td><?= $el['citta'] ?></td>
            <td><?= $el['indirizzo'] ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">Nessun ordine trovato</td>
        </tr>
    <?php endif; ?>

</table>

</div>

</div>

</body>
</html>