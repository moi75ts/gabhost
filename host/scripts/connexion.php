<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require "../functions.php";
    function connexion_pseudo($login,$password,$conn){
        $stmt = $conn->prepare("SELECT username, password, id FROM users WHERE username = ?");
        $stmt->execute(array($login));
        $result = $stmt->fetch();
        if(empty($result["id"])){
            show_error("Le compte n'existe pas" , "?p=Connexion");
        }
        if (password_verify($password, $result["password"])){
            session_start();
            $_SESSION['id'] = $result["id"];
            $_SESSION['pseudo'] = $result["username"];
            header("Location: ../");
        }
        else {show_error("Mot de passe ou email incorrect :(" , "?p=Connexion");}
        die();
    }
    function connexion_email($login,$password,$conn){
        $stmt = $conn->prepare("SELECT username, password, id FROM users WHERE email = ?");
        $stmt->execute(array($login));
        $result = $stmt->fetch();
        if(empty($result["id"])){
            show_error("Le compte n'existe pas" , "?p=Connexion");
        }
        if (password_verify($password, $result["password"])){
            session_start();
            $_SESSION['id'] = $result["id"];
            $_SESSION['pseudo'] = $result["username"];
            header("Location: ../");
        }
        else {show_error("Mot de passe ou email incorrect :(" , "?p=Connexion");}
        die();
    }
    if (check_csrf($_POST["csrf"]) === TRUE){
        $conn = bdd_connect();
        if ($conn){
            $login = sanitize_email($_POST['login']);
            $password = sanitize_string($_POST['password']);
            if ($login === FALSE or $password === FALSE){
                show_error("Un des champs est vide, ", "?p=Connexion");
            }
            if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                connexion_pseudo($login,$password,$conn);
            }else{
                connexion_email($login,$password,$conn);
            }
        }
        else{
            show_error("Une erreur c'est produite lors de la connexion à la Base de données, ", "?p=Connexion");
        }
    }else{
        show_error("Session de page invalide. Rechargez la page puis reconnectez vous.", "?p=Connexion");
    }
}else{
    echo "Request not POST";
}
