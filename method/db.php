<?php
ini_set('display_errors',1);
error_reporting(E_ALL);




class db
{
    public $query;
    public $conn;
    public $server = "localhost";
    public $user = "root";
    public $pass = '';
    public $dbname="portale";

    public function __construct () {
      $this->conn=new mysqli($this->server,$this->user,$this->pass,$this->dbname);

      
    
    }

    function verify($query)
    {
        $result = $this->conn->prepare($query);
        if ($result->num_rows > 0) {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    function query($query)
    {

        if ($this->query = $this->conn->prepare($query)){
            if (func_num_args()>1)
            {
                $args = func_get_args();
                $args=array_slice($args,1);
                $args_ref= array();
                $types='';
                foreach($args as $k => &$arg)
                {
                    if (is_array($args[$k]))
                    {
                        foreach($args[$k] as $j => &$a)
                        {
                            $args_ref[]=&$a;
                            $types .= _gettype($args[$j]);
                        }
                    }
                    else
                    {
                        $args_ref[]=&$arg;
                        $types .= $this->_gettype($args[$k]);

                    }
                }
                array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }   
            
            
            $this->query->execute();
            
            return $this;

        }   

       
        

    }
    
 
    
    public function fetchArray() {
    
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }
        $this->query->close();
        $this->query_closed = TRUE;
        
        return $result;
    
	    
	}

    public function affectedRows() {
		return $this->query->affected_rows;
	}
    public function fetchAll($callback = null) {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break') break;
            } else {
                $result[] = $r;
            }
        }
        $this->query->close();
        $this->query_closed = TRUE;
		return $result;
	}
    
    function close()
    {
        $this->conn->close();
        return;     
    
    }
    private function _gettype($var) {
	    if (is_string($var)) return 's';
	    if (is_float($var)) return 'd';
	    if (is_int($var)) return 'i';
	    return 'b';
	}

}




?> 



