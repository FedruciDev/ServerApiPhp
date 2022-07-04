<?php 
header('Access-Control-Allow-Origin: *');
include ("method/db.php");
include ("method/permission.php");
include("method/Response/response.php");
include("method/Response/success_response.php");
include("method/Response/failed_response.php");

try
{
    
    if (!empty($_POST) && !empty($_POST["action"]) )
    {
        if (!empty($_POST["token"]))
        {
            $token=$_POST["token"];
            $auth=new Permission();
            if ($auth->verify_token($token)->ok==true)
            {
                $action=$_POST["action"];
                if ($action="get_traffic")
                {
                    
                }
                else
                {
                    $response=new Failed();
                    $response->Send();
                }
            }
            

        }
    }
} catch(Exception $e){
    $response=new Failed();
    $response->Send();
}




?>