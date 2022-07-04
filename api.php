<?php 
header('Access-Control-Allow-Origin: *');
include ("method/db.php");
include("method/login.php");
include("method/user.php");
include("method/permission.php");
include("method/product.php");
include("method/Response/response.php");
include("method/Response/success_response.php");
include("method/Response/failed_response.php");

try
{
    
    if (!empty($_POST) && !empty($_POST["action"]) )
    {
        $perms=new Permission();
        $action=$_POST["action"];
        if (!empty($_POST["auth"]))
        {
            $authValue=json_decode($_POST["auth"]);
            $authVerify=new Login();
            $authVerify=$authVerify->authverify($authValue->email,$authValue->token);
            
            if ($authVerify->ok==True)
            {
                if ($action=="usersGet")
                {
                    $user=new User();
                    $users=$user->allUserGet();
                    if ($users->ok==true)
                    {
                        $response=new Success();
                        $response->data=$users->result;
                        $response->Send(); 
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }
                } 
                if ($action=="getAllRoles")
                {
                    
                    $roles=$perms->getAllRoles();
                    if ($roles->ok==true)
                    {
                        $response=new Success();
                        $response->data=$roles->result;
                        $response->Send(); 
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }
                }    
                else if ($action=="usersDelete")
                {
                    $user=new User();
                    
                    $result=$user->usersDelete(json_decode($_POST["value"]));
                    if($result->ok==true)
                    {
                        $response=new Success();
                        $response->Send();
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }

                }
                else if ($action=="getAllTable")
                {
                        $result=$perms->getAllTable();
                        $response=new Success();
                        $response->data=$result->result;
                        $response->Send();
                   

                }
                else if ($action=="rolesDelete")
                {
                    
                    
                    $result=$perms->rolesDelete(json_decode($_POST["value"]));
                    if($result->ok==true)
                    {
                        $response=new Success();
                        $response->Send();
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }

                }
                else if ($action=="userAdd")
                {

                    $user=new User();
                    
                    $result=$user->userAdd(($_POST["value"]));
                    if($result->ok==true)
                    {
                        $response=new Success();
                        $response->Send();
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }

                }
                else if ($action=="rolesAdd")
                {

                    
                    
                    $result=$perms->rolesAdd(($_POST["value"]));
                    if($result->ok==true)
                    {
                        $response=new Success();
                        $response->Send();
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }

                }
                else if ($action=="getUserRoles")
                {
                    $user=new User();
                    $result=$user->getUserRoles();
                    if($result->ok==true)
                    {
                        $response=new Success();
                        $response->data=$result->result;
                        $response->Send();
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }

                }

                //-------------
                //prodotti
                //-------------
                
                if ($action=="getAllProduct")
                {
                    
                    $product=new Product();
                    $products=$product->getAllProduct();
                    if ($products->ok==true)
                    {
                        $response=new Success();
                        $response->data=$products->result;
                        $response->Send(); 
                    }
                    else
                    {
                        $response=new Failed();
                        $response->Send();
                    }
                } 

            }

            else
            {
                $response=new Failed();
                $response->message="Permission denied";
                $response->Send();
            }
        
        }
        else
        {
            if ($_POST["action"]=="login")
            {
                if (!empty($_POST["values"]))
                    {
                        $values=json_decode($_POST["values"]);
                        $password=$values->password;
                        $email=$values->email;
                        $login=new Login();
                        $check=$login->login($email,$password);
                        
                        if ($check->ok==True)
                        {
                            
                            $response=new Success();
                            $response->data=$check->result;
                            $response->Send();
                        
                        }
                        else
                        {
                            $response=new Failed();
                            $response->message="Invalid credential";
                            $response->Send();
                        }
                    }
                    else
                    {
                        
                    }
            }  
        
            if ($_POST["action"]=="authverify")
            {
                if (!empty($_POST["values"]))
                {
                    $values=json_decode($_POST["values"]);
                    if (isset($values->token) && isset($values->email))
                    {
                        $token=$values->token;
                        $email=$values->email;
                        $login=new Login();
                        $check=$login->authverify($email,$token);
        
                        if ($check->ok==True)
                        {
                            $response=new Success();
                            $response->data=$check->result;
                            $response->message="Valid token";
                            $response->Send();
        
                        }
                        else
                        {
                            $response=new Failed();
                            $response->message="Invalid token";
                            $response->Send();
                        }
                    }
                    else
                    {
                        $response=new Failed();
                        $response->message="Invalid token";
                        $response->Send();
                    }
                    
                }
                else
                {
                    $response=new Failed();
                    $response->Send();
                }
            } 
                    
            if ($_POST["action"]=="logout")
            {
                if (!empty($_POST["values"]))
                {
                    $values=json_decode($_POST["values"]);
                    if (isset($values->token) && isset($values->email))
                    {
                        $token=$values->token;
                        $email=$values->email;
                        $login=new Login();
                        $result=$login->logout($email,$token);
                        if ($result->ok==True)
                        {
                            $response=new Success();
                            $response->data->email=$email;
                            $response->message="Valid token";
                            $response->Send();
                        }
                    }
                }
        
            }
        
            if ($_POST["action"]=="path_permission")
            {
                
                
                $path_perm=$perms->PathPermission();
                $response=new Success();
                $response->data=$path_perm->result;
                $response->Send();
        
            }
        }
    

        
            


        

    }
    else
    {
        $response=new Failed();
        $response->Send();
    }
} catch(Exception $e){
    $response=new Failed();
    $response->Send();
}




?>