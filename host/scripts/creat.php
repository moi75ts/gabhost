<?php
//session_start();
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require "../functions.php";
session_start();
$nom = sanitize_string($_POST['nom']);
$desc = sanitize_string($_POST['desc']);
$egg = sanitize_string($_POST['egg_id']);
$pack = sanitize_string($_POST['pack_id']);
$mois = sanitize_number($_POST['mois']);
$gamme = $_POST['gamme'];

if( $mois >= 1 AND $mois <= 12){
}else{
    show_error_light("La durée de location du serveur est invalide","?p=Creation-serveur");
}

switch ($gamme){
    case ("créer mon serveur Essentiel"):
        $ram = 2048;
        $disk = 4096;
        $cpu = 70;
        $db = 1;
        $backups = 1;
        $query = "SELECT token_e FROM users WHERE id = :id";
        $query2 = "UPDATE `users` SET `token_e`= token_e-:mois WHERE id = :id";
        break;
    case ("créer mon serveur Medium"):
        $ram = 4096;
        $disk = 16384;
        $cpu = 140;
        $db = 3;
        $backups = 2;
        $query = "SELECT token_m FROM users WHERE id = :id";
        $query2 = "UPDATE `users` SET `token_m`= token_m-:mois WHERE id = :id";
        break;
    case ("créer mon serveur Gaming"):
        $ram = 8192;
        $disk = 65536;
        $cpu = 210;
        $db = 5;
        $backups = 3;
        $query = "SELECT token_g FROM users WHERE id = :id";
        $query2 = "UPDATE `users` SET `token_g`= token_g-:mois WHERE id = :id";
        break;
    default:
        show_error("Gamme non valide", "?p=Creation-serveur");
        break;
}

$bdd = new bdd;
$id = $_SESSION['id'];
$conn = $bdd->getconn();
$stmt = $conn->prepare($query);
$stmt->execute(['id' => $id]); 
$token = $stmt->fetchColumn();
if ((($token - $mois) < 0) or ($token === NULL )){
    show_error_light("Vous n'avez pas assez de token","?p=Accueil");
}


