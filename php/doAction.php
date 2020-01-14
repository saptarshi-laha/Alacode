<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){
  $userid = checkLoggedIn::isLoggedIn();
  $username = Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'];

  if(isset($_POST['captcha']) && isset($_POST['search'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){
  if(isset($_POST['M']) && !isset($_POST['P'])){
    $otheruserid = $_POST['M'];
    header("Location: ../profile.php?id=".$otheruserid);
  }
  else if(isset($_POST['P']) && isset($_POST['S']) && $_POST['S']!='' && !isset($_POST['M'])){
    $otheruserid = $_POST['P'];
    $projectname = $_POST['S'];
    $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :id1 AND Project_Name = :proj AND Member_ID = :id2', array(':id1'=>$userid, ':proj'=>$projectname, ':id2'=>$userid));
    $foundproj = 0;
    for($i = 0; $i < count($result); $i++){
      if($projectname == $result[0]['Project_Name']){
        $foundproj = 1;
      }
    }
    if($foundproj == 1){
      $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :id1 AND Project_Name = :proj AND Member_ID = :id2', array(':id1'=>$userid, ':proj'=>$projectname, ':id2'=>$otheruserid));
      if(count($result) == 0){
        $alert = "You have been invited to contribute to project ".$projectname." by ".$username.";accept=no";
        $exists = Database::query('SELECT COUNT(Alert) FROM alerts WHERE Alert = :alert AND Receiver_ID = :recv AND Sender_ID = :crea', array(':alert'=>$alert, ':recv'=>$otheruserid, ':crea'=>$userid))[0]['COUNT(Alert)'];
        if($exists == 0){
        Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid,':rid'=>$otheruserid, ':newa'=>1, ':alertX'=>$alert));
        }
        header("Location: ../search.php?search=".$_POST['search']);
      }
      else{
        header("Location: ../search.php?search=".$_POST['search']);
      }
    }
    else{
      header("Location: ../search.php?search=".$_POST['search']);
    }

  }
  else{
    header("Location: ../search.php?search=".$_POST['search']);
  }
}
else{
  header("Location: ../search.php?search=".$_POST['search']);
}
}
else{
  header("Location: ../search.php?search=".$_POST['search']);
}
}
else{
  header("Location: ../login.php");
}

?>
