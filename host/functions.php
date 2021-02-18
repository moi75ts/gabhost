<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require "/var/www/php.php";
function files(){
    $files = shell_exec("ls ./fichiers/");
    $files = preg_split('/\s+/', $files);
    return $files;
}
require "scripts/server.class.php";
function detect_browser(){
    $arr_browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $user_browser = '';
    foreach ($arr_browsers as $browser) {
        if (strpos($agent, $browser) !== false) {
            $user_browser = $browser;
            break;
        }         
    }
    if ($user_browser == "Trident"){
        show_error("internet explorer ne fonctionne pas sur le site.","");
    }
    if ($user_browser == "Firefox"){
        return "Firefox";
    }
}

function get_csrf(){
    session_start();
    $token = md5(uniqid(rand(), true));
    return $_SESSION['csrf'] = $token;
}

function check_csrf($csrf_token){
    session_start();
    if($_POST){
        $csrf = isset($_POST['csrf']) ? $_POST['csrf'] : '';
        if(!empty($csrf)){
            if($_SESSION['csrf'] === $csrf_token){
                unset($_SESSION['csrf']);
                return TRUE;
            }
            else{
                return FALSE;
            }
        }
    }
}

function sanitize_email($string){
    if (empty($string)){
        return FALSE;
    }
    $clean_string = filter_var($string , FILTER_SANITIZE_EMAIL);
    return $clean_string;
}

