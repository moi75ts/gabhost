<?php
CLASS NODECTRL{
    public $Nodelist = [];
    public $Panelurl;
    private $Apikey;
    public $totalfree_e;
    public $totalfree_m;
    public $totalfree_g;
    function __construct(){
        $this->Apikey = getapi2();
        $this->Panelurl = getpanelurl();
        $this->createnodelist($this->Panelurl,$this->Apikey);
        $this->sommesrv();
    }
    
    function createnodelist($website,$api){
        $json = shell_exec("curl \"https://$website/api/application/nodes\" \
        -H \"Authorization: Bearer $api\"\
        -H \"Content-Type: application/json\" \
        -H \"Accept: Application/vnd.pterodactyl.v1+json\" \
        -X GET");
        $json = utf8_encode($json);
        $json = json_decode($json, true);
        foreach ($json["data"] as $node){
            if($node["attributes"]["maintenance_mode"] == FALSE){
                $newnode = new NODE ($node["attributes"]["id"],$node["attributes"]["memory"]+(($node["attributes"]["memory_overallocate"]/100)*$node["attributes"]["memory"]),$node["attributes"]["disk"]+(($node["attributes"]["disk_overallocate"]/100)*$node["attributes"]["disk"]),$node["attributes"]["allocated_resources"]["memory"],$node["attributes"]["allocated_resources"]["disk"],$node["attributes"]["maintenance_mode"]); 
                $this->Nodelist[] = $newnode;
            }
        }
    }
    
    function sommesrv(){
        $somme_e = 0;
        $somme_m = 0;
        $somme_g = 0;
        foreach ($this->Nodelist as $node){
            $somme_e += $node->free_e;
        }
        foreach ($this->Nodelist as $node){
            $somme_m += $node->free_m;
        }
        foreach ($this->Nodelist as $node){
            $somme_g += $node->free_g;
        }
        $this->totalfree_e = $somme_e;
        $this->totalfree_m = $somme_m;
        $this->totalfree_g = $somme_g;
    }
}