<?php
include('checkLoggedIn.php');
include('PHPMailer.php');
include('SMTP.php');
include('Exception.php');
use PHPMailer\PHPMailer\PHPMailer;


if(isset($_POST['captcha']) || isset($_GET['token'])){
    if(isset($_POST['captcha'])){
  $captcha = Captcha::createRequest($_POST['captcha']);
    }
    else if(isset($_GET['token'])){
        $captcha = array();
        $captcha[] = true;
        $captcha[] = 0.9;
    }
  if($captcha[0] == true && $captcha[1] >= 0.7){

if(checkLoggedIn::isLoggedIn()){
  header("Location: ../index.php");
}
else{

  if(isset($_POST['email'])){

    $id = Database::query('SELECT * FROM r_users WHERE email = :email', array(':email'=>$_POST['email']));
    if(count($id) == 1){
      $strong = True;
      $token = bin2hex(openssl_random_pseudo_bytes(500, $strong));
      date_default_timezone_set("Asia/Bangkok");
      Database::query('INSERT INTO fpassword VALUES(:id, :rid, :email, :token, :valid)', array(':id'=>'', ':rid'=>$id[0]['ID'], ':email'=>$_POST['email'],':token'=>hash('sha3-512' , $token), ':valid'=>time()+60*60));




    $mail = new PHPMailer(true);

    try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'exploited.website@gmail.com';
    $mail->Password   = 'Exploited101Website';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('no-reply@exploited.website');
    $mail->addAddress($_POST['email']);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Token';
    $mail->Body    = 'The password reset token is only valid for 1 hour. Please click the link to reset your password. https://exploited.website/forgot.php?token='.$token;
    $mail->AltBody = 'The password reset token is only valid for 1 hour. Please click the link to reset your password. https://exploited.website/forgot.php?token='.$token;

    $mail->send();
    } catch (Exception $e) {
    header("Location: ../index.php");
    }
    header("Location: ../forgot.php?er=success");

    }
    else{
      header("Location: ../forgot.php?er=success");
    }
  }
  else if(isset($_GET['token'])){

    $exists = Database::query('SELECT * FROM fpassword WHERE Token = :token', array(':token'=>hash('sha3-512' ,$_GET['token'])));
    if(count($exists) == 1 && time() <= $exists[0]['Valid']){
    $id = $exists[0]['Reset_ID'];
    $newpassword = randomPassword();
    Database::query('UPDATE r_users SET Password = :pass WHERE ID = :id', array(':pass'=>password_hash($newpassword, PASSWORD_ARGON2ID, ['memory_cost' => 2048, 'time_cost' => 50, 'threads' => 50]), ':id'=>$id));

    $mail = new PHPMailer(true);

  try {
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'exploited.website@gmail.com';
  $mail->Password   = 'Exploited101Website';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port       = 465;

  $mail->setFrom('no-reply@exploited.website');
  $mail->addAddress($exists[0]['Email']);

  $mail->isHTML(true);
  $mail->Subject = 'Password Has Been Reset';
  $mail->Body    = 'Your new password is '.$newpassword.'. Please log in and change your password as soon as possible!';
  $mail->AltBody = 'Your new password is '.$newpassword.'. Please log in and change your password as soon as possible!';

  $mail->send();
  } catch (Exception $e) {
  header("Location: ../index.php");
  }

  Database::query('DELETE FROM fpassword WHERE Email = :email', array(':email'=>$exists[0]['Email']));
  header("Location: ../forgot.php?er=reset");

}else{
  header("Location: ../index.php");
}

  }
  else{
    header("Location: ../index.php");
  }

}

}
else{
header("Location: ../index.php");
}

}
else{
header("Location: ../index.php");

}



function randomPassword($len = 12) {
    $sets = array();
    $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    $sets[] = '0123456789';
    $sets[]  = '~!@#$%^&*(){}[],./?';

    $password = '';

    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
    }

    while(strlen($password) < $len) {
        $randomSet = $sets[array_rand($sets)];

        $password .= $randomSet[array_rand(str_split($randomSet))];
    }

    return str_shuffle($password);
}

 ?>
