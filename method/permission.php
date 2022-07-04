<?php

class Permission
{

    public function __construct () 
    {
        $this->token=["f43tvuvuMJb0VQSdk3DM0pLpETaCfhTjf2pCR4Dx"];
      
    }

    function verify_token($token)
    {
      
        if (in_array($token,$this->token)) 
        {
            $this->response->ok=True;
            return($this->response);
        }
        else
        {
            $this->response->ok=False;
            return($this->response);
        }
    }

}

?>