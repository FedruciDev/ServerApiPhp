<?php

class Permission
{
    protected $method=["INSERT","UPDATE","SELECT","DELETE"];
    
    protected $table=["users","permission","prodotti"];
    
    public function __construct () 
    {
        $this->db=new db();
        $this->response=new StdClass();
        $this->response->ok=False;
        $this->response->result=[];
        $this->roles=$this->db->query("SELECT * FROM permission")->fetchAll();
        if (isset($_POST["auth"]))
        {
          $this->userRole=$this->db->query("SELECT roles FROM users WHERE email=?",json_decode($_POST["auth"])->email)->fetchArray()["roles"];
        }
    }

    function PathPermission()
    {

        $result=$this->db->query("SELECT * FROM permission")->fetchAll();
        if (sizeof($result)>0) 
        {
            $this->response->result=$result;
            $this->response->ok=True;
            return($this->response);
        }
    }

    function GetQueryType($Query)
    {
        $str = trim($Query);
        $queries = explode(";",$str);
        $result = array();
        $queryTypes =  array('SELECT','INSERT','UPDATE','DELETE','REPLACE','SET','DROP');

        foreach($queries as $query) {
            $position = array();
            foreach ($queryTypes as $string) {
                $pos = strpos($query, $string);
                if($pos !== false) {
                    $position[$string] = $pos;
                }
            }
            asort($position);
            reset($position);
            $result[] = key($position);   
        }    
        return $result;
    }

    function getAllRoles()
    {
        if ($this->verifyPermission("SELECT * FROM permission")->ok==True)
        {
            $result=$this->db->query("SELECT * FROM permission")->fetchAll();
            if (sizeof($result)>0)
            {
                $this->response->ok=True;
                $this->response->result=$result;
                return($this->response);
    
            }
            
            
        }
        else
        {
            $this->response->ok=False;
            return($this->response);
        }


    }

    function getAllTable()
    {
    
            
                $this->response->ok=True;
                $this->response->result=$this->table;
                return($this->response);
    
            
    }


    


    function verifyPermission($query)
    {
        
       
        $queryType=$this->GetQueryType($query)[0];
        if ($queryType=="INSERT")
        {
            $target=(explode(' ',$query)[2]); 
        }
        else if ($queryType=="SELECT")
        {
            $target=(explode(' ',$query)[3]);
        }
        else if ($queryType=="DELETE")
        {
            $target=(explode(' ',$query)[2]);
        }
        
        $roles=$this->roles;
        foreach($roles as $role)
        {
            if (($role["roles"])==$this->userRole)
            {
                
                if (in_array(str_replace(" ","",$target),json_decode($role["entity"])->$queryType))
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

        $this->response->ok=False;
        return($this->response);
    }

    function rolesDelete($roles)
    {
        
        if ($this->verifyPermission("DELETE FROM permission WHERE roles=?")->ok==True)
        {
            foreach ($roles as $role) {
                $role=json_decode($role);
                $result=$this->db->query("DELETE FROM permission WHERE roles=?",$role->roles);
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
    
    function rolesAdd($roles)
    {
        $roles=json_decode($roles);
        if ($this->verifyPermission("INSERT INTO permission (roles,pass,username,email) VALUES (?,?,?,?)")->ok==True)
        {
            
            $permArray=new StdClass(); 
            foreach($this->method as $permission)
            {
                $arrayPerm=[];
                foreach($this->table as $table)
                {
                    if (isset($roles->checkBox->$permission->$table)&&$roles->checkBox->$permission->$table)
                    {
                        array_push($arrayPerm, $table );
                    }
                    else
                    {

                    }
                    
                }

                $permArray->$permission=$arrayPerm;
                
            }
                $result=$this->db->query("INSERT INTO permission (roles,path,entity) VALUES (?,?,?)",$roles->roles,'["/","/_error","/user","/roles"]',json_encode($permArray));
                if ($result->affectedRows()>0)
                {
                   
                    $this->response->ok=True;
                    $this->response->result=$permArray;
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
}

?>