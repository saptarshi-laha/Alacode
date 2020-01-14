<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

$userid = checkLoggedIn::isLoggedIn();
if(isset($_POST['codearea']) && isset($_POST['fileid'])){

  $fileData = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['fileid']));
  if(count($fileData) == 1){
  $found = 0;
  $memberData = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$fileData[0]['Creator_ID'], ':pname'=>$fileData[0]['Project_Name']));
  for($i = 0; $i<count($memberData); $i++){
    if($memberData[$i]['Member_ID'] == $userid){
      if($memberData[$i]['Role'] == -1){
        $found = -1;
      }
      else if($memberData[$i]['Role'] == 0){
        $found = 0;
      }
      else if($memberData[$i]['Role'] == 1){
        $found = 1;
      }
      else{
        $found = 2;
      }
    }
  }

  if($userid != $fileData[0]['Member_ID'] && $userid != $fileData[0]['Creator_ID'] && $found == 2){
    header("Location: login.php");
  }

  $path = '../users/U_'.Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$fileData[0]['Creator_ID']))[0]['Username'].'/'.$fileData[0]['Project_Name'].'/'.$fileData[0]['Filename'].'.txt';
  if(($found == -1 && $userid == $fileData[0]['Creator_ID'] && $fileData[0]['Creator_ID'] == $fileData[0]['Member_ID']) || ($found == -1 && $fileData[0]['Creator_ID'] != $fileData[0]['Member_ID'])  ||
  ($found == 1 && $userid == $fileData[0]['Member_ID']) || ($found == 0 && $userid == $fileData[0]['Member_ID'])){

    if(file_exists($path)){
    file_put_contents($path, $_POST['codearea'], LOCK_EX);
    }

    header("Location: ../code.php?id=".$_POST['fileid']."&method=edit#default");

  }
}
else{
  header("Location: ../dashboard.php");
}
}
else{
    header("Location: ../dashboard.php");
}
}
else{
  header("Location: ../code.php?id=".$_POST['fileid']."&method=edit#default");
}
}
else{
  header("Location: ../code.php?id=".$_POST['fileid']."&method=edit#default");
}
}
else{
  header("Location: ../login.php");
}

?>