switch ($egg){
    case("27"): //minecraft forge
        $version = sanitize_string($_POST['version']);
        $img = "quay.io/pterodactyl/core:java";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"MC_VERSION\": \"$version\",
        \"BUILD_TYPE\": \"recommended\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"CRACK\": \"$crack\",
        \"SLOTS\": \"$slots\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("26"): //minecraft paper spigot
        $version = sanitize_string($_POST['version']);
        $img = "quay.io/pterodactyl/core:java";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"MINECRAFT_VERSION\": \"$version\",
        \"SLOTS\": \"$slots\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"CRACK\": \"$crack\",
        \"BUILD_NUMBER\": \"latest\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("25"): //minecraft vanilla
        $version = sanitize_string($_POST['version']);
        $img = "quay.io/pterodactyl/core:java";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"VANILLA_VERSION\": \"$version\",
        \"CRACK\": \"$crack\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"SLOTS\": \"$slots\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("12"): //Mumble
        $slots = sanitize_number($_POST['slots']);
        $img = "quay.io/pterodactyl/core:glibc";
        $start = "./murmur.x86 -fg";
        $env = "\"MAX_USERS\": \"$slots\",
        \"MUMBLE_VERSION\": \"1.3.3\"";
        break;
    case("14"): //RUST
        if ($_POST['oxide'] === "0"){
            $oxide = "0";
        }else{
            $oxide = sanitize_number($_POST['oxide']);
        }
        $NOM_SERVEUR = sanitize_string($_POST['HOSTNAME']);
        $DESC_SERVEUR = sanitize_string($_POST['DESC_SERV']);
        $taille_monde = sanitize_number($_POST['taillemonde']);
        $slots = sanitize_number($_POST['slots']);
        $mdp = bin2hex(random_bytes(5));
        $img = "quay.io/pterodactyl/core:rust";
        $start = './RustDedicated -batchmode +server.port {{SERVER_PORT}} +server.identity \"rust\" +rcon.port {{RCON_PORT}} +rcon.web true +server.hostname \\\\\"{{HOSTNAME}}\\\\\" +server.level \\\\\"{{LEVEL}}\\\\\" +server.description \\\\\"{{DESCRIPTION}}\\\\\" +server.url \\\\\"{{SERVER_URL}}\\\\\" +server.headerimage \\\\\"{{SERVER_IMG}}\\\\\" +server.worldsize \\\\\"{{WORLD_SIZE}}\\\\\" +server.seed \\\\\"{{WORLD_SEED}}\\\\\" +server.maxplayers {{MAX_PLAYERS}} +rcon.password \\\\\"{{RCON_PASS}}\\\\\" +server.saveinterval {{SAVEINTERVAL}} {{ADDITIONAL_ARGS}}';
        $env = "\"HOSTNAME\": \"$NOM_SERVEUR\",
        \"OXIDE\": \"$oxide\",
        \"LEVEL\": \"Procedural Map\",
        \"DESCRIPTION\": \"$DESC_SERVEUR\",
        \"WORLD_SIZE\": \"$taille_monde\",
        \"MAX_PLAYERS\": \"$slots\",
        \"RCON_PASS\": \"$mdp\",
        \"RCON_PORT\": \"28016\",
        \"SAVEINTERVAL\": \"60\"";
        break;
    case("16"): //spigot
        $version = sanitize_string($_POST['version']);
        $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"DL_VERSION\": \"$version\",
        \"CRACK\": \"$crack\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"SLOTS\": \"$slots\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("17"): //custom jar
        $img = "quay.io/pterodactyl/core:java";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $env = "\"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("19"): //Terraria vanilla
        $nom_clean = sanitize_name($nom);
        $taillemonde = sanitize_number($_POST['taillemonde']);
        if ($_POST['difficulty'] === "0"){
            $difficulty = "0";
        }else{
            $difficulty = sanitize_number($_POST['difficulty']);
        }
        echo $difficulty;
        $img = "quay.io/parkervcp/pterodactyl-images:ubuntu";
        $start = "./TerrariaServer.bin.x86_64 -config serverconfig.txt";
        $env = "\"TERRARIA_VERSION\": \"latest\",
                \"WORLD_NAME\": \"$nom_clean\",
                \"WORLD_SIZE\": \"$taillemonde\",
                \"MAX_PLAYERS\": \"8\",
                \"WORLD_DIFFICULTY\": \"$difficulty\",
                \"SERVER_MOTD\": \"Changez le MOTD dans les options\"";
        break;
    case("20"): //Terraria Tmodloader
        $nom_clean = sanitize_name($nom);
        $taillemonde = sanitize_number($_POST['taillemonde']);
        $img = "quay.io/parkervcp/pterodactyl-images:debian_mono-5";
        $start = "./tModLoaderServer -ip 0.0.0.0 -port \${SERVER_PORT} -maxplayers \${MAX_PLAYERS} -world ~/saves/Worlds/\${WORLD_NAME}.wld -worldname \${WORLD_NAME} -autocreate \${WORLD_SIZE} -savedirectory ~/ -tmlsavedirectory ~/saves -modpath ~/mods";
        $env = "\"VERSION\": \"latest\",
                \"WORLD_NAME\": \"$nom_clean\",
                \"WORLD_SIZE\": \"$taillemonde\",
                \"MAX_PLAYERS\": \"8\"";
        break;
    case("21"): //Terraria Tshock
        $nom_clean = sanitize_name($nom);
        $taillemonde = sanitize_number($_POST['taillemonde']);
        $img = "quay.io/pterodactyl/core:mono";
        $start = "mono TerrariaServer.exe -ip 0.0.0.0 -port {{SERVER_PORT}} -maxplayers {{MAX_PLAYERS}} -world {{WORLD_NAME}}.wld -autocreate {{WORLD_SIZE}}";
        $env = "\"TSHOCK_VERSION\": \"latest\",
                \"WORLD_NAME\": \"$nom_clean\",
                \"WORLD_SIZE\": \"$taillemonde\",
                \"MAX_PLAYERS\": \"8\"";
        break;
    case("24"): //Factorio
        $slots = sanitize_number($_POST['slots']);
        $NOM_SERVEUR = sanitize_string($_POST['HOSTNAME']);
        $DESC_SERVEUR = sanitize_string($_POST['DESC_SERV']);
        $token = sanitize_string($_POST['token']);
        $pseudo = sanitize_string($_POST['pseudo']);
        //$crack = sanitize_string($_POST['crack']);
        if($_POST['pseudo'] === ""){
            $pseudo = "unnamed";
        }
        if($_POST['token'] === ""){
            $token = "notoken";
        }
        $img = "quay.io/parkervcp/pterodactyl-images:base_debian";
        $start = "./bin/x64/factorio --port {{SERVER_PORT}} --server-settings data/server-settings.json --start-server {{SAVE_NAME}}.zip";
        $env = "\"FACTORIO_VERSION\": \"latest\",
                \"SAVE_NAME\": \"gamesave\",
                \"SERVER_TOKEN\": \"$token\",
                \"SERVER_NAME\": \"$NOM_SERVEUR\",
                \"SERVER_DESC\": \"$DESC_SERVEUR\",
                \"SERVER_USERNAME\": \"$pseudo\",
                \"SAVE_INTERVAL\": \"10\",
                \"SAVE_SLOTS\": \"5\",
                \"AFK_KICK\": \"0\",
                \"CRACK\": \"true\",
                \"MAX_SLOTS\": \"$slots\"";
        break;
    case("28"): //template taupe gun
        $version = "1.8.7";
        $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"DL_VERSION\": \"$version\",
        \"CRACK\": \"$crack\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"SLOTS\": \"$slots\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    case("29"): //template lg uhc
        $version = "1.8.7";
        $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
        $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
        $slots = sanitize_number($_POST['slots']);
        $crack = sanitize_string($_POST['crack']);
        $pvp = sanitize_string($_POST['pvp']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"DL_VERSION\": \"$version\",
        \"CRACK\": \"$crack\",
        \"DIFFICULTY\": \"$difficulty\",
        \"PVP\": \"$pvp\",
        \"SLOTS\": \"$slots\",
        \"SPAWNP\": \"0\",
        \"SERVER_JARFILE\": \"server.jar\"";
        break;
    
    case("30"): //template 7 days to die
        $img = "quay.io/parkervcp/pterodactyl-images:ubuntu_source";
        $start = "./7DaysToDieServer.x86_64 -configfile=serverconfig.xml -quit -batchmode -ServerName=\\\"\${SERVER_NAME}\\\" -ServerDescription=\\\"\${SERVER_DESC}\\\" -nographics -dedicated -ServerDisabledNetworkProtocols=\\\"\\\" -ServerPort=\${SERVER_PORT} -ServerMaxPlayerCount=\${MAX_PLAYERS} -GameDifficulty=\${GAME_DIFFICULTY} -ControlPanelEnabled=false -TelnetEnabled=true -TelnetPort=8081 -logfile logs/latest.log & echo -e \\\"Checing on telnet connection\\\" && until nc -z -v -w5 127.0.0.1 8081; do echo \\\"Waiting for telnet connection...\\\"; sleep 5; done && telnet -E 127.0.0.1 8081";
        $slots = sanitize_number($_POST['slots']);
        $NOM_SERVEUR = sanitize_string($_POST['HOSTNAME']);
        $DESC_SERVEUR = sanitize_string($_POST['DESC_SERV']);
        $difficulty = sanitize_string($_POST['difficulty']);
        $env = "\"SRCDS_APPID\": \"29442040\",
        \"AUTO_UPDATE\": \"1\",
        \"MAX_PLAYERS\": \"$slots\",
        \"GAME_DIFFICULTY\": \"$difficulty\",
        \"SERVER_NAME\": \"$NOM_SERVEUR\",
        \"SERVER_DESC\": \"$DESC_SERVEUR\",
        \"LD_LIBRARY_PATH\": \".\"";
        break;

    case("31"): //template Ark
        $img = "quay.io/parkervcp/pterodactyl-images:debian_source";
        $start = "rmv() { echo -e \\\"stoppping server\\\"; rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD} -c saveworld && rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD} -c DoExit; }; trap rmv 15; cd ShooterGame/Binaries/Linux && ./ShooterGameServer {{SERVER_MAP}}?listen?SessionName=\\\"{{SESSION_NAME}}\\\"?ServerPassword={{ARK_PASSWORD}}?MaxPlayers={{SLOTS}}?ServerAdminPassword={{ARK_ADMIN_PASSWORD}}?Port={{SERVER_PORT}}?RCONPort={{RCON_PORT}}?QueryPort={{QUERY_PORT}}?RCONEnabled={{ENABLE_RCON}}\$( [ \\\"\$BATTLE_EYE\\\" == \\\"0\\\" ] || printf %s '?-NoBattlEye' ) -crossplay -server -log & until echo \\\"waiting for rcon connection...\\\"; rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD}; do sleep 5; done";
        $slots = sanitize_number($_POST['slots']);
        $NOM_SERVEUR = sanitize_string($_POST['HOSTNAME']);
        $map = sanitize_string($_POST['map']);
        $passwd = sanitize_string($_POST['passwd']);
        $BATTLE_EYE = sanitize_bool($_POST['battleye']);
        $adminpass = bin2hex(random_bytes(5));
        $install_type = "complex";
        $env = "\"SRCDS_APPID\": \"376030\",
        \"AUTO_UPDATE\": \"1\",
        \"ARK_PASSWORD\": \"$passwd\",
        \"ARK_ADMIN_PASSWORD\": \"$adminpass\",
        \"SERVER_MAP\": \"$map\",
        \"SESSION_NAME\": \"$NOM_SERVEUR\",
        \"ENABLE_RCON\": \"True\",
        \"RCON_PORT\": \"27020\",
        \"QUERY_PORT\": \"27015\",
        \"BATTLE_EYE\": \"$BATTLE_EYE\",
        \"SLOTS\": \"$slots\"";
        break;
    default:
        show_error("Jeux inconnu", "?p=Creation-serveur");
        break;
}
$status_creation = nvserv($nom,$desc,$egg,$pack,$img,$start,$ram,$disk,$cpu,$db,$backups,$env);
$status_creation = utf8_encode($status_creation);
$status_creation = json_decode($status_creation, true);
if (isset($status_creation["attributes"]["created_at"])){
    $srvid = $status_creation["attributes"]["id"];
    if($install_type == "complex"){
        if(newark($srvid,$status_creation["attributes"]["identifier"],$id)!== TRUE){
            show_error_nodest("le serveur n'a pas pu être créé: $erreur","?p=Creation-serveur");
        }
    }
    show_success("Le serveur est bien créé (tkt c'est rien c'est le debug)","?p=Mes-serveurs");
    $stmt = $conn->prepare($query2);
    $stmt->execute(['id' => $id,'mois' => $mois]); 
    $stmt = $conn->prepare('UPDATE servers SET expdate = DATE_ADD(created_at, INTERVAL :mois MONTH) WHERE id = :id');
    $stmt->execute(['mois' => $mois,'id' => $srvid]);
}else{
    $erreur = $status_creation["errors"][0]["detail"];
    switch ($erreur) {
        case "No nodes satisfying the requirements specified for automatic deployment could be found.":
            $erreur = "Il n'y à plus de place dans la Gabhost, pour garantir les performmances des autres serveurs il n'est plus possible d'en créer de nouveaux";
        break; 
        default:
            $erreur = "Une erreur inconnue est survenue: $erreur";
        break;
    }
    $erreur = "<br>". $erreur;
    show_error_nodest("le serveur n'a pas pu être créé: $erreur","?p=Creation-serveur");
}
