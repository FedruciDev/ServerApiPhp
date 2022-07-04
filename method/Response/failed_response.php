<?php 

class Failed extends Response
{

    public function __construct()
    {

        $this -> ok=False;
        $this -> message="Failed";
        $this->data=new StdClass();
    }

}
?>