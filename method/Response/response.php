<?php


class Response 
{
    protected $ok=True;
    public $message;
    public $data;

    public function __construct()
    {
        

    }
    function is_obj_empty($obj)
    {
        if( is_null($obj) ){
            return true;
         }
         foreach( $obj as $key => $val ){
            return false;
         }
         return true;  

    }
    function Send()
    {
        $response=new StdClass();
        $response -> ok = $this -> ok;
        
        if (!empty($this -> message))
        {
            $response -> message = $this -> message;
        }
        
        
            $response -> data = $this -> data;
        

        print(json_encode($response));
        
    }

}    

?>