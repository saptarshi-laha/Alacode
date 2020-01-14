<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){
  $userid = checkLoggedIn::isLoggedIn();

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

  if(isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
    $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']));
    if(count($cid) == 1){
    $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Creator_ID'];
    $pname = Database::query('SELECT Project_Name FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Project_Name'];
    $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1', array(':cid1'=>$cid, ':pname1'=>$pname));
    $role1 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND Member_ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$userid));
    $role2 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$_POST['P']));
    if(count($role2) == 1 && $role2[0]['Member_ID'] != $userid && $_POST['P'] != $cid && $role1[0]['Role']== -1 &&  $role2[0]['Role'] == 1 && $userid != $cid){
      Database::query('UPDATE projects SET Role = :x WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid1', array(':x'=>($role2[0]['Role'] - 1), ':cid1'=>$cid, ':pname1'=>$pname, ':mid1'=>$_POST['P']));
      header("Location: ../project.php?id=".$_POST['projid']);
  }
    else if(count($role2) == 1 && $role2[0]['Member_ID']  != $userid && $userid == $cid && ($role2[0]['Role'] == 0 || $role2[0]['Role'] == 1)){
      Database::query('UPDATE projects SET Role = :x WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid1', array(':x'=>($role2[0]['Role'] - 1), ':cid1'=>$cid, ':pname1'=>$pname, ':mid1'=>$_POST['P']));
      header("Location: ../project.php?id=".$_POST['projid']);
    }
    else{
      header("Location: ../project.php?id=".$_POST['projid']);
    }
  }
  else{
    header("Location: ../index.php");
  }
  }
  else if(!isset($_POST['P']) && isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
      $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']));
      if(count($cid) == 1){
      $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Creator_ID'];
      $pname = Database::query('SELECT Project_Name FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Project_Name'];
      $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1', array(':cid1'=>$cid, ':pname1'=>$pname));
      $role1 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND Member_ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$userid));
      $role2 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$_POST['DE']));
      if(count($role2) == 1 && $role2[0]['Member_ID']  != $userid && $_POST['DE'] != $cid && $role1[0]['Role']== -1 && $role2[0]['Role'] == 0 && $userid != $cid){
        Database::query('UPDATE projects SET Role = :x WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid1', array(':x'=>($role2[0]['Role'] + 1), ':cid1'=>$cid, ':pname1'=>$pname, ':mid1'=>$_POST['DE']));
        header("Location: ../project.php?id=".$_POST['projid']);
      }
      else if(count($role2) == 1 && $role2[0]['Member_ID']  != $userid && $userid == $cid && ($role2[0]['Role'] == 0 || $role2[0]['Role'] == -1)){
        Database::query('UPDATE projects SET Role = :x WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid1', array(':x'=>($role2[0]['Role'] + 1), ':cid1'=>$cid, ':pname1'=>$pname, ':mid1'=>$_POST['DE']));
        header("Location: ../project.php?id=".$_POST['projid']);
      }
      else{
        header("Location: ../project.php?id=".$_POST['projid']);
      }
    }
    else{
      header("Location: ../index.php");
    }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
      $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']));
      if(count($cid) == 1){
      $cid = Database::query('SELECT Creator_ID FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Creator_ID'];
      $pname = Database::query('SELECT Project_Name FROM projects WHERE ID = :pid', array(':pid'=>$_POST['projid']))[0]['Project_Name'];
      $result = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1', array(':cid1'=>$cid, ':pname1'=>$pname));
      $role1 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND Member_ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$userid));
      $role2 = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid1 AND Project_Name = :pname1 AND ID = :mid', array(':cid1'=>$cid, ':pname1'=>$pname, ':mid'=>$_POST['RE']));
      if(count($role2) == 1 && $role2[0]['Member_ID'] != $userid && $_POST['RE'] != $cid && $role1[0]['Role']== -1 && ($role2[0]['Role'] == 0 || $role2[0]['Role'] == 1) && $userid != $cid){
        Database::query('DELETE FROM projects WHERE ID = :mid1', array(':mid1'=>$_POST['RE']));
        Database::query('INSERT INTO comments VALUES(:id, :sid, :pid, :comm, :pname)', array(':id'=>'', ':sid'=>$userid, ':pid'=>$role2[0]['Creator_ID'], ':comm'=>' has removed '.Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$role2[0]['Member_ID']))[0]['Username'].' from the project.', ':pname'=>$pname));
        $alert = "You have been invited to contribute to project ".$pname." by ".Database::query('SELECT Username FROM r_users WHERE ID = :cid', array(':cid'=>$cid))[0]['Username'].";accept=yes";
        $exists = Database::query('SELECT COUNT(Alert) FROM alerts WHERE Alert = :alert AND Receiver_ID = :recv AND Sender_ID = :crea', array(':alert'=>$alert, ':recv'=>$role2[0]['Member_ID'], ':crea'=>$role2[0]['Creator_ID']))[0]['COUNT(Alert)'];
        if($exists == 1){
        $aid = Database::query('SELECT ID FROM alerts WHERE Alert = :alert AND Receiver_ID = :recv AND Sender_ID = :crea', array(':alert'=>$alert, ':recv'=>$role2[0]['Member_ID'], ':crea'=>$role2[0]['Creator_ID']))[0]['ID'];
        Database::query('DELETE FROM alerts WHERE ID = :aid', array(':aid'=>$aid));
        $alert = "You have been removed from the project ".$pname." by ".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'].";accept=none";
        Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid,':rid'=>$role2[0]['Member_ID'], ':newa'=>1, ':alertX'=>$alert));
        }
        header("Location: ../project.php?id=".$_POST['projid']);
      }
      else if(count($role2) == 1 && $role2[0]['Member_ID'] != $userid && $userid == $cid && ($role2[0]['Role'] == 0 || $role2[0]['Role'] == -1 || $role2[0]['Role'] == 1)){
        Database::query('DELETE FROM projects WHERE ID = :mid1', array(':mid1'=>$_POST['RE']));
        Database::query('INSERT INTO comments VALUES(:id, :sid, :pid, :comm, :pname)', array(':id'=>'', ':sid'=>$userid, ':pid'=>$role2[0]['Creator_ID'], ':comm'=>' has removed '.Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$role2[0]['Member_ID']))[0]['Username'].' from the project.', ':pname'=>$pname));
        $alert = "You have been invited to contribute to project ".$pname." by ".Database::query('SELECT Username FROM r_users WHERE ID = :cid', array(':cid'=>$cid))[0]['Username'].";accept=yes";
        $exists = Database::query('SELECT COUNT(Alert) FROM alerts WHERE Alert = :alert AND Receiver_ID = :recv AND Sender_ID = :crea', array(':alert'=>$alert, ':recv'=>$role2[0]['Member_ID'], ':crea'=>$role2[0]['Creator_ID']))[0]['COUNT(Alert)'];
        if($exists == 1){
        $aid = Database::query('SELECT ID FROM alerts WHERE Alert = :alert AND Receiver_ID = :recv AND Sender_ID = :crea', array(':alert'=>$alert, ':recv'=>$role2[0]['Member_ID'], ':crea'=>$role2[0]['Creator_ID']))[0]['ID'];
        Database::query('DELETE FROM alerts WHERE ID = :aid', array(':aid'=>$aid));
        $alert = "You have been removed from the project ".$pname." by ".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'].";accept=none";
        Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid,':rid'=>$role2[0]['Member_ID'], ':newa'=>1, ':alertX'=>$alert));
        }
        header("Location: ../project.php?id=".$_POST['projid']);
      }
      else{
        header("Location: ../project.php?id=".$_POST['projid']);
      }
    }
    else{
      header("Location: ../index.php");
    }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
    $alldata = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['REA']));
    $projid = $_POST['projid'];
    $projdata = Database::query('SELECT Project_Name FROM projects WHERE ID = :id', array(':id'=>$projid));
    if(count($alldata) == 1 && count($projdata) == 1 && $alldata[0]['Project_Name'] == Database::query('SELECT Project_Name FROM projects WHERE ID = :id', array(':id'=>$projid))[0]['Project_Name']){
      $reviewers = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$alldata[0]['Creator_ID'], ':pname'=>$alldata[0]['Project_Name']));
      $found = 0;
      for($i = 0; $i<count($reviewers); $i++){
        if($reviewers[$i]['Member_ID'] == $userid && ($reviewers[$i]['Role'] == 0 || $reviewers[$i]['Role'] == -1) && $userid != $reviewers[$i]['Creator_ID'] && $userid == $alldata[0]['Member_ID']){
          $found = 1;
        }
      }

      if($found == 0){
        Database::query('UPDATE files SET Reviewed = :r WHERE ID = :id', array(':r'=>0, ':id'=>$alldata[0]['ID']));
      }

      header("Location: ../project.php?id=".$_POST['projid']);

    }
    else{
      header("Location: ../project.php?id=".$_POST['projid']);
    }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
    $alldata = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['RER']));
    $projid = $_POST['projid'];
    $projdata = Database::query('SELECT Project_Name FROM projects WHERE ID = :id', array(':id'=>$projid));
    if(count($alldata) == 1 && count($projdata) == 1 && $alldata[0]['Project_Name'] == Database::query('SELECT Project_Name FROM projects WHERE ID = :id', array(':id'=>$projid))[0]['Project_Name']){
      $reviewers = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$alldata[0]['Creator_ID'], ':pname'=>$alldata[0]['Project_Name']));
      $found = 0;
      for($i = 0; $i<count($reviewers); $i++){
        if($reviewers[$i]['Member_ID'] == $userid && ($reviewers[$i]['Role'] == 0 || $reviewers[$i]['Role'] == -1) && $userid != $reviewers[$i]['Creator_ID'] && $userid == $alldata[0]['Member_ID']){
          $found = 1;
        }
      }
      if($found == 0){
        Database::query('UPDATE files SET Reviewed = :r WHERE ID = :id', array(':r'=>-1, ':id'=>$alldata[0]['ID']));
      }

      header("Location: ../project.php?id=".$_POST['projid']);

    }
    else{
      header("Location: ../project.php?id=".$_POST['projid']);
    }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && isset($_POST['R']) && !isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
    $fileData = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['R']));
    if(count($fileData) == 1){
    $projid = $_POST['projid'];
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
      header("Location: ../project.php?id=".$_POST['projid']);
    }
    else{
      header("Location: ../code.php?id=".$_POST['R']."&method=read#default");
    }
  }
  else{
    header("Location: ../project.php?id=".$_POST['projid']);
  }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && isset($_POST['E']) && !isset($_POST['D']) && isset($_POST['projid'])){
    $fileData = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['E']));
    if(count($fileData) == 1){
    $projid = $_POST['projid'];
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
      header("Location: ../project.php?id=".$_POST['projid']);
    }
    else{
      header("Location: ../code.php?id=".$_POST['E']."&method=edit#default");
    }
  }
  else{
    header("Location: ../project.php?id=".$_POST['projid']);
  }
  }
  else if(!isset($_POST['P']) && !isset($_POST['DE']) && !isset($_POST['RE']) && !isset($_POST['REA']) && !isset($_POST['RER']) && !isset($_POST['R']) && !isset($_POST['E']) && isset($_POST['D']) && isset($_POST['projid'])){
    $fileData = Database::query('SELECT * FROM files WHERE ID = :id', array(':id'=>$_POST['D']));
    if(count($fileData) == 1){
    $projid = $_POST['projid'];
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
      header("Location: ../project.php?id=".$_POST['projid']);
    }
    else{
      if(($found == -1 && $userid == $fileData[0]['Creator_ID'] && $fileData[0]['Creator_ID'] == $fileData[0]['Member_ID']) ||
      ($found == -1 && $fileData[0]['Creator_ID'] != $fileData[0]['Member_ID'])  || ($found == 1 && $userid == $fileData[0]['Member_ID']) ||
      ($found == 0 && $userid == $fileData[0]['Member_ID'])){
        $path = '../users/U_'.Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$fileData[0]['Creator_ID']))[0]['Username'].'/'.$fileData[0]['Project_Name'].'/'.$fileData[0]['Filename'].'.txt';
        if(file_exists($path)){
          unlink($path);
          Database::query('DELETE FROM files WHERE ID = :id', array(':id'=>$fileData[0]['ID']));
          header("Location: ../project.php?id=".$_POST['projid']);
        }
        else{
          header("Location: ../project.php?id=".$_POST['projid']);
        }
      }
      else{
        header("Location: ../project.php?id=".$_POST['projid']);
      }
    }
  }else{
    header("Location: ../project.php?id=".$_POST['projid']);
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
}
else{
  header("Location: ../login.php");
}
?>
