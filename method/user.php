<?php


class User
{

    public function __construct () 
    {
        
        $this->db=new db();
        $this->response=new StdClass();
        $this->response->ok=False;
        $this->response->result=[];
        $this->perms=new Permission();
    }

    function get($email)
    {


        $result=$this->db->query("SELECT email,username,roles FROM users WHERE email=?",$email)->fetchArray();
        if (sizeof($result)>0) 
        {
            $this->response->result=$result;
            $this->response->ok=True;
            return($this->response);
        }
        else
        {
            $this->response->ok=False;
            return($this->response);
        }

    }

    function allUserGet()
    {
        if ($this->perms->verifyPermission("SELECT email,username,roles FROM users")->ok==True)
        {
            $result=$this->db->query("SELECT email,username,roles FROM users")->fetchAll();
            if (sizeof($result)>0) 
            {
                $this->response->result=$result;
                $this->response->ok=True;
                return($this->response);
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

    function usersDelete($users)
    {
        
        if ($this->perms->verifyPermission("DELETE FROM users WHERE email=?")->ok==True)
        {
            foreach ($users as $user) {
                $user=json_decode($user);
                $result=$this->db->query("DELETE FROM users WHERE email=?",$user->email);
                if ($result->affectedRows()>0)
                {
                   
    
                }
                else
                {
                    $this->response->ok=False;
                    return($this->response);
                }
            }
            $this->response->ok=True;
            return($this->response);
            
        }
        else
        {
            $this->response->ok=False;
            return($this->response);   
        }
        

    }
    
    function userAdd($user)
    {
        $user=json_decode($user);
        if ($this->perms->verifyPermission("INSERT INTO users (roles,pass,username,email) VALUES (?,?,?,?)")->ok==True)
        {
            
            if (isset($user->roles) && isset($user->password) && isset($user->username) && isset($user->email))
            {
                $roles=$this->getUserRoles()->result;

                if (in_array($user->roles,$roles))
                {
                    $result=$this->db->query("INSERT INTO users (roles,pass,username,email) VALUES (?,?,?,?)",$user->roles,$user->password,$user->username,$user->email);
                    if ($result->affectedRows()>0)
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
        else
        {
            $this->response->ok=False;
            return($this->response);
        }
        

    }
    function getUserRoles()
    {
        $result=$this->db->query("SELECT roles FROM permission")->fetchAll();
        if (sizeof($result)>0) 
        {
            $roles=array();
            foreach ($result as $role)
            {
                array_push($roles,$role["roles"]);
            }
            $this->response->result=$roles;
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