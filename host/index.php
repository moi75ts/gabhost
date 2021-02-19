<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
//charge twig
session_start();
if(!empty($_SESSION['pseudo'])){
    $_SESSION['conn']=TRUE;
}else{
    $_SESSION['conn']=FALSE;
}
require "vendor/autoload.php";
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader,['debug' => true, 'auto_reload' => true]);
$twig->addExtension(new \Twig\Extension\DebugExtension());


//filter twig
$filter = new \Twig\TwigFilter('json_decode', function ($string) {
    return json_decode($string, true);
});
$twig->addFilter($filter);
$filter = new \Twig\TwigFilter('gbunique', function ($string) {
    return array_unique($string);
});
$twig->addFilter($filter);
$filter = new \Twig\TwigFilter('gbcount', function ($string) {
    return array_count_values($string);
});
$twig->addFilter($filter);


//Routing
$page = "Accueil";
if (isset($_GET['p'])){
    $page = $_GET['p'];
}
//def des fonctions //set le token de la session
require_once "functions.php";
//rendu template

switch ($page){
    case ("Accueil"):
        $node = getnodestatus();
        echo $twig->render('accueil.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION, "nodes" => $node , "agent" => detect_browser()]]);
        break;
    case ("Connexion"):
        echo $twig->render('connexion.twig', ['params' => ["page" => $page , "title" => $page , "csrf" => get_csrf(),"session" => $_SESSION], 'cookies' => $_COOKIE]);
        break;
    case ("fichiers"):
        echo $twig->render('files.twig', ['params' => ["page" => $page , "title" => $page , "csrf" => get_csrf(),"session" => $_SESSION,'files' => files()], 'cookies' => $_COOKIE]);
        break;
    case ("Inscription"):
        echo $twig->render('inscription.twig', ['params' => ["page" => $page , "title" => $page , "csrf" => get_csrf(),"session" => $_SESSION], 'cookies' => $_COOKIE]);
        break;
    case ("Les-configs"):
        echo $twig->render('configs.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION], 'cookies' => $_COOKIE]);
        break;
    case ("definitions"):
        echo $twig->render('definitions.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION, "agent" => detect_browser()], 'cookies' => $_COOKIE]);
        break;
    case ("nc"):
        echo $twig->render('main.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION, "agent" => detect_browser()], 'cookies' => $_COOKIE]);
        break;
    case ("Mes-serveurs"):
        if ($_SESSION['conn']===True){
            $liste_serveurs = liste_serveurs();
            $user_data = get_user_data();
            require "./scripts/changesrv.php";
            $liste_jeux = getjeux();
            echo $twig->render('mes-serveurs.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION , "serveurs" => $liste_serveurs , "user_data" => $user_data, "jeux" => $liste_jeux] , 'cookies' => $_COOKIE]);
            break;
        }
        else{
            header("Location: /?p=Connexion");
            break;
        }
    case ("Creation-serveur"):
        if ($_SESSION['conn']===True){
            $user_data = get_user_data();
            $versions = list_minecraft_versions();
            echo $twig->render('nvserv.twig', ['params' => ["page" => $page , "title" => $page ,"session" => $_SESSION , "versions" => $versions, "user_data" => $user_data] , 'cookies' => $_COOKIE]);
            break;
        }
        else{
            header("Location: /?p=Connexion");
            break;
        }
    default:
        header('HTTP/1.1 404');
        echo $twig->render('404.twig', ['params' => ["page" => $page , "title" => "ERREUR 404" , "csrf" => get_csrf(),"session" => $_SESSION] , 'cookies' => $_COOKIE]);
}