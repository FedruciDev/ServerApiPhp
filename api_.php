<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("method/db.php");
header('Access-Control-Allow-Origin: *');







function verifytoken($token)
{
  $db =new db();

  $result=$db->query("SELECT username FROM sessioni WHERE token=?",$token)->fetchArray();
  $response = new stdClass();
  if (count($result)==1)
  {
    $username=$result['username'];
    $result=$db->query("SELECT username,img,img_back FROM admin WHERE username=?",$username)->fetchArray();
    $response->data = $result;
    $response->result = true;
    return ($response);
  }
  else
  {
    $response->data = null;
    $response->result = false;
    return ($response);
    
  }
  
    
  
}

function rmsession($token)
{
  $db =new db();

  $result=$db->query('DELETE FROM sessioni WHERE token=?',$token);
  
  if ($result->affectedRows()==1)
  {
    
    return true;
    
  }
  else
  {
    return false;
  }
  
    
  
}

function upload_files($files,$path)
{
  $paths=array();
  $counter=0;
  
  foreach($files["tmp_name"] as $img )
  {
    $img_= $files["name"][$counter];
    $ext= pathinfo($img_, PATHINFO_EXTENSION);
    
    $path_destination=$path."/".$counter.".".$ext;
    $exist=false;
   
      if(!is_dir($path))
      {
        if (mkdir($path))
        {
          if (move_uploaded_file($img,$path_destination))
          {
            array_push($paths,$path_destination);
            
          }
          else
          {
            return false;
          }
        }
        else
        {
          return false;
        }
       
      }
      else
      {
        if (move_uploaded_file($img,$path_destination))
        {
          array_push($paths,$path_destination);
          
        }
        else
        {
          return false;
        }
      }
    
   

    $counter++;
  }
  return $paths;
  
}

