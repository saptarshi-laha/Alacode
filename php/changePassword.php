<?php
include('checkLoggedIn.php');

if(isset($_POST['captcha'])){
  $captcha = Captcha::createRequest($_POST['captcha']);
  if($captcha[0] == true && $captcha[1] >= 0.0){
if(isset($_POST['cpwd']) && isset($_POST['curpass']) && isset($_POST['newpass1']) && isset($_POST['newpass2'])){

        if(checkLoggedIn::isLoggedIn() && password_verify($_POST['curpass'], Database::query('SELECT Password FROM r_users WHERE ID=:userid', array(':userid'=>checkLoggedIn::isloggedIn()))[0]['Password']) &&
        $_POST['newpass1'] == $_POST['newpass2'] && strlen($_POST['newpass1']) >= 12 && strlen($_POST['newpass1']) <= 500 && preg_match("/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{12,500}$/", $_POST['newpass1'])){
            $id = checkLoggedIn::isLoggedIn();
            Database::query('UPDATE r_users SET Password=:password WHERE ID=:userid', array(':password'=>password_hash($_POST['newpass1'],PASSWORD_ARGON2ID, ['memory_cost' => 2048, 'time_cost' => 50, 'threads' => 50]), ':userid'=>$id));
            Database::query('DELETE FROM cookies WHERE User_ID=:userid', array(':userid'=>$id));
            setcookie('alacode_cookie', '-1', time()-604800);
            setcookie('alacode_cookie_reset', '1', time()-604800);
            header("Location: ../login.php");
        }
        else{
          header("Location: ../dashboard.php?id=".checkLoggedIn::isLoggedIn());
        }
}
else{
  header("Location: ../dashboard.php?id=".checkLoggedIn::isLoggedIn());
}
}
else{
  header("Location: ../dashboard.php?id=".checkLoggedIn::isLoggedIn());
}
}
else{
  header("Location: ../dashboard.php?id=".checkLoggedIn::isLoggedIn());
}

?>
