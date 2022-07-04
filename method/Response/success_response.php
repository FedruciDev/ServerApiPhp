<?php 

class Success extends Response
{

    public function __construct()
    {

        $this -> ok=True;
        $this -> message="Success";
        $this->data=new StdClass();
        
    }

}
?>