<?php
require 'config.php';

session_start();

$codiceCarta = $_SESSION['codiceCarta'];
$cognome = $_SESSION['cognome'];
$nome = $_SESSION['nome'];

$sql = $pdo->prepare('SELECT scontrino.codiceScontrino, dataOra, numeroCassa, importoTotale, nomeProdotto, prezzo, punto_vendita.indirizzo, stato.stato FROM scontrino, prodotto, scontrino_prodotto, consegna, punto_vendita, stato where scontrino.codiceScontrino = scontrino_prodotto.codiceScontrino and prodotto.codiceBarre = scontrino_prodotto.codiceBarre and scontrino.codiceScontrino = consegna.codiceScontrino and punto_vendita.codiceNegozio = consegna.codiceNegozio and stato.idStato = consegna.idStato and scontrino.codiceCarta = :codiceCarta');
$sql->bindParam(':codiceCarta', $codiceCarta);
$sql->execute();
$ris = $sql->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cronologia Spesa - SpesaIntelligente</title>

    <style>
        :root {
            --brand-green: #0D472E;
            --brand-green-light: #5B9A42;
            --brand-orange: #E9661B;
            --brand-orange-hover: #D15513;
            --brand-yellow: #F6B819;

            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-color);
            background-image:
                radial-gradient(at 10% 10%, rgba(91, 154, 66, 0.1) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(233, 102, 27, 0.1) 0px, transparent 50%);
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 35px;
            box-shadow:
                0 10px 25px -5px rgba(13, 71, 46, 0.1),
                0 8px 10px -6px rgba(13, 71, 46, 0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(
                90deg,
                var(--brand-green),
                var(--brand-green-light),
                var(--brand-yellow),
                var(--brand-orange)
            );
        }

        .badge {
            display: table;
            margin: 0 auto 15px auto;
            background: rgba(13, 71, 46, 0.08);
            color: var(--brand-green);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1 {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--brand-green);
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            background: white;
        }

        thead {
            background: var(--brand-green);
            color: white;
        }

        th {
            padding: 16px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        tbody tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 10px;
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-delete {
            background: #dc2626;
        }

        .btn-delete:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        .btn-update {
            background: var(--brand-orange);
        }

        .btn-update:hover {
            background: var(--brand-orange-hover);
            transform: translateY(-2px);
        }

        .bottom-actions {
            margin-top: 30px;
            text-align: center;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo-img {
            max-width: 200px;
            height: auto;
            display: inline-block;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }

        .btn-main {
            display: inline-block;
            padding: 14px 24px;
            background: var(--brand-green);
            color: white;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(13, 71, 46, 0.2);
        }

        .btn-main:hover {
            background: var(--brand-green-light);
            transform: translateY(-2px);
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .card {
                padding: 20px;
            }

            th, td {
                padding: 12px 8px;
                font-size: 0.85rem;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
        
    </style>
</head>

<body>

<div class="container">

    <div class="card">
        <div class="logo-container"><img src="logo.png" alt="Spesa Intelligente Logo" class="logo-img"></div>
        <div class="badge">📊 Storico Acquisti</div>
        <h1 class="main-title">Ciao <?= $cognome . ' ' . $nome ?></h1>

        <p class="subtitle">Ecco la tua cronologia delle spese effettuate con la carta fedeltà</p>
        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <td>Codice scontrino</td>
                        <td>Data</td>
                        <td>Cassa</td>
                        <td>Importo</td>
                        <td>Prodotto</td>
                        <td>Prezzo</td>
                        <td>Punto vendita</td>
                        <td>Stato</td>
                    </tr>
                </thead>

                <?php if(count($ris) > 0): ?>
                    <?php foreach($ris as $el): ?>
                        <tr>
                            <td><?= $el['codiceScontrino'] ?></td>
                            <td><?= $el['dataOra'] ?></td>
                            <td><?= $el['numeroCassa'] ?></td>
                            <td><?= $el['importoTotale'] ?> €</td>
                            <td><?= $el['nomeProdotto'] ?></td>
                            <td><?= $el['prezzo'] ?> €</td>
                            <td><?= $el['indirizzo'] ?></td>

                            <td>
                                <?php if(empty($el['stato'])): ?>
                                    <form action="pagamento.php" method="POST">
                                        <input type="hidden" name="codiceScontrino" value="<?= $el['codiceScontrino'] ?>">
                                        <button type="submit" style="background:#E9661B; color:white; border:none; padding:6px 10px; border-radius:8px; cursor:pointer;">
                                            Procedi al pagamento
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?= $el['stato'] ?>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-message">
                            Nessuno scontrino trovato
                        </td>
                    </tr>
                <?php endif; ?>

            </table>

        </div>

        <div class="bottom-actions">
            <a href="carrello.php" class="btn-main">Vai al carrello</a>
        </div>

    </div>

</div>

</body>
</html>