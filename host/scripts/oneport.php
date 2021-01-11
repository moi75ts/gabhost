<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require "../functions.php";
session_start();
$suuid = $_POST["suuid"];
$suuid = sanitize_name($suuid);
$id = idfromsuuid($suuid);
if (verifyownership($_SESSION["id"],$id) === TRUE){
    $port = oneport($id);
    show_success("Le port $port viens d'etre ajouter a votre serveur","?p=Mes-serveurs");
}