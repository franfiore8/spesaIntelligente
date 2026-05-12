<?php
require 'config.php';
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = $pdo->prepare('SELECT * FROM cliente where email = :email');
    $sql->bindParam(':email', $email);
    $sql->execute();

    $cliente = $sql->fetch();

    if ($cliente) {
        $hashedpassword = $cliente['pass'];
        if (password_verify($password, $hashedpassword)) {
            $_SESSION['email'] = $cliente['email'];
            $_SESSION['codiceCarta'] = $cliente['codiceCarta'];
            $_SESSION['cognome'] = $cliente['cognome'];
            $_SESSION['nome'] = $cliente['nome'];
            $_SESSION['success'] = true;
            header("Location: index.php");
            exit;
        } else
            $error_message = "Password errata riprova.";
    } else
        $error_message = "Account inesistente verifica i dati o registrati.";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SpesaIntelligente</title>


    <style>
        :root {
            /* Colori estratti dal tuo logo */
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
            max-width: 440px;
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

        /* Barra superiore decorativa che richiama l'arco verde/arancio del logo */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--brand-green), var(--brand-green-light), var(--brand-yellow), var(--brand-orange));
        }

        /* Logo Immagine */
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

        .logo-img:hover {
            transform: scale(1.03);
        }

        /* Testi */
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

        /* Badge Reparto/Area */
        .badge {
            display: table;
            margin: 0 auto 15px auto;
            background: rgba(13, 71, 46, 0.08);
            color: var(--brand-green);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Gestione Errori */
        .alert {
            background-color: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Input Form */
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border-radius: 14px;
            border: 1.5px solid #e5e7eb;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #fafafa;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: #9ca3af;
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

        /* Pulsante Login */
        button {
            width: 100%;
            padding: 16px;
            background: var(--brand-orange);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(233, 102, 27, 0.25);
        }

        button:hover {
            background: var(--brand-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(233, 102, 27, 0.35);
        }

        button:active {
            transform: translateY(0);
        }

        /* Footer Card */
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--brand-green);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: var(--brand-orange);
            text-decoration: underline;
        }

        /* Dettaglio di sicurezza sul fondo */
        .security-notice {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 15px;
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        
        <div class="logo-container">
            
            <img src="logo.png" alt="Spesa Intelligente Logo" class="logo-img">
        </div>

        <div class="badge"><i class="fa-solid fa-id-card"></i> Carta Fedeltà</div>
        <h1>Accesso Cliente</h1>
        <p class="subtitle">Accedi per sbloccare sconti e liste intelligenti</p>

        <?php if (!empty($error_message)): ?>
            <div class="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error_message) ?></span>
            </div>
        <?php endif; ?>

        <form method="post">
            
            <div class="input-group">
                <input type="text" id="email" name="email" placeholder="Email o Codice Carta">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fa-solid fa-lock"></i>
            </div>

            <button type="submit"><span>Accedi alla Spesa</span><i class="fa-solid fa-arrow-right-to-bracket"></i></button>

        </form>

        <div class="footer">
            Non possiedi ancora la carta? <br><a href="register.php">Registrati ora gratuitamente</a>
        </div>

    </div>
    
    <div class="security-notice">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Connessione sicura e crittografata</span>
    </div>
</div>

</body>
</html>