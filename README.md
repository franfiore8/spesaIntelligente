🛒 SpesaIntelligente
Web application per la gestione della spesa al supermercato con carta fedeltà digitale, sviluppata in PHP e MySQL.

📌 Descrizione
SpesaIntelligente è una piattaforma web che permette ai clienti di registrarsi, ottenere una carta fedeltà digitale e fare la spesa online. Ogni cliente può sfogliare il catalogo prodotti divisi per reparto, aggiungere articoli al carrello e visualizzare la cronologia completa dei propri scontrini con lo stato della consegna.

Il progetto simula il sistema informatico di una catena di supermercati: ogni acquisto genera uno scontrino digitale associato alla carta fedeltà del cliente, gestito dal database insieme alle informazioni sul punto vendita e sullo stato della consegna.

👥 Componenti del gruppo
(inserire i nomi dei componenti del gruppo)

🗂️ Schema del database
Nome database: spesaintelligente

Diagramma delle relazioni
cliente (codiceCarta) ──────────────────────────────────────┐
                                                            │
scontrino (codiceScontrino, codiceCarta FK) ────────────────┘
    │
    ├── scontrino_prodotto ── prodotto ── reparto
    │      (codiceBarre FK)   (codiceReparto FK)
    │
    └── consegna ── punto_vendita
           │        (codiceNegozio FK)
           └── stato (idStato FK)
Tabelle
cliente — utenti registrati
Campo	Tipo	Chiave
codiceCarta	VARCHAR	PK — generato automaticamente (es. RM1234)
cognome	VARCHAR	
nome	VARCHAR	
email	VARCHAR	UNIQUE
pass	VARCHAR	Hash bcrypt
telefono	VARCHAR	
dataIscrizione	DATETIME	Impostata con NOW()
prodotto — catalogo prodotti
Campo	Tipo	Chiave
codiceBarre	VARCHAR	PK
nomeProdotto	VARCHAR	
descrizione	TEXT	
prezzo	DECIMAL	
codiceReparto	INT	FK → reparto
reparto — reparti del supermercato
Campo	Tipo	Chiave
codiceReparto	INT	PK
nomeReparto	VARCHAR	
numeroCorsia	INT	
scontrino — acquisti effettuati
Campo	Tipo	Chiave
codiceScontrino	INT	PK auto-increment
codiceCarta	VARCHAR	FK → cliente
dataOra	DATETIME	
numeroCassa	INT	
importoTotale	DECIMAL	Aggiornato dinamicamente
scontrino_prodotto — prodotti in ogni scontrino
Campo	Tipo	Chiave
codiceScontrino	INT	PK + FK → scontrino
codiceBarre	VARCHAR	PK + FK → prodotto
quantita	INT	
punto_vendita — negozi fisici
Campo	Tipo	Chiave
codiceNegozio	INT	PK
citta	VARCHAR	
indirizzo	VARCHAR	
stato — stati di consegna
Campo	Tipo	Chiave
idStato	INT	PK
stato	VARCHAR	Es. "In consegna", "Consegnato"
consegna — associa scontrino, negozio e stato
Campo	Tipo	Chiave
codiceScontrino	INT	PK + FK → scontrino
codiceNegozio	INT	FK → punto_vendita
idStato	INT	FK → stato
🖥️ Pagine dell'applicazione
login.php — Accesso cliente
Form di accesso con email e password. Verifica le credenziali tramite password_verify (bcrypt) e in caso di errore mostra messaggi specifici ("Password errata", "Account inesistente"). Al login corretto salva in sessione: codiceCarta, nome, cognome, email.

register.php — Registrazione carta fedeltà
Form di registrazione con validazione lato server: - formato email valido - nome e cognome di almeno 2 caratteri - password di almeno 8 caratteri - conferma password coincidente - email non già registrata nel database

Il codice carta fedeltà viene generato automaticamente come iniziale cognome + iniziale nome + 4 cifre casuali (es. RM4821). La password viene salvata con password_hash (bcrypt).

index.php — Dashboard / Cronologia spese
Pagina principale dopo il login. Mostra tutti gli scontrini del cliente loggato con: codice scontrino, data e ora, numero cassa, importo totale, prodotto acquistato, prezzo unitario, punto vendita e stato della consegna. Se la consegna non ha ancora uno stato, compare il pulsante "Procedi al pagamento".

