<?php	
CLASS NODE{
    public $id;
    public $totalram;
    public $totalspace;
    public $usedram;
    public $usedspace;
    public $status;
    public $freeram;
    public $freespace;
    public $free_e;
    public $free_m;
    public $free_g;

    function freeserver($ram,$disk){
        $limitram = intval($this->freeram / $ram);
        $limitdisk = intval($this->freespace / $disk);
        return min($limitram,$limitdisk);
    }

    function __construct($id,$totalram,$totalspace,$usedram,$usedspace,$status){
        $this->id = $id;
        $this->totalram = $totalram;
        $this->totalspace = $totalspace;
        $this->usedram = $usedram;
        $this->usedspace = $usedspace;
        $this->status = $status;
        $this->freeram = $totalram-$usedram;
        $this->freespace = $totalspace - $usedspace;
        $this->free_e = $this->freeserver(2048,4096);
        $this->free_m = $this->freeserver(4096,16384);
        $this->free_g = $this->freeserver(8192,65536);
    }
}