<?php
header('Access-Control-Allow-Origin: *');


class Login 
{


    public function __construct () 
    {
        
        $this->db=new db();
        $this->response=new StdClass();
        $this->response->ok=False;
        $this->response->result=[];
        
    }


    function gen_token($email)
    {
        
        $result=$this->db->query("SELECT username FROM users WHERE email=?", $email)->fetchArray();
        if (count($result)>0)
        {
            $username=$result["username"];
            $token = openssl_random_pseudo_bytes(16);
            $token = bin2hex($token);


            $result=$this->db->query('INSERT INTO token (email,token,username) VALUES (?,?,?)',$email,$token,$username);
            if ($result->affectedRows()>0) 
            {
                $this->response->ok=True;
                $this->response->result=$token;
                
                return ($this->response);
            }
            else
            {
                $this->response->ok=False;
                return($this->response);
            }
        }
        else
        {
            $this->response->ok=False;
            return($this->response);
        }
        
        

        
    }   

    function login($email,$password)
    {
        
        $result=$this->db->query('SELECT email,username,roles FROM users WHERE email=? AND pass=? ',$email,$password)->fetchArray();
        if (sizeof($result)>0)
        {
            $token=$this->gen_token($email);
            if ($token->ok==true)
            {
                $token=$token->result;
                $this->response->ok=True;
                $this->response->result=$result;
                $this->response->result["token"]=$token;
                return ($this->response);
            }
            else
            {
                $this->response->ok=False;
                $this->response->result=$result;
                return($this->response);
            }
            
            
        }
        else
        {
            $this->response->ok=False;
            $this->response->result=$result;
            return($this->response);
        }


    } 

    function logout($email,$token)
    {
        
        $result=$this->db->query('DELETE FROM token WHERE email=? AND token=?',$email,$token);
        if ($result->affectedRows()>0) 
        {
            $this->response->ok=True;
            return ($this->response);
        }
        else
        {
            $this->response->ok=False;
            return($this->response);
        }

    }

    function authverify($email,$token)
    {

        $result=$this->db->query('SELECT token FROM token WHERE email=? AND token=? ',$email,$token)->fetchAll();
        
        if (sizeof($result)>0)
        {
            
            $user=new User();
            $user=$user->get($email);

            if ($user->ok==True)
            {
                $user=$user->result;
            }
            else
            {
                $this->response->ok=False;
                $this->response->result=$result;
                return($this->response);
            }
            $user["token"]=$token;
            $this->response->ok=True;
            $this->response->result=$user;
            
            return ($this->response);

        }
        else
        {
            
            $this->response->ok=False;
            $this->response->result=$result;
            return($this->response);
        }

    }
    
}


?>