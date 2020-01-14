<?php
include('database.php');
include('captcha.php');

if(isset($_POST['register']) && isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['email']) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['address'])
&& isset($_POST['contactnum'])){

  $username = $_POST['username'];
  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];
  $email = $_POST['email'];
  $fname = $_POST['firstname'];
  $lname = $_POST['lastname'];
  $address = $_POST['address'];
  $contact = $_POST['contactnum'];

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

  if(!Database::query('SELECT Username from r_users WHERE Username=:username', array(':username'=>$username)) && preg_match('/^(?=.{5,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/', $username) && strlen($username) >=5 && strlen($username) <= 32){
    if(preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $fname) && preg_match('~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u', $lname) && strlen($fname) >=2 && strlen($lname) >=2 && strlen($fname) <=30 && strlen($lname) <=30){
      if(filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) >=5 && strlen($email) <= 50 && !Database::query('SELECT Email from r_users WHERE Email=:email', array(':email'=>$email))){
        if(strlen($contact) >=8 && strlen($contact) <=10 && preg_match('/^[0-9]*$/', $contact) && !Database::query('SELECT Contact_Number from r_users WHERE Contact_Number=:contactnum', array(':contactnum'=>$contact))){
          if(strlen($address) >= 10 && strlen($address) <= 500 && preg_match('/[#.0-9a-zA-Z\s,-]+$/', $address)){
            if(strlen($password1) >= 12 && $password1==$password2 && strlen($password1) <= 500 && preg_match("/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{12,500}$/", $password1)){

      Database::query('INSERT INTO r_users VALUES (:id, :username, :password, :email, :firstname, :lastname, :address, :contactnum, :role)',
      array(':id'=>'',':username'=>$username, ':password'=>password_hash($password1, PASSWORD_ARGON2ID, ['memory_cost' => 2048, 'time_cost' => 50, 'threads' => 50]),
      ':email'=>$email, ':firstname'=>$fname, ':lastname'=>$lname, ':address'=>$address, ':contactnum'=>$contact, ':role'=>1));
      mkdir("../users/U_".$username, 0755, true);
      copy("../assets/img/img5.jpg", "../users/U_".$username."/face");

      header("Location: ../login.php");

    }
    else{

      header("Location: ../register.php?error=pwerr&username=".$username."&fname=".$fname."&lname=".$lname."&email=".$email."&contact=".$contact."&address=".$address);
    }
    }
    else{

      header("Location: ../register.php?error=adderr&username=".$username."&fname=".$fname."&lname=".$lname."&email=".$email."&contact=".$contact);
    }
      }
      else{

      header("Location: ../register.php?error=conerr&username=".$username."&fname=".$fname."&lname=".$lname."&email=".$email."&address=".$address);
      }
    }
      else{

        header("Location: ../register.php?error=emlerr&username=".$username."&fname=".$fname."&lname=".$lname."&contact=".$contact."&address=".$address);
      }

    }
    else{

      header("Location: ../register.php?error=fnlnerr&email=".$email."&username=".$username."&contact=".$contact."&address=".$address);
    }

}
      else{

            header("Location: ../register.php?error=unerr&email=".$email."&fname=".$fname."&lname=".$lname."&contact=".$contact."&address=".$address);
        }
}
else{
  header("Location: ../register.php");
}
}
else{
  header("Location: ../register.php");
}
}
else{

  header("Location: ../index.php");
}
?>
