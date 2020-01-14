<?php

include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

      if(isset($_POST['alldevices'])){

        Database::query('DELETE FROM cookies WHERE User_ID=:userid', array(':userid'=>checkLoggedIn::isLoggedIn()));
        setcookie("alacode_cookie", -1, ['expires'=>time() + 60 * 60 * 24 * 7, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
        setcookie("alacode_cookie_reset", 1, ['expires'=>time() + 60 * 60 * 24 * 3, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
        header("Location: ../index.php");

      }
      else if(isset($_POST['currentdevice'])){

          if(isset($_COOKIE['alacode_cookie'])){
            Database::query('DELETE FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('d', $_COOKIE['alacode_cookie'])))));
          }

          setcookie("alacode_cookie", -1, ['expires'=>time() + 60 * 60 * 24 * 7, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
          setcookie("alacode_cookie_reset", 1, ['expires'=>time() + 60 * 60 * 24 * 3, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
          header("Location: ../index.php");
    }
    else{
      header("Location: ../index.php");
    }

}
else{
  header("Location: ../index.php");
}
}
else{
  header("Location: ../index.php");
}
}
else{
  header("Location: ../index.php");
}

?>