function upload_file($file,$path,$name)
{
 
  if(!is_dir($path))
  {
    if (mkdir($path))
    {
      $img_= $file["name"];
      $ext= pathinfo($img_, PATHINFO_EXTENSION);
      $path_destination=$path."/".$name.".".$ext;
      if (move_uploaded_file($file["tmp_name"],$path."/".$name.".".$ext))
      {
        return $path_destination;
        
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
   
  }
  else
  {
    $img_= $file["name"];
    $ext= pathinfo($img_, PATHINFO_EXTENSION);
    $path_destination=$path."/".$name.".".$ext;
    if (move_uploaded_file($file["tmp_name"],$path."/".$name.".".$ext))
    {
      return $path_destination;
      
    }
    else
    {
      return false;
    }
  }
}

function rrmdir($dir) { 
  if (is_dir($dir)) { 
    $objects = scandir($dir); 
    foreach ($objects as $object) { 
      if ($object != "." && $object != "..") { 
        if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
      } 
    } 
    reset($objects); 
    rmdir($dir); 
  } 
} 

if(isset($_POST["action"]))
{
  $action=$_POST["action"];
  $db =new db();

  $response = new stdClass();
  $response->esito = false;
  $response->result = null;
  $response->message = "";

  if (isset($_POST["token"]))
  {
    $token=$_POST["token"];
    $tokenverify=verifytoken($token);
    if($action=="verify")
    {
      if ($token!=null)
      {
        
        if ($tokenverify->result==true)
      {
        
        $response->esito = true;
        $response->result = $tokenverify->data;
        $response->message = "Valido!";
      }
      else
      {
        $response->esito = false;
        $response->result = null;
        $response->message = "Non valido!";
        $response->logout = true;
      }
      }
    }
    else if ($token!=null&&$tokenverify->result==true)
    {
      
      if ($action=="logout")
      {
        
        if(rmsession($token)==true)
        {
          $response->esito = true;
          $response->result = null;
          $response->message = "Success!";
        }
        else
        {
          $response->esito = false;
          $response->result = null;
          $response->message = "Error!";
        }
      }
      
      elseif($action=="product")
      {
        $result=$db->query("SELECT * FROM prodotti")->fetchAll();        
        
        $response->esito = true;
        $response->result = $result;
        $response->message = "";
      
        
      }
      elseif($action=="category")
      {
        $result=$db->query("SELECT * FROM categorie")->fetchAll();        
        
        $response->esito = true;
        $response->result = $result;
        $response->message = "";
      
        
      }
      elseif($action=="imgChangeAdmin")
      {
        $username=$_POST["user"];
        $old_img=$_POST["old_img"];
        if (unlink("../profile/profile-pic/".$username."/".$old_img)&&move_uploaded_file($_FILES["file"]["tmp_name"],"../profile/profile-pic/".$username."/".$_FILES["file"]["name"]))
        {
          $result=$db->query('UPDATE admin SET img=? WHERE username=?',$_FILES["file"]["name"],$username);
       
          if ($result->affectedRows()==1)
          {
             
            $response->esito = true;
            $response->result = null;
            $response->message = "Immagine cambiata!";
             
          }
          else
          {
            $response->esito = false;
            $response->result = null;
            $response->message = "Error!";
          }
        
          
        }
      
        
      }
      elseif($action=="deleteProduct")
      {
          $product=$_POST["product"];
          $result=$db->query('DELETE FROM prodotti WHERE id=?',$product);
       
          if ($result->affectedRows()==1)
          {
             
            $response->esito = true;
            $response->result = null;
            $response->message = "Prodotto eliminato!";
             
          }
          else
          {
            $response->message = "Error!";
          }
        
          
      
        
      }
      elseif($action=="addProduct")
      {
        $name=$_POST["name"];
        $title=$_POST["title"];
        $descr=$_POST["description"];
        $category=$_POST["category"];
        $id=rand(1111111,9999999);
        $path="../item/product/".$id;
        $result_files=upload_files($_FILES["img"],$path);
        $result_file=upload_file($_FILES["img_min"],$path,"miniature");
        if ($result_files!=false && $result_files!=false)
        {
          $files=new StdClass();
          $files->file=$result_files;
          $files->miniature=$result_file;
          $files=json_encode($files);
          $result=$db->query('INSERT INTO Prodotti (nome,id,descrizione,id_cat,titolo,file_json) VALUES (?,?,?,?,?,?)',$name,$id,$descr,intval($category),$title,$files);
       
          if ($result->affectedRows()==1)
          {
            $response->esito = true;
            $response->result = null;
            $response->message = "Prodotto aggiunto!";
             
          }
          else
          {
            $response->message="Insert problem!";
          }
          
        }
        else
        {
          $response->message="File problem!";

        }
        
        
      }
      elseif($action=="editProduct")
      {
        $product_id=$_POST["id"];
        $name=$_POST["name"];
        $title=$_POST["title"];
        $descr=$_POST["description"];
        $category=$_POST["category"];
        $result=$db->query("SELECT * FROM Prodotti WHERE id=?",$product_id)->fetchArray();
        $file_old=$result["file_json"];
        $file_old=json_decode($file_old);
        $file_old_array=array();
        //se aggiunge immagine min
        if(isset($_POST["img_min_old"]) && isset($_FILES["img_min"]))
        {
          
          if (unlink($file_old->miniature))
          {
            print("yes");
          }
        
        }
        //-se aggiunge immagine min

        //se aggiunge immagini
        if(isset($_FILES["img"]))
        {
          $last=0;
          //ottengo nome ultima foto
            foreach($file_old_array as $file)
            {
              
              $ext= pathinfo(basename($file), PATHINFO_EXTENSION);
              if(basename($file,".".$ext)>$last)
              {
                $last=basename($file,".".$ext);
              }
              

            }
          //- fine ottengo nome utima foto

            $counter=0;
            $except=true;
            // carico file su server
            foreach($_FILES["img"]["tmp_name"] as $img)
            {
              $ext= pathinfo($_FILES["img"]["name"][$counter], PATHINFO_EXTENSION);
              $path="../item/product/".$product_id."/".strval($last+1).".".$ext;

              if (move_uploaded_file($img,$path))
              {
                array_push($file_old->file,$path);
              }
              else
              {
                $except=false;
              }

              $counter=$counter+1;
              $last=intval($last)+1;
             
            }
            //- carico file su server

          

          
        }
        //-se aggiunge immagini
        
        $result=$db->query('UPDATE Prodotti SET file_json=?,nome=?,id_cat=?,titolo=?,descrizione=? WHERE id=?',json_encode($file_old),$name,$category,$title,$descr,$product_id);
       
        if ($result->affectedRows()==1)
        {
          $response->esito = true;
          $response->result = null;
          $response->message = "Prodotto modificato!";
           
        }
        else
        {

          $response->esito = false;
          $response->result = null;
          $response->message = "Errore, dati non cambiati!";
          
        }

        
      }
      elseif($action=="imgChangeBack")
      {
        $username=$_POST["user"];
        $old_img=$_POST["old_img"];
        if (unlink("../profile/background-profile-pic/".$username."/".$old_img)&&move_uploaded_file($_FILES["file"]["tmp_name"],"../profile/background-profile-pic/".$username."/".$_FILES["file"]["name"]))
        {
          $result=$db->query('UPDATE admin SET img_back=? WHERE username=?',$_FILES["file"]["name"],$username);
       
          if ($result->affectedRows()==1)
          {
             
            $response->esito = true;
            $response->result = null;
            $response->message = "Immagine cambiata!";
             
          }
          else
          {
            $response->esito = false;
            $response->result = null;
            $response->message = "Error!";
          }
        
          
        }
      
        
      }
    elseif($action=="changePassword")
      {
        $username=$_POST["user"];
        $new_pass=$_POST["new_pass"];
        $current=$_POST["current_pass"];
        $result=$db->query('UPDATE admin SET pass=? WHERE username=? AND pass=?',md5($new_pass),$username,md5($current));
      
        if ($result->affectedRows()==1)
        {
            
          $response->esito = true;
          $response->result = null;
          $response->message = "Password cambiata!";
            
        }
        else
        {
          $response->esito = false;
          $response->result = null;
          $response->message = "Errore!";
        }
      
        
      }
      
        
      
    }
   
    
    
  }
  else
  {
    if ($action=="login")
    {
      $pass=$_POST['password'];
      $user=$_POST['username'];
      $pass=md5($pass);

      $result=$db->query('SELECT * FROM admin WHERE username=? AND pass=?',$user,$pass)->fetchAll();
      if (count($result)==1)
      {
          $token = openssl_random_pseudo_bytes(16);
          $token = bin2hex($token);  
          $result=$db->query('INSERT INTO sessioni (token,username) VALUES (?,?)',$token,$user);
       
          if ($result->affectedRows()==1)
          {
             
              $response->esito = true;
              $response->result = $token;
              $response->message = "Success!";
             
          }
          else
          {
            $response->esito = false;
            $response->result = null;
            $response->message = "Error!";
          }
      }
      else
      {
   
          $response->esito = false;
          $response->result = null;
          $response->message = "Invalid credendial!";
          
      }
    }
    
  }

  print_r(json_encode($response));
}
    
  