carrello.php — Avvio acquisto
Crea un nuovo scontrino nel database associato alla carta fedeltà del cliente (importo iniziale 0, numero cassa assegnato casualmente tra 1 e 5). Salva il codiceScontrino in sessione e reindirizza allo shop.

shop.php — Catalogo prodotti
Visualizza tutti i prodotti con nome, descrizione, prezzo, reparto e numero corsia. Per ogni prodotto è presente un form con campo quantità e pulsante "Aggiungi". All'aggiunta il sistema: 1. inserisce la riga in scontrino_prodotto (o incrementa la quantità se il prodotto è già nel carrello, tramite ON DUPLICATE KEY UPDATE) 2. aggiorna importoTotale nello scontrino corrente

order.php — Dettaglio ordine corrente
Visualizza il dettaglio completo dello scontrino aperto: prodotti acquistati con quantità, prezzi, importo totale, stato consegna, città e indirizzo del punto vendita.

config.php — Configurazione database
Connessione al database tramite PDO con gestione delle eccezioni.

📁 Struttura dei file
spesaIntelligente/
├── config.php       # Connessione PDO al database MySQL
├── login.php        # Pagina di login
├── register.php     # Registrazione nuovo cliente / carta fedeltà
├── index.php        # Dashboard con cronologia scontrini
├── carrello.php     # Creazione scontrino e avvio acquisto
├── shop.php         # Catalogo prodotti con aggiunta al carrello
├── order.php        # Dettaglio ordine corrente
└── logo.png         # Logo SpesaIntelligente
Nota: il file logout.php è referenziato nell'applicazione ma non è incluso nel pacchetto consegnato. Va creato manualmente (vedi istruzioni sotto).

⚙️ Requisiti
PHP >= 7.4
MySQL >= 5.7 (o MariaDB equivalente)
Server Apache — consigliato XAMPP per sviluppo locale
Estensioni PHP: pdo, pdo_mysql
🚀 Installazione e avvio
1. Copiare il progetto nella cartella web

C:/xampp/htdocs/spesaIntelligente/
2. Creare il database

Aprire phpMyAdmin su http://localhost/phpmyadmin e creare un database con il nome:

spesaintelligente
3. Importare le tabelle

Dalla scheda Importa di phpMyAdmin, caricare il file SQL di struttura e (opzionalmente) il file con i dati di esempio.

4. Verificare config.php

$host   = "localhost";
$dbname = "spesaintelligente";
$user   = "root";
$pass   = "";
Modificare $user e $pass se il proprio MySQL usa credenziali diverse dalla configurazione di default XAMPP.

5. Creare il file logout.php (mancante nel pacchetto)

<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>
6. Aprire l'applicazione nel browser

http://localhost/spesaIntelligente/login.php
🔑 Credenziali di prova
Campo	Valore
Email	(inserire email dell'utente di test dal file SQL)
Password	(inserire password in chiaro corrispondente)
Codice Carta	(inserire codice carta generato)
🔐 Sicurezza implementata
Password salvate con hashing bcrypt (password_hash / password_verify)
Tutte le query usano prepared statements PDO con bindParam — protezione da SQL injection
Output HTML protetto con htmlspecialchars — protezione da XSS
Pagine riservate protette da controllo di sessione con redirect automatico al login
⚠️ Problemi noti e funzionalità non completate
logout.php è referenziato ma assente nel pacchetto (soluzione nelle istruzioni di installazione)
pagamento.php è referenziato in index.php ma non è stato implementato
In shop.php manca il controllo che $_SESSION['codiceScontrino'] sia definito: accedendo direttamente alla pagina senza passare da carrello.php si genera un errore PHP
Non è presente una distinzione tra utente normale e amministratore
Non è implementata una funzionalità di ricerca o filtro sui prodotti
Il layout di shop.php non è allineato al design system (verde/arancio/Poppins) delle altre pagine
🛠️ Tecnologie utilizzate
Tecnologia	Ruolo
PHP 8	Logica server-side, gestione sessioni, operazioni CRUD
MySQL + PDO	Database relazionale con prepared statements
HTML5 + CSS3	Interfaccia utente e layout responsive
Font Awesome 6	Icone dell'interfaccia
Google Fonts – Poppins	Tipografia
