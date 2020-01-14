<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){
  $userid = checkLoggedIn::isLoggedIn();

  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){

  if(isset($_POST['A']) && isset($_POST['uid']) && !isset($_POST['R']) && !isset($_POST['message']) && !isset($_POST['id']) && !isset($_POST['proj_comm']) && !isset($_POST['pid']) && !isset($_POST['pname']) && !isset($_POST['ppid'])){
    $id = $_POST['A'];
    $otheruserid = $_POST['uid'];
    $result = Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id));
    if(count(result) == 1 && $result[0]['Sender_ID'] == $otheruserid && $result[0]['Receiver_ID'] == $userid){
      $mess1 = explode(" by ", Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id))[0]['Alert']);
      $prname = explode(" ", $mess1[0]);
      $alert = Database::query('SELECT Alert from alerts WHERE ID = :id',array(":id"=>$id))[0]['Alert'];
      $temp1 = explode(";", $alert)[1];
      if($temp1 == 'accept=no'){
        $mess1 = explode(" by ", Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id))[0]['Alert']);
        $prname = explode(" ", $mess1[0]);
        $alert = Database::query('SELECT Alert from alerts WHERE ID = :id',array(":id"=>$id))[0]['Alert'];
        $temp1 = explode(";", $alert)[0];
        Database::query('UPDATE alerts SET Alert = :x WHERE ID = :id',array(":x"=>$temp1.';accept=yes', ":id"=>$id));
        Database::query('INSERT INTO projects VALUES(:id, :cid, :pname, :mid, :role)', array(':id'=>'', ':cid'=>$otheruserid, ':pname'=>$prname[8], ':mid'=>$userid, ':role'=>1));
        Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid,':rid'=>$otheruserid, ':newa'=>1, ':alertX'=>"Your project ".$prname[8]." is being actively contributed by ".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'].";accept=none"));
        Database::query('INSERT INTO comments VALUES(:id, :sid, :pid, :comm, :pname)', array(':id'=>'', ':sid'=>$userid, ':pid'=>$otheruserid, ':comm'=>' has joined the project.', ':pname'=>$prname[8]));
      }
    }
    header("Location: ../alerts.php");
  }
  else if(!isset($_POST['A']) && isset($_POST['uid']) && isset($_POST['R']) && !isset($_POST['message']) && !isset($_POST['id']) && !isset($_POST['proj_comm']) && !isset($_POST['pid']) && !isset($_POST['pname']) && !isset($_POST['ppid'])){
    $id = $_POST['R'];
    $otheruserid = $_POST['uid'];
    $result = Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id));
    if(count(result) == 1 && $result[0]['Sender_ID'] == $otheruserid && $result[0]['Receiver_ID'] == $userid){
      $mess1 = explode(" by ", Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id))[0]['Alert']);
      $prname = explode(" ", $mess1[0]);
      $alert = Database::query('SELECT Alert from alerts WHERE ID = :id',array(":id"=>$id))[0]['Alert'];
      $temp1 = explode(";", $alert)[1];
      if($temp1 == 'accept=yes'){
        $alert = Database::query('SELECT Alert from alerts WHERE ID = :id',array(":id"=>$id))[0]['Alert'];
        $temp1 = explode(";", $alert)[0];
        $mess1 = explode(" by ", Database::query('SELECT * FROM alerts WHERE ID = :id', array(':id'=>$id))[0]['Alert']);
        $prname = explode(" ", $mess1[0]);
        Database::query('DELETE FROM alerts WHERE ID = :id',array(":id"=>$id));
        Database::query('DELETE FROM projects WHERE Member_ID = :mid AND Project_Name = :prname AND Creator_ID = :cid', array(':mid'=>$userid, ':prname'=>$prname[8], ':cid'=>$otheruserid));
        Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid, ':rid'=>$otheruserid, ':newa'=>1, ':alertX'=>"Your project ".$prname[8]." has been abandoned by ".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username'].";accept=none"));
        Database::query('INSERT INTO comments VALUES(:id, :sid, :pid, :comm, :pname)', array(':id'=>'', ':sid'=>$userid, ':pid'=>$otheruserid, ':comm'=>' has resigned from the project.', ':pname'=>$prname[8]));
          }
        }
    header("Location: ../alerts.php");
  }
  else if(!isset($_POST['A']) && !isset($_POST['R']) && !isset($_POST['uid']) && isset($_POST['message']) && isset($_POST['id']) && !isset($_POST['proj_comm']) && !isset($_POST['pid']) && !isset($_POST['pname']) && !isset($_POST['ppid'])){
    $otheruserid = $_POST['id'];
    $result = Database::query('SELECT * FROM r_users WHERE ID = :id', array(':id'=>$otheruserid));
    if(count($result) == 1){
    Database::query('INSERT INTO messages VALUES(:id, :sid, :rid, :nm, :me)', array(':id'=>'', ':sid'=>$userid, ':rid'=>$otheruserid, ':nm'=>1, ':me'=>$_POST['message']));
    }
    header('Location: ../profile.php?id='.$otheruserid);
  }
  else if(!isset($_POST['A']) && !isset($_POST['R']) && !isset($_POST['uid']) && !isset($_POST['message']) && !isset($_POST['id']) && isset($_POST['proj_comm']) && isset($_POST['pid']) && isset($_POST['pname']) && isset($_POST['ppid'])){
    $result = Database::query('SELECT * FROM projects WHERE Project_Name = :pname AND Creator_ID = :pid AND Member_ID = :mid', array(':pname'=>$_POST['pname'], ':pid'=>$_POST['pid'], ':mid'=>$userid));
    if(count($result) == 1){
    Database::query('INSERT INTO comments VALUES(:id, :sid, :pid, :comm, :pname)', array(':id'=>'', ':sid'=>$userid, ':pid'=>$_POST['pid'], ':comm'=>$_POST['proj_comm'], ':pname'=>$_POST['pname']));
    }
    header('Location: ../project.php?id='.$_POST['ppid']);
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
  header("Location: ../login.php");
}

?>
