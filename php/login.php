<?php
include('checkLoggedIn.php');

if(isset($_POST['captcha'])){
  $captcha = Captcha::createRequest($_POST['captcha']);
  if($captcha[0] == true && $captcha[1] >= 0.0){

if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])){

$username = $_POST['username'];
$password = $_POST['password'];

if(Database::query('SELECT Username FROM r_users WHERE Username=:username', array(':username'=>$username))){

  if(password_verify($password, Database::query('SELECT Password FROM r_users WHERE Username=:username', array(':username'=>$username))[0]['Password'])){

    if(!checkLoggedIn::isLoggedIn()){


      $strong = True;
      $cookie = bin2hex(openssl_random_pseudo_bytes(500, $strong));
      $cookieECC = eccEncrypt::run(array('e', $cookie));
      $id = Database::query('SELECT ID FROM r_users WHERE Username=:username', array(':username'=>$username))[0]['ID'];
      Database::query('INSERT INTO cookies VALUES (:id, :cookie, :userid)', array(':id'=>'', ':cookie'=>hash('sha3-512' , $cookie), ':userid'=>$id));
      setcookie("alacode_cookie", $cookieECC, ['expires'=>time() + 60 * 60 * 24 * 7, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
      setcookie("alacode_cookie_reset", -1, ['expires'=>time() + 60 * 60 * 24 * 3, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);

      header("Location: ../index.php");

  }
      else{
          if(isset($_COOKIE['alacode_cookie'])){
              Database::query('DELETE FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('e', $_COOKIE['alacode_cookie'])))));
            }
            setcookie('alacode_cookie', '-1', time()-604800);
            setcookie('alacode_cookie_reset', '1', time()-604800);

            $strong = True;
            $cookie = bin2hex(openssl_random_pseudo_bytes(500, $strong));
            $cookieECC = eccEncrypt::run(array('e', $cookie));
            $id = Database::query('SELECT ID FROM r_users WHERE Username=:username', array(':username'=>$username))[0]['ID'];
            Database::query('INSERT INTO cookies VALUES (:id, :cookie, :userid)', array(':id'=>'', ':cookie'=>hash('sha3-512' , $cookie), ':userid'=>$id));
            setcookie("alacode_cookie", $cookieECC, ['expires'=>time() + 60 * 60 * 24 * 7, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
            setcookie("alacode_cookie_reset", -1, ['expires'=>time() + 60 * 60 * 24 * 3, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);

            header("Location: ../index.php");
        }
}
    else{

        header("Location: ../login.php?error=iuop&username=".$username."");
      }
}
  else{

    header("Location: ../login.php?error=iuop&username=".$username."");
  }
}
else{

  header("Location: ../index.php");
}

}
else{
  header("Location: ../login.php");
}
}
else{
  header("Location: ../login.php");
}


?>
