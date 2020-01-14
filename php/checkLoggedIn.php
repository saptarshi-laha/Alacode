<?php

include('database.php');
include('eccEncrypt.php');
include('captcha.php');

class checkLoggedIn{

public static function isLoggedIn(){

    if(isset($_COOKIE['alacode_cookie'])){
      if(Database::query('SELECT User_ID FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('d', $_COOKIE['alacode_cookie'])))))){
              if(isset($_COOKIE['alacode_cookie_reset'])){
                $id = Database::query('SELECT User_ID FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('d', $_COOKIE['alacode_cookie'])))))[0]['User_ID'];
                return $id;
              }
              else{
                $strong = True;
                $cookie = bin2hex(openssl_random_pseudo_bytes(500, $strong));
                $cookieECC = eccEncrypt::run(array('e', $cookie));
                $id = Database::query('SELECT User_ID FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('d', $_COOKIE['alacode_cookie'])))))[0]['User_ID'];
                Database::query('INSERT INTO cookies VALUES (:id, :cookie, :userid)', array(':id'=>'', ':cookie'=>hash('sha3-512' , $cookie), ':userid'=>$id));
                Database::query('DELETE FROM cookies WHERE Cookie=:cookie', array(':cookie'=>hash('sha3-512', eccEncrypt::run(array('d', $_COOKIE['alacode_cookie'])))));
                setcookie("alacode_cookie", $cookieECC, ['expires'=>time() + 60 * 60 * 24 * 7, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
                setcookie("alacode_cookie_reset", -1, ['expires'=>time() + 60 * 60 * 24 * 3, 'path'=>'/', 'domain'=>'exploited.website', 'httponly'=>TRUE, 'secure'=>TRUE, 'samesite'=>'Strict']);
                return $id;
              }
      }
    }

    return false;
 }

}
?>
