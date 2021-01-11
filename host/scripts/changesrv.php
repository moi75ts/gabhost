<?php
function getjeux(){
    $json = '{
        "jeux": {
          "minecraft": {
            "name": "Minecraft",
            "types": [
              "Minecraft Vanilla",
              "Spigot",
              "Paper Spigot",
              "Forge",
              "Jar custom"
            ]
          },
          "template": {
            "name": "Template",
            "types": [
              "Taupe gun",
              "LG UHC"
            ]
          },
          "terraria": {
            "name": "Terraria",
            "types": [
              "Terraria Vanilla",
              "Terraria TModLoader"
            ]
          },
          "rust": {
            "name": "Rust",
            "types": [
              "Rust"
            ]
          },
          "factorio": {
            "name": "factorio",
            "types": [
              "Factorio"
            ]
          },
          "mumble": {
            "name": "Mumble",
            "types": [
              "Mumble"
            ]
          },
          "7 days to die": {
            "name": "7 days to die",
            "types": [
              "7 days to die"
            ]
          }
        }
      }';
    $json = json_decode($json, true);
    return $json;
}
if(isset($_POST["changesrv"])){ 
    session_start();
    require "../functions.php";
    $suuid = $_POST["suuid"];
    $suuid = sanitize_name($suuid);
    $id = idfromsuuid($suuid);
    if (verifyownership($_SESSION["id"],$id) === TRUE){
      switch ($_POST["changesrv"]){
      
        case "MinecraftMinecraft Vanilla":
          $img = "quay.io/pterodactyl/core:java";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "25";
          $env = "\"VANILLA_VERSION\": \"latest\",
          \"CRACK\": \"true\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"SLOTS\": \"20\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "MinecraftSpigot":
          $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "16";
          $env = "\"DL_VERSION\": \"latest\",
          \"CRACK\": \"true\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"SLOTS\": \"20\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "MinecraftPaper Spigot":
          $img = "quay.io/pterodactyl/core:java";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "26";
          $env = "\"MINECRAFT_VERSION\": \"latest\",
          \"SLOTS\": \"20\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"CRACK\": \"true\",
          \"BUILD_NUMBER\": \"latest\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "MinecraftForge":
          $img = "quay.io/pterodactyl/core:java";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "27";
          $env = "\"MC_VERSION\": \"latest\",
          \"BUILD_TYPE\": \"recommended\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"CRACK\": \"true\",
          \"SLOTS\": \"20\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "MinecraftJar custom":
          $img = "quay.io/pterodactyl/core:java";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "17";
          $env = "\"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "TemplateTaupe gun":
          $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "28";
          $env = "\"DL_VERSION\": \"1.8.7\",
          \"CRACK\": \"true\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"SLOTS\": \"20\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "TemplateLG UHC":
          $img = "quay.io/parkervcp/pterodactyl-images:debian_openjdk-8-jre";
          $start = "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}";
          $egg = "29";
          $env = "\"DL_VERSION\": \"1.8.7\",
          \"CRACK\": \"true\",
          \"DIFFICULTY\": \"normal\",
          \"PVP\": \"true\",
          \"SLOTS\": \"20\",
          \"SPAWNP\": \"0\",
          \"SERVER_JARFILE\": \"server.jar\"";
          break;
  
        case "TerrariaTerraria Vanilla":
          $img = "quay.io/parkervcp/pterodactyl-images:ubuntu";
          $start = "./TerrariaServer.bin.x86_64 -config serverconfig.txt";
          $egg = "19";
          $env = "\"TERRARIA_VERSION\": \"latest\",
          \"WORLD_NAME\": \"lamap\",
          \"WORLD_SIZE\": \"2\",
          \"MAX_PLAYERS\": \"8\",
          \"WORLD_DIFFICULTY\": \"0\",
          \"SERVER_MOTD\": \"Changez le MOTD dans les options\"";
          break;
  
        case "TerrariaTerraria TModLoader":
          $img = "quay.io/parkervcp/pterodactyl-images:debian_mono-5";
          $egg = "20";
          $start = "./tModLoaderServer -ip 0.0.0.0 -port \${SERVER_PORT} -maxplayers \${MAX_PLAYERS} -world ~/saves/Worlds/\${WORLD_NAME}.wld -worldname \${WORLD_NAME} -autocreate \${WORLD_SIZE} -savedirectory ~/ -tmlsavedirectory ~/saves -modpath ~/mods";
          $env = "\"VERSION\": \"latest\",
          \"WORLD_NAME\": \"lamap\",
          \"WORLD_SIZE\": \"2\",
          \"MAX_PLAYERS\": \"8\"";
          break;
  
        case "RustRust":
          $img = "quay.io/pterodactyl/core:rust";
          $egg = "14";
          $mdp = bin2hex(random_bytes(5));
          $start = './RustDedicated -batchmode +server.port {{SERVER_PORT}} +server.identity \"rust\" +rcon.port {{RCON_PORT}} +rcon.web true +server.hostname \\\\\"{{HOSTNAME}}\\\\\" +server.level \\\\\"{{LEVEL}}\\\\\" +server.description \\\\\"{{DESCRIPTION}}\\\\\" +server.url \\\\\"{{SERVER_URL}}\\\\\" +server.headerimage \\\\\"{{SERVER_IMG}}\\\\\" +server.worldsize \\\\\"{{WORLD_SIZE}}\\\\\" +server.seed \\\\\"{{WORLD_SEED}}\\\\\" +server.maxplayers {{MAX_PLAYERS}} +rcon.password \\\\\"{{RCON_PASS}}\\\\\" +server.saveinterval {{SAVEINTERVAL}} {{ADDITIONAL_ARGS}}';
          $env = "\"HOSTNAME\": \"Mon serveur Gabhost\",
          \"OXIDE\": \"0\",
          \"LEVEL\": \"Procedural Map\",
          \"DESCRIPTION\": \"Mon serveur Gabhost (nom et description modifiable dans les paramètres du panel)\",
          \"WORLD_SIZE\": \"4000\",
          \"MAX_PLAYERS\": \"40\",
          \"RCON_PASS\": \"$mdp\",
          \"RCON_PORT\": \"28016\",
          \"SAVEINTERVAL\": \"60\"";
          break;
  
        case "factorioFactorio":
          $egg = "24";
          $img = "quay.io/parkervcp/pterodactyl-images:base_debian";
          $start = "./bin/x64/factorio --port {{SERVER_PORT}} --server-settings data/server-settings.json --start-server {{SAVE_NAME}}.zip";
          $env = "\"FACTORIO_VERSION\": \"latest\",
          \"SAVE_NAME\": \"gamesave\",
          \"SERVER_TOKEN\": \"changeme\",
          \"SERVER_NAME\": \"Mon serveur Factorio Gabhost (Change moi)\",
          \"SERVER_DESC\": \"Change moi\",
          \"SERVER_USERNAME\": \"NONAME\",
          \"SAVE_INTERVAL\": \"10\",
          \"SAVE_SLOTS\": \"5\",
          \"AFK_KICK\": \"0\",
          \"CRACK\": \"true\",
          \"MAX_SLOTS\": \"20\"";
          break;
  
        case "MumbleMumble":
          $egg = "12";
          $img = "quay.io/pterodactyl/core:glibc";
          $start = "./murmur.x86 -fg";
          $env = "\"MAX_USERS\": \"40\",
          \"MUMBLE_VERSION\": \"1.3.3\"";
          break;

        case "7 days to die7 days to die":
          $egg = "30";
          $img = "quay.io/parkervcp/pterodactyl-images:ubuntu_source";
          $start = "./7DaysToDieServer.x86_64 -configfile=serverconfig.xml -quit -batchmode -ServerName=\\\"\${SERVER_NAME}\\\" -ServerDescription=\\\"\${SERVER_DESC}\\\" -nographics -dedicated -ServerDisabledNetworkProtocols=\\\"\\\" -ServerPort=\${SERVER_PORT} -ServerMaxPlayerCount=\${MAX_PLAYERS} -GameDifficulty=\${GAME_DIFFICULTY} -ControlPanelEnabled=false -TelnetEnabled=true -TelnetPort=8081 -logfile logs/latest.log & echo -e \\\"Checing on telnet connection\\\" && until nc -z -v -w5 127.0.0.1 8081; do echo \\\"Waiting for telnet connection...\\\"; sleep 5; done && telnet -E 127.0.0.1 8081";
          $env = "\"SRCDS_APPID\": \"29442040\",
          \"AUTO_UPDATE\": \"1\",
          \"MAX_PLAYERS\": \"10\",
          \"GAME_DIFFICULTY\": \"2\",
          \"SERVER_NAME\": \"Mon Serveur 7 days to die Gabhost\",
          \"SERVER_DESC\": \"Mon Serveur 7 days to die Gabhost\",
          \"LD_LIBRARY_PATH\": \".\"";
          break;

          case "ArkArk":
            $serveur = new SERVER($suuid,$_SESSION["id"]);
            $serveur->wipeports();
            $queryport = $serveur->add1port();
            $gameport = $serveur->add2port();
            $serveur->setprimaryport($gameport);
            $egg = "31";
            $img = "quay.io/parkervcp/pterodactyl-images:debian_source";
            $start = "rmv() { echo -e \\\"stoppping server\\\"; rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD} -c saveworld && rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD} -c DoExit; }; trap rmv 15; cd ShooterGame/Binaries/Linux && ./ShooterGameServer {{SERVER_MAP}}?listen?SessionName=\\\"{{SESSION_NAME}}\\\"?ServerPassword={{ARK_PASSWORD}}?MaxPlayers={{SLOTS}}?ServerAdminPassword={{ARK_ADMIN_PASSWORD}}?Port={{SERVER_PORT}}?RCONPort={{RCON_PORT}}?QueryPort={{QUERY_PORT}}?RCONEnabled={{ENABLE_RCON}}\$( [ \\\"\$BATTLE_EYE\\\" == \\\"0\\\" ] || printf %s '?-NoBattlEye' ) -crossplay -server -log & until echo \\\"waiting for rcon connection...\\\"; rcon -a 127.0.0.1:\${RCON_PORT} -p \${ARK_ADMIN_PASSWORD}; do sleep 5; done";
            $adminpass = bin2hex(random_bytes(5));
            $install_type = "complex";
            $env = "\"SRCDS_APPID\": \"376030\",
            \"AUTO_UPDATE\": \"1\",
            \"ARK_PASSWORD\": \"\",
            \"ARK_ADMIN_PASSWORD\": \"$adminpass\",
            \"SERVER_MAP\": \"TheIsland\",
            \"SESSION_NAME\": \"Mon serveur ARK Gabhost (Change moi)\",
            \"ENABLE_RCON\": \"True\",
            \"RCON_PORT\": \"27020\",
            \"QUERY_PORT\": \"$queryport\",
            \"BATTLE_EYE\": \"0\",
            \"SLOTS\": \"40\"";
            break;
  
        default:
          show_error("Jeux inconnu", "?p=Mes-serveurs");
          break;
      }
      $create = updateserv($id,$start,$env,$egg,$img);
      $create = utf8_encode($create);
      $create = json_decode($create, true);
      if (isset($create["attributes"]["created_at"])){
        $srvid = $create["attributes"]["id"];
        reinstallsrv($srvid);
        show_success("Le serveur est bien changé","?p=Mes-serveurs");  
      }else{
        $erreur = $create["errors"][0]["detail"];
        switch ($erreur) {
            default:
                $erreur = "Une erreur inconnue est survenue: $erreur";
            break;
        }
        $erreur = "<br>". $erreur;
        show_error_nodest("le serveur n'a pas pu être créé: $erreur","?p=Mes-serveurs");
    }
    }

}