function sanitize_string($string){
    if (empty($string)){
        return FALSE;
    }
    $clean_string = filter_var($string , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    return $clean_string;
}

function sanitize_name($string){
    if (empty($string)){
        return FALSE;
    }
    $clean_string = filter_var($string , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $clean_string = preg_replace("/[^A-Za-z0-9?!-]/",'',$clean_string);
    return $clean_string;
}

function sanitize_number($string){
    if (empty($string)){
        return FALSE;
    }
    $clean_string = filter_var($string , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $clean_string = preg_replace("/[^0-9]/",'',$clean_string);
    return $clean_string;
}

function sanitize_bool($bool){
    if(filter_var($bool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL){
        show_error("." ,"?p=#");
    }
    else{
        return $bool;
    }
}

function bdd_connect(){
    $dsn = "mysql:host=127.0.0.1;dbname=panel";//host infos
    $options = [
        PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
    ];
    try {
        $conn = new PDO($dsn, "php", getpassword(), $options); //getpassword function for your php.php file
    } catch(\PDOException $e) {
        show_error_nodest("Erreur dans la connexion à la BDD",""); //if error
    }
    return $conn; // if all worked return pdo connection object
}

function show_error($erreur,$page){
    session_start();
    session_destroy();
    echo "
    <head>
    <title>ERREUR</title>
    <link href=\"../style/w3.css\" rel=\"stylesheet\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    </head>
    <body>
    <br><br><br><br>
        <div class=\"w3-panel w3-display-container w3-red w3-center w3-display-middle full-width firefox-display-center\">
    <p>ERREUR.</p>
    <p>$erreur</p>
    <a href=\"/$page\"><button class=\"w3-button w3-medium w3-light-blue\">OK</button></a><br><br>
  </div>
  </body>";
  die();
}

function show_error_light($erreur,$page){
    session_start();
    echo "
    <head>
    <title>ERREUR</title>
    <link href=\"../style/w3.css\" rel=\"stylesheet\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    </head>
    <body>
    <br><br><br><br>
    <div class=\"w3-panel w3-display-container w3-red w3-center w3-display-middle full-width firefox-display-center\">
    <p>ERREUR.</p>
    <p>$erreur</p>
    <a href=\"/$page\"><button class=\"w3-button w3-medium w3-light-blue\">OK</button></a><br><br>
  </div>
  </body>";
  die();
}

function show_error_nodest($erreur,$page){
    echo "
    <head>
    <title>ERREUR</title>
    <link href=\"../style/w3.css\" rel=\"stylesheet\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    </head>
    <body>
    <div class=\"w3-panel w3-display-container w3-red w3-center w3-display-middle full-width firefox-display-center\">
    <p>ERREUR.</p>
    <p>$erreur</p>
    <a href=\"/$page\"><button class=\"w3-button w3-medium w3-light-blue\">OK</button></a><br><br>
  </div>
  </body>";
  die();
}

function show_success($message,$page){
    echo "
    <head>
    <title>Succès</title>
    <link href=\"../style/w3.css\" rel=\"stylesheet\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    </head>
    <body>
    <div class=\"w3-panel w3-display-container w3-green w3-center w3-display-middle full-width firefox-display-center\">
    <p>SUCCES.</p>
    <p>$message</p>
    <a href=\"/$page\"><button class=\"w3-button w3-medium w3-light-blue\">NICE !</button></a><br><br>
  </div>
  </body>";
}

function create_user($email1,$email2,$pseudo,$prenom,$nom,$mdp,$mdp2){
    $getapi = getapi();
    $conn = bdd_connect();
    $email1 = sanitize_email($email1);
    $email2 = sanitize_email($email2);
    $pseudo = sanitize_string($pseudo);
    $prenom = sanitize_name($prenom);
    $nom = sanitize_name($nom);
    $mdp = sanitize_string($mdp);
    $mdp2 = sanitize_string($mdp2);
    $stmt = $conn->prepare("SELECT username FROM `users` WHERE username = ?");
    $stmt->execute(array($pseudo));
    $result = $stmt->fetchall();
    if(isset($result[0])){
        show_error("il existe déjà un compte avec le pseudo $pseudo" , "?p=Inscription");
    }
    $stmt = $conn->prepare("SELECT email FROM `users` WHERE email = ?");
    $stmt->execute(array($email1));
    $result = $stmt->fetchall();
    if(isset($result[0])){
        show_error("il existe déjà un compte avec l'email $email1", "?p=Inscription");
    }

    if (filter_var($email1, FILTER_VALIDATE_EMAIL)) {
    }else{
        show_error("OOPS. le format de l'email est incorrect.","?p=Inscription");
    }
    if ($email1 !== $email2){
        show_error("OOPS. les emails ne sont pas identiques.","?p=Inscription");
    }    
    if (empty($pseudo) or empty($prenom) or empty($nom) or empty($mdp) or empty($mdp2)){
        show_error("OOPS. un des champs est vide.","?p=Inscription");
    }
    if ($mdp !== $mdp2){
        show_error("OOPS. les mots de passe ne sont pas identiques.","?p=Inscription");
    }
    $create = shell_exec("
   curl \"https://panel.gabhost.tk/api/application/users\" \
  -H \"Authorization: Bearer $getapi\" \
  -H \"Content-Type: application/json\" \
  -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
  -X POST \
  -d '
  {
      \"username\": \"$pseudo\",
      \"email\": \"$email1\",
      \"first_name\": \"$prenom\",
      \"last_name\": \"$nom\",
      \"password\": \"$mdp\",
      \"root_admin\": false,
      \"language\": \"en\"
  }'
    ");
    $create = utf8_encode($create);
    $create = json_decode($create, true);
    if(isset($create["errors"])){
        show_error("une erreur est survenue","?p=Inscription");
    }
}

function get_user_data(){
    $data = array();
    $conn = bdd_connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute(array($_SESSION["id"]));
    $result = $stmt->fetchall();
    if (isset($result[0])){
        return $result;
    }else{
        return "empty";
    }
    return $result;
}

function liste_serveurs(){
    $conn = bdd_connect();
    $liste_srv = array();
    $stmt = $conn->prepare("SELECT * FROM servers WHERE owner_id = ?");
    $stmt->execute(array($_SESSION["id"]));
    $result = $stmt->fetchall();
    if (isset($result[0])){
        return $result;
    }else{
        return "empty";
    }
}

function list_minecraft_versions(){
    $versions = array();
    $json = file_get_contents("https://launchermeta.mojang.com/mc/game/version_manifest.json");
    $json = utf8_encode($json);
    $json = json_decode($json, true);
    foreach($json['versions'] as $version) { //foreach element in $arr
        if(preg_match('/^[\d]\.[\d]+\.[\d]+$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
        if(preg_match('/^[\d]{1,2}[wW][\d]{1,2}[a-zA-Z]$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
        if(preg_match('/^[\d]\.[\d]+\.[\d]+ Pre-Release \d$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
        if(preg_match('/^[\d]\.[\d]+\.[\d]+-pre\d$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
        if(preg_match('/^[\d]\.[\d]+\.[\d]+-rc\d$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
        if(preg_match('/^[\d]\.[\d]+$/', $version['id'], $matches)){
        $versions[] = $matches;
        }
    }
    return $versions;
}

function nvserv($nom,$desc,$egg,$pack,$img,$start,$ram,$disk,$cpu,$db,$backups,$env){
    session_start();
    $getapi = getapi();
    if(!$_SESSION['id']){show_error("Erreur de session, reconnectez vous","?p=Connexion");}
    $id = $_SESSION['id'];
    return shell_exec("
    curl \"https://panel.gabhost.tk/api/application/servers\" \
    -H \"Authorization: Bearer $getapi\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X POST \
    -d '{
      \"name\": \"$nom\",
      \"user\": $id,
      \"description\": \"$desc\",
      \"egg\": $egg,
      \"pack\": $pack,
      \"docker_image\": \"$img\",
      \"startup\": \"$start\",
      \"limits\": {
        \"memory\": $ram,
        \"swap\": 0,
        \"disk\": $disk,
        \"io\": 500,
        \"cpu\": $cpu
      },
      \"feature_limits\": {
          \"databases\": $db,
          \"backups\": $backups
      },
      \"environment\": {
        $env
      },
      \"allocation\": {
        \"default\": 28,
        \"additional\": []
      },
      \"deploy\": {
        \"locations\": [1],
        \"dedicated_ip\": false,
        \"port_range\": []
      },
      \"start_on_completion\": true,
      \"skip_scripts\": false,
      \"oom_disabled\": true
    }'
    ");
}

function getnodestatus(){
    $api = getapi2();
    $shell = shell_exec("curl \"https://panel.gabhost.tk/api/application/nodes\" \
    -H \"Authorization: Bearer $api\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X GET ");
    $shell = utf8_encode($shell);
    $shell = json_decode($shell, true);
    return $shell;
}

function oneport($id){
    //get port limit (backup limit +1)
    $api = getapi2();
    $conn = bdd_connect();
    $stmt = $conn->prepare("SELECT backup_limit FROM `servers` WHERE id = ?");
    $stmt->execute(array($id));
    $portlimit = $stmt->fetchColumn() + 1;
    //get all port from server
    $stmt = $conn->prepare("SELECT port FROM `allocations` WHERE server_id = ?");
    $stmt->execute(array($id));
    $ports = $stmt->fetchall();
    if (count($ports) >= ($portlimit+1)){
        show_error_light("vous avez déjà atteint la limite de ports pour votre serveur." ,"?p=Mes-serveurs");
    }
    // get server node
    $server = shell_exec("curl \"https://panel.gabhost.tk/api/application/servers/$id\" \
    -H \"Authorization: Bearer $api\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X GET ");
    $server = utf8_encode($server);
    $server = json_decode($server, true);
    $node = $server["attributes"]["node"];
    //we have the node get avaiable ports
    $getport = shell_exec("curl \"https://panel.gabhost.tk/api/application/nodes/$node/allocations\" \
    -H \"Authorization: Bearer $api\"\
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X GET");
    $getport = utf8_encode($getport);
    $getport = json_decode($getport, true);
    //filtre ports
    foreach($getport["data"] as $port){
        if($port["attributes"]["assigned"] === False){
            $empty_ports[] = $port["attributes"]["port"];
        }
    }
    if (isset($empty_ports[0])){
    }else{
        show_error_light("Il n'y a plus de port disponible sur le node du serveur","?p=Mes-serveurs");
    }
    $stmt = $conn->prepare("UPDATE allocations SET server_id = ? WHERE port = ?");
    $stmt->execute(array($id,$empty_ports[0]));
    return $empty_ports[0];
}

class bdd {
    public $conn;
    function __construct(){
        $this->conn = bdd_connect();
    }
    public function getvalue($what, $table, $where, $equals){
        $query = 'SELECT ? FROM ? WHERE ? = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute(array('quoi' => $what , 'ou' => $table , 'comp1' => $where, 'comp2' => $equals));

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
    public function getconn(){
        return $this->conn;
    }
}

function updateserv($idsrv,$startup,$env,$egg,$img){
    session_start();
    $getapi = getapi();
    if(!$_SESSION['id']){show_error("Erreur de session, reconnectez vous","?p=Connexion");}
    $id = $_SESSION['id'];
    //echo $startup;
    $json = "
    curl \"https://panel.gabhost.tk/api/application/servers/$idsrv/startup\" \
    -H \"Authorization: Bearer $getapi\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X PATCH \
    -d '{
        \"startup\": \"$startup\",
        \"environment\": {
            $env
        },
        \"egg\": $egg,
        \"image\": \"$img\",
        \"skip_scripts\": false
      }'
    ";
    return shell_exec($json);
}

function verifyownership($uid,$sid){
    $getapi = getapi();
    $result = shell_exec("
    curl \"https://panel.gabhost.tk/api/application/servers/$sid\" \
    -H \"Authorization: Bearer $getapi\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X GET \
    ");
    $result = utf8_encode($result);
    $result = json_decode($result, true);
    if ($result["attributes"]["user"] !== $uid){
        show_error("Vous essayez de modifier un serveur qui n'est pas votre." , "?p=Accueil");
        return FALSE;
    }
    else {
        return TRUE;
    }
}

function idfromsuuid($suuid){
    session_start();
    $conn = bdd_connect();
    $stmt = $conn->prepare("SELECT id FROM `servers` WHERE uuidShort = ?");
    $stmt->execute(array($suuid));
    $result = $stmt->fetchColumn();
    if(isset($result)){
        return $result;
    }else{
        show_error_light("L'uuid du serveur n'existe pas","?p=Mes-serveurs");
    }
}

function reinstallsrv($id){
    $getapi = getapi();
    $result = shell_exec("
    curl \"https://panel.gabhost.tk/api/application/servers/$id/reinstall\" \
    -H \"Authorization: Bearer $getapi\" \
    -H \"Content-Type: application/json\" \
    -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
    -X POST \
    ");
    return $result;
}

function newark($id,$suuid,$oid){
    $server = new SERVER($suuid,$oid);
    //Query SQL pour savoir le port du serveur il sera utilisé pour le mettre en tant que variable pour la query (ark)
    $conn = bdd_connect();
    $stmt = $conn->prepare("SELECT port FROM allocations  WHERE server_id = ? ");
    $stmt->execute(array($id));
    $port = $stmt->fetchColumn();
    if(!empty($port)){
    }else{
        show_error_light("L'id du serveur n'existe pas","?p=Mes-serveurs");
    }
    //Ecrit le port de base comme valeur de la variable query pour ark
    $stmt = $conn->prepare("UPDATE server_variables SET variable_value = ? WHERE variable_id = 198 AND server_id = ?");
    $stmt->execute(array($port,$id));

    $gameport = $server->add2port($suuid);
    $server->setprimaryport($gameport);
    return TRUE;
}

function gettoken($uid,$sid){
    $server = new SERVER($sid,$uid);
    $gamme = $server->gamme;
    $conn = bdd_connect();
    switch ($gamme){
        case "gaming":
            $stmt = $conn->prepare("SELECT token_g FROM users WHERE id = ?");
            break;
        case "medium":
            $stmt = $conn->prepare("SELECT token_m FROM users WHERE id = ?");
            break;
        case "essentiel":
            $stmt = $conn->prepare("SELECT token_e FROM users WHERE id = ?");
            break;
        default:
            show_error("Gamme inconnue","?p=Accueil");
    }
    $stmt->execute(array($uid));
    return $stmt->fetchColumn();
}