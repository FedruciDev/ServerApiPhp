<?php


class Product
{

    public function __construct () 
    {
        
        $this->db=new db();
        $this->response=new StdClass();
        $this->response->ok=False;
        $this->response->result=[];
        $this->perms=new Permission();
    }

    public function addProduct(){}

    public function deleteProduct()
    {}

    public function getAllProduct()
    {
        if ($this->perms->verifyPermission("SELECT * FROM prodotti")->ok==True)
        {
            $result=$this->db->query("SELECT * FROM prodotti")->fetchAll();
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
    public function getProduct($id)
    {
        if ($this->perms->verifyPermission("SELECT * FROM prodotti WHERE id=?",$id)->ok==True)
        {
            $result=$this->db->query("SELECT * FROM prodotti WHERE id=?",$id)->fetchAll();
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
}


?>