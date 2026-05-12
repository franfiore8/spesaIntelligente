<?php
    $host="localhost";
    $dbname="spesaintelligente";
    $user="root";
    $pass="";
    try{
        $pdo= new PDO("mysql:host=$host; dbname=$dbname;", $user, $pass);
    }
    catch(PDOException $e){
        echo "Connessione non riuscita" .$e->getMessage();
    }


?>