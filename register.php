<?php
    require 'config.php';
    session_start();

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confermaPassword'] ?? '';
        $nTelefono = trim($_POST['nTelefono'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            $errors[] = "Formato email non valido.";
        if (strlen($name) < 2) 
            $errors[] = "Il nome deve contenere almeno 2 caratteri.";
        if (strlen($surname) < 2) 
            $errors[] = "Il cognome deve contenere almeno 2 caratteri.";
        if (strlen($password) < 8) 
            $errors[] = "La password deve essere di almeno 8 caratteri.";
        if ($password !== $confirm) 
            $errors[] = "Le password inserite non coincidono.";

        if (empty($errors)){
            $sql = $pdo->prepare('SELECT email FROM cliente where email = :email');
            $sql->bindParam(':email', $email);
            $sql->execute();
            $cliente = $sql->fetch();
            
            if ($cliente) {
                $errors[] = "Questa email è già associata ad una Carta Fedeltà.";
            } else {
                $hashpass = password_hash($password, PASSWORD_DEFAULT);
                $cartaFedelta = strtoupper($surname[0]) . strtoupper($name[0]) . rand(1000, 9999);
                
                $sql = $pdo->prepare('INSERT INTO cliente (codiceCarta, cognome, nome, email, pass, telefono, dataIscrizione) VALUES (:codiceCarta, :cognome, :nome, :email, :pass, :telefono, NOW())');
                $sql->bindParam(':codiceCarta', $cartaFedelta);
                $sql->bindParam(':cognome', $surname);
                $sql->bindParam(':nome', $name);
                $sql->bindParam(':email', $email);
                $sql->bindParam(':pass', $hashpass);
                $sql->bindParam(':telefono', $nTelefono);
                $sql->execute();
                
                $_SESSION['cliente'] = $email;
                $_SESSION['codiceCarta'] = $cartaFedelta;
                header("Location: login.php");
                exit;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione • SpesaIntelligente</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Palette coordinata con il logo */
            --brand-green: #0D472E;
            --brand-green-light: #5B9A42;
            --brand-orange: #E9661B;
            --brand-orange-hover: #D15513;
            --brand-yellow: #F6B819;
            
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --error-bg: #fef2f2;
            --error-text: #dc2626;
            --error-border: #fca5a5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 10%, rgba(91, 154, 66, 0.1) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(233, 102, 27, 0.1) 0px, transparent 50%);
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 520px; /* Leggermente più largo per i campi affiancati */
        }

        .card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 25px -5px rgba(13, 71, 46, 0.1), 0 8px 10px -6px rgba(13, 71, 46, 0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* Barra superiore decorativa */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--brand-green), var(--brand-green-light), var(--brand-yellow), var(--brand-orange));
        }

        /* Logo */
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-img {
            max-width: 180px;
            height: auto;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.03);
        }

        /* Badge & Titoli */
        .badge {
            display: table;
            margin: 0 auto 10px auto;
            background: rgba(233, 102, 27, 0.1);
            color: var(--brand-orange);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1 {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-green);
            margin-bottom: 5px;
        }

        p.subtitle {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        /* Box Errori */
        .error-box {
            background-color: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
            padding: 15px;
            border-radius: 14px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .error-box ul {
            list-style-type: none;
            padding-left: 5px;
        }

        .error-box li {
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form & Input */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #fafafa;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: #9ca3af;
            font-size: 0.9rem;
        }

        input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--brand-green-light);
            box-shadow: 0 0 0 4px rgba(91, 154, 66, 0.15);
        }

        input:focus + i,
        input:not(:placeholder-shown) + i {
            color: var(--brand-green);
        }

        /* Bottone */
        button {
            width: 100%;
            padding: 16px;
            background: var(--brand-green);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(13, 71, 46, 0.2);
        }

        button:hover {
            background: #0a3622;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 71, 46, 0.3);
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--brand-orange);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: var(--brand-orange-hover);
            text-decoration: underline;
        }

        /* Responsive per smartphone piccoli */
        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr; /* Stack verticale su mobile */
                gap: 0;
            }
            .card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">

        <!-- Logo -->
        <div class="logo-container">
            <img src="logo.png" alt="Spesa Intelligente Logo" class="logo-img">
        </div>

        <div class="badge"><i class="fa-solid fa-sparkles"></i> Nuovo Cliente</div>
        <h1>Attiva la tua Carta</h1>
        <p class="subtitle">Compila i dati per creare la tua Carta Fedeltà digitale</p>

        <!-- Gestione Errori -->
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach($errors as $e): ?>
                        <li><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            
            <!-- Riga: Nome e Cognome affiancati -->
            <div class="form-row">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Nome">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="surname" placeholder="Cognome>
                    <i class="fa-solid fa-tag"></i>
                </div>
            </div>

            <!-- Email -->
            <div class="input-group">
                <input type="email" name="email" placeholder="Email">
                <i class="fa-solid fa-envelope"></i>
            </div>

            <!-- Telefono -->
            <div class="input-group">
                <input type="tel" name="nTelefono" placeholder="Numero di Telefono (es. 3331234567)" required pattern="[0-9]{10}" maxlength="10">
                <i class="fa-solid fa-phone"></i>
            </div>

            <!-- Riga: Password e Conferma affiancate -->
            <div class="form-row">
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password (min. 8)" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="confermaPassword" placeholder="Conferma Pass." required>
                    <i class="fa-solid fa-shield-check"></i>
                </div>
            </div>

            <button type="submit">
                <i class="fa-solid fa-qrcode"></i>
                <span>Genera Carta Fedeltà</span>
            </button>

        </form>

        <div class="footer">
            Hai già una carta fedeltà? <a href="login.php">Accedi qui</a>
        </div>

    </div>
</div>

</body>
</html>