<?php
require "../functions.php";
if (check_csrf($_POST["csrf"]) === TRUE){
    create_user($_POST["email"],$_POST["email2"],$_POST["pseudo"],$_POST["prenom"],$_POST["nom"],$_POST["password"],$_POST["password2"]);
    show_success("SUPER! votre compte est crée","?p=Connexion");
}else{
    show_error("Session de page invalide. Rechargez la page puis reconnectez vous.", "?p=Inscription");
}