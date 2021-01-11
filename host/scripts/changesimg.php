<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
session_start();
require "../functions.php";
$suuid = sanitize_name($_POST["suuid"]);
$serveur = new SERVER($suuid, $_SESSION["id"]);
if ($serveur->secure === TRUE){
    if (isset($_POST["updateimg"])){
        $switch = $_POST["updateimg"];
        switch( $switch) {
            case "1";
                $serveur->updateimg("quay.io/pterodactyl/core:java");
                break;
            case "2";
                $serveur->updateimg("quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-openj9");
                break;
            case "3";
                $serveur->updateimg("quay.io/parkervcp/pterodactyl-images:debian_openjdk-11");
                break;
            default:
            show_error("Option inconnue", "?p=Creation-serveur");
            break;
        }
        show_success("La version de java est bien changer","?p=Mes-serveurs");
    }
    else{
        show_error("Vous n'avez rien choisis","?p=Accueil");
    }
}else{
    show_error("Vous n'êtes pas propriétaire du serveur","?p=Accueil");
}