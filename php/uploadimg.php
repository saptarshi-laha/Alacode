<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

$userid = checkLoggedIn::isLoggedin();

$file = $_FILES['img'];

$fileName = $_FILES['img']['name'];
$fileSize = $_FILES['img']['size'];
$fileError = $_FILES['img']['error'];
$fileTmp = $_FILES['img']['tmp_name'];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['img']['tmp_name']);
finfo_close($finfo);

if($mime == 'image/jpeg'){

  if($fileError == 0){

    if($fileSize > 0 && $fileSize <= (1024 * 1024 * 10)){

      unlink("../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']."/face");
      $target_dir = "../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']."/";
      $target_file = $target_dir . basename($fileName);
      move_uploaded_file($fileTmp, $target_file);
      rename("../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']."/".$fileName, "../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']."/face");
      header("Location: ../dashboard.php?id=".checkLoggedIn::isLoggedIn());
    }
    else{
      header("Location: ../dashboard.php?id=".$userid."&error=img");
    }

  }
  else{
    header("Location: ../dashboard.php?id=".$userid."&error=img");
  }

}
else{
    header("Location: ../dashboard.php?id=".$userid."&error=img");
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
