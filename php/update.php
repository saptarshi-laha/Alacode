<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){

$userid = checkLoggedIn::isLoggedIn();

if(isset($_POST['captcha'])){
  $captcha = Captcha::createRequest($_POST['captcha']);
  if($captcha[0] == true && $captcha[1] >= 0.0){

if(isset($_POST['unemfnln']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['first_name']) && isset($_POST['last_name'])){

  $oldusername = Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $fname = $_POST['first_name'];
  $lname = $_POST['last_name'];

  if(Database::query('SELECT COUNT(Username) from r_users WHERE Username=:username', array(':username'=>$username))[0]['COUNT(Username)'] <=1 && preg_match('/^[a-zA-Z0-9_.]*$/', $username) && strlen($username) >=5 && strlen($username) <= 32){
    if(preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $fname) && preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $lname) && strlen($fname) >=2 && strlen($lname) >=2 && strlen($fname) <=30 && strlen($lname) <=30){
      if(filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) >=5 && strlen($email) <= 50 && Database::query('SELECT COUNT(Email) from r_users WHERE Email=:email', array(':email'=>$email))[0]['COUNT(Email)'] <= 1){

        Database::query('UPDATE r_users SET Username=:username, Email=:email, First_Name=:first_name, Last_Name=:last_name WHERE ID=:id',
        array(':username'=>$username, ':email'=>$email, ':first_name'=>$fname, ':last_name'=>$lname, ':id'=>$userid));
        if($oldusername != $username){
        mkdir("../users/U_".$username, 0755, true);
        recurse_copy("../users/U_".$oldusername."/","../users/U_".$username."/");
        delete_files("../users/U_".$oldusername."/");
        }

        header("Location: ../dashboard.php?id=".$userid);

      }
      else{
        header("Location: ../dashboard.php?id=".$userid."&error1=unemfnln");
      }

    }
    else{
      header("Location: ../dashboard.php?id=".$userid."&error1=unemfnln");
    }

  }
  else{
    header("Location: ../dashboard.php?id=".$userid."&error1=unemfnln");
  }

}
else if(isset($_POST['coadd']) && isset($_POST['address']) && isset($_POST['contact'])){

  $address = $_POST['address'];
  $contact = $_POST['contact'];

  if(strlen($contact) >=8 && strlen($contact) <=10 && preg_match('/^[0-9]*$/', $contact) && Database::query('SELECT COUNT(Contact_Number) from r_users WHERE Contact_Number=:contactnum', array(':contactnum'=>$contact))[0]['COUNT(Contact_Number)'] <= 1){
    if(strlen($address) >= 10 && strlen($address) <= 500 && preg_match('/[#.0-9a-zA-Z\s,-]+$/', $address)){

      Database::query('UPDATE r_users SET Address=:address, Contact_Number=:contact WHERE ID=:id',
      array(':address'=>$address, ':contact'=>$contact, ':id'=>$userid));

      header("Location: ../dashboard.php?id=".$userid);

    }
    else{
      header("Location: ../dashboard.php?id=".$userid."&error2=coadd");
    }

  }
  else{
    header("Location: ../dashboard.php?id=".$userid."&error2=coadd");
  }

}
else if(isset($_POST['project']) && isset($_POST['proj_name'])){
  $username = Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'];
  $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname AND Member_ID = :mid', array(':cid'=>$userid, ':pname'=>$_POST['proj_name'], ':mid'=>$userid));
  if(preg_match('/^(?=.{5,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/', $_POST['proj_name']) && strlen($_POST['proj_name']) >=5 && strlen($_POST['proj_name']) <= 32 && !file_exists("../users/U_".$username."/".$_POST['proj_name']) && count($result) == 0){
    mkdir("../users/U_".$username."/".$_POST['proj_name'], 0755, true);
    Database::query('INSERT INTO projects VALUES (:id, :cid, :pname, :mid, :role)', array(':id'=>'', ':cid'=>$userid, ':pname'=>$_POST['proj_name'], ':mid'=>$userid, ':role'=>-1));
    header("Location: ../dashboard.php?id=".$userid);
  }
  else{
    header("Location: ../dashboard.php?id=".$userid."&error3=proj");
  }

}
else{

  header("Location: ../dashboard.php?id=".$userid);

}

}
else {
  header("Location: ../dashboard.php?id=".$userid);
}
}
else{
  header("Location: ../dashboard.php?id=".$userid);
}
}

else{

header("Location: ./login.php");

}


function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK );

        foreach( $files as $file ){
            delete_files( $file );
        }

        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );
    }
}

?>
