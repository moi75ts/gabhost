<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
CLASS SERVER {
    public $id;
    private $conn;
    public $ports;
    public $node;
    public $secure;
    public $gamme;
    private $api;
    function __construct($suuid,$oid){
        $this->conn = bdd_connect();
        $this->id = $this->idfromsuuid($suuid);
        $this->secure = $this->verifyownership($oid);
        $this->gamme = $this->getgamme();
        $this->api = getapi2();
    }

    public function idfromsuuid($suuid){
        $stmt = $this->conn->prepare("SELECT id FROM `servers` WHERE uuidShort = ?");
        $stmt->execute(array($suuid));
        $id = $stmt->fetchColumn();
        return $id;
    }

    public function getgamme(){
        $stmt = $this->conn->prepare("SELECT cpu FROM `servers` WHERE id = ?");
        $stmt->execute(array($this->id));
        $cpu = $stmt->fetchColumn();
        if($cpu == "70"){
            $gamme = "essentiel";
            return $gamme;
        }
        elseif($cpu == "140"){
            $gamme = "medium";
            return $gamme;
        }
        else{
            $gamme = "gaming";
            return $gamme;
        }
    }

    public function verifyownership($oid){
        $stmt = $this->conn->prepare("SELECT owner_id FROM `servers` WHERE id = ?");
        $stmt->execute(array($this->id));
        $owner = $stmt->fetchColumn();
        if($owner === $oid){
            return TRUE;
        }
        else{
            return FALSE;
            die();
        }
    }

    public function ports(){
        $stmt = $this->conn->prepare("SELECT port FROM `allocations` WHERE server_id = ?");
        $stmt->execute(array($this->id));
        $ports = $stmt->fetchAll();
        return $ports;
    }

    public function nodeid(){
        $stmt = $this->conn->prepare("SELECT node_id FROM `servers` WHERE id = ?");
        $stmt->execute(array($this->id));
        $nodeid = $stmt->fetchColumn();
        return $nodeid;
    }

    public function setprimaryport($port){
        $stmt = $this->conn->prepare("SELECT id FROM `allocations` WHERE port = ?");
        $stmt->execute(array($port));
        $portid = $stmt->fetchColumn();

        $stmt = $this->conn->prepare("UPDATE servers SET allocation_id = ? WHERE id = ?");
        $stmt->execute(array($portid,$this->id));
        return TRUE;
    }

    public function updatestartcommand($command){
        $stmt = $this->conn->prepare("UPDATE servers SET startup = ? WHERE id = ?");
        $stmt->execute(array($command,$this->id));
        return TRUE;
    }

    public function updateimg($img){
        $stmt = $this->conn->prepare("UPDATE servers SET `image` = ? WHERE id = ?");
        $stmt->execute(array($img,$this->id));
        return TRUE;
    }

    public function wipeports(){
        $stmt = $this->conn->prepare("SELECT port FROM `allocations` WHERE server_id = ?");
        $stmt->execute(array($this->id));
        $result = $stmt->fetch();
        foreach($result as $port){
            $stmt = $this->conn->prepare("UPDATE allocations SET server_id = NULL WHERE `port` = ?");
            $stmt->execute(array($port));
        }
        return TRUE;
    }

    public function add1port(){
        $api = $this->api;
        $stmt = $this->conn->prepare("SELECT backup_limit FROM `servers` WHERE id = ?");
        $stmt->execute(array($this->id));
        $portlimit = $stmt->fetchColumn() + 1;
        if(count($this->ports()) <= $portlimit){
            $nodeid = $this->nodeid();
            $getport = shell_exec("curl \"https://panel.gabhost.tk/api/application/nodes/$nodeid/allocations\" \
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
                $stmt = $this->conn->prepare("UPDATE allocations SET server_id = ? WHERE port = ?");
                $stmt->execute(array($this->id,$empty_ports[0]));
                return $empty_ports[0];
            }else{
                show_error_light("Il n'y a plus de port disponible sur le node du serveur","?p=Mes-serveurs");
            }
        }else{
            return "erreur-max-port-reached";
        }
        return $newport;
    }

    public function add2port(){
        $api = $this->api;
        $stmt = $this->conn->prepare("SELECT backup_limit FROM `servers` WHERE id = ?");
        $stmt->execute(array($this->id));
        $portlimit = $stmt->fetchColumn() + 1;
        if(count($this->ports()) <= ($portlimit-1)){
            $nodeid = $this->nodeid();
            $getport = shell_exec("curl \"https://panel.gabhost.tk/api/application/nodes/$nodeid/allocations\" \
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
            if (isset($empty_ports[1])){
                while(isset($empty_ports[1])){
                    if(($empty_ports[1]-$empty_ports[0])===1){ //si le 2ème - le 1er = 1 alors les ports sont conséqutifs 
                        break; //donc c'est bon
                    }else{
                        unset($empty_ports[0]); //sinon on suppr le 1er port car obligatoirement standalone
                    }
                    $empty_ports = array_values($empty_ports); //on réorganise les indexs
                }
                $stmt = $this->conn->prepare("UPDATE allocations SET server_id = ? WHERE port = ?");
                $stmt->execute(array($this->id,$empty_ports[0]));
                $stmt = $this->conn->prepare("UPDATE allocations SET server_id = ? WHERE port = ?");
                $stmt->execute(array($this->id,$empty_ports[1]));
                return $empty_ports[0];
            }else{
                show_error_light("Il n'y a plus de ports disponible sur le node du serveur","?p=Mes-serveurs");
            }
        }else{
            return "erreur-max-port-reached";
        }
        return $newport;
    }

}