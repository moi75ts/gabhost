<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../functions.php";
session_start();
$mois = sanitize_number($_POST['mois']);
if( $mois >= 1 AND $mois <= 12){
}else{
    show_error_light("La durée de location du serveur est invalide","?p=Creation-serveur");
}
$serveur = NEW SERVER($_POST["suuid"],$_SESSION["id"]);
if ($serveur->secure === TRUE){
    if(($serveur->gamme) === "essentiel"){
        $query2 = "UPDATE `users` SET `token_e`= token_e-:mois WHERE id = :id";
    }
    elseif(($serveur->gamme) === "medium"){
        $query2 = "UPDATE `users` SET `token_m`= token_m-:mois WHERE id = :id";
    }
    else{
        $query2 = "UPDATE `users` SET `token_g`= token_g-:mois WHERE id = :id";
    }
    $srvid = $serveur->id;
    $conn = bdd_connect();
    $stmt = $conn->prepare('UPDATE servers SET expdate = DATE_ADD(expdate, INTERVAL :mois MONTH) WHERE id = :id');
    $stmt->execute(['mois' => $mois,'id' => $srvid]);
    $stmt = $conn->prepare($query2);
    $stmt->execute(['id' => $_SESSION["id"],'mois' => $mois]);
    show_success("Vous venez d'étendre la location de votre serveur.","?p=Mes-serveurs");
    echo "...";
}