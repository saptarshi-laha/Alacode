<?php
include('checkLoggedIn.php');

if(checkLoggedIn::isLoggedIn()){
  $userid = checkLoggedIn::isLoggedIn();
  if(isset($_POST['captcha'])){
    $captcha = Captcha::createRequest($_POST['captcha']);
    if($captcha[0] == true && $captcha[1] >= 0.0){
  if(isset($_POST['filename']) && isset($_POST['file']) && isset($_POST['projectname']) && !isset($_POST['proj_del']) && !isset($_POST['proj_delname'])){
    $member = Database::query('SELECT Member_ID FROM projects WHERE ID = :id', array(':id'=>$_POST['projectname']));
    if(count($member) == 1 && $member[0]['Member_ID'] == $userid){
    $data = Database::query('SELECT * FROM projects WHERE ID = :id', array(':id'=>$_POST['projectname']));
    if(count($data) == 1 && preg_match('/^(?=.{5,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/', $_POST['filename']) && strlen($_POST['filename']) >=5 && strlen($_POST['filename']) <= 32 &&
    !file_exists("../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$data[0]['Creator_ID']))[0]['Username']."/".$data[0]['Project_Name']."/".$_POST['filename'].'.txt')){
    Database::query('INSERT INTO files VALUES(:id, :mid, :cid, :fname, :pname, :rev)', array(':id'=>'', ':mid'=>$userid, ':cid'=>$data[0]['Creator_ID'], ':fname'=>$_POST['filename'], ':pname'=>$data[0]['Project_Name'], ':rev'=>1));
    touch("../users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$data[0]['Creator_ID']))[0]['Username']."/".$data[0]['Project_Name']."/".$_POST['filename'].'.txt');
    header("Location: ../project.php?id=".$_POST['projectname']);
    }
    else{
    header("Location: ../project.php?id=".$_POST['projectname']);
    }
  }
  else{
    header("Location: ../index.php");
  }
  }
  else if(!isset($_POST['filename']) && !isset($_POST['file']) && !isset($_POST['projectname']) && isset($_POST['proj_del']) && isset($_POST['proj_delname'])){
    $projData = Database::query('SELECT * FROM projects WHERE ID = :id', array(':id'=>$_POST['proj_delname']));
    if(count($projData) == 1){
      if($projData[0]['Creator_ID'] == $userid){
        Database::query('DELETE FROM comments WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$userid, ':pname'=>$projData[0]['Project_Name']));
        $username = Database::query('SELECT * FROM r_users WHERE ID = :cid', array(':cid'=>$projData[0]['Creator_ID']))[0]['Username'];
        $members = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$userid, ':pname'=>$projData[0]['Project_Name']));
        for($i = 0; $i<count($members); $i++){
          Database::query('DELETE FROM files WHERE Creator_ID = :cid AND Member_ID = :mid AND Project_Name = :pname', array(':cid'=>$userid, ':mid'=>$members[$i]['Member_ID'], ':pname'=>$projData[0]['Project_Name']));
          $alert = 'The project '.$projData[0]['Project_Name'].' has been deleted by its creator '.$username.';accept=none';
          Database::query('INSERT INTO alerts VALUES(:id, :sid, :rid, :newa, :alertX)', array(':id'=>'', ':sid'=>$userid,':rid'=>$members[$i]['Member_ID'], ':newa'=>1, ':alertX'=>$alert));
        }
        $alert = "You have been invited to contribute to project ".$projData[0]['Project_Name']." by ".$username.";accept=yes";
        Database::query('DELETE FROM alerts WHERE Sender_ID = :id AND Alert = :alert', array(':id'=>$userid, ':alert'=>$alert));
        $alert = "You have been invited to contribute to project ".$projData[0]['Project_Name']." by ".$username.";accept=no";
        Database::query('DELETE FROM alerts WHERE Sender_ID = :id AND Alert = :alert', array(':id'=>$userid, ':alert'=>$alert));
        Database::query('DELETE FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$userid, ':pname'=>$projData[0]['Project_Name']));
        $username = Database::query('SELECT * FROM r_users WHERE ID = :cid', array(':cid'=>$projData[0]['Creator_ID']));
        if(count($username) == 1){
        delete_files("../users/U_".$username[0]['Username']."/".$projData[0]['Project_Name']."/");
        header("Location: ../index.php");
      }
      else{
        header("Location: ../project.php?id=".$_POST['proj_delname']);
      }
      }
      else{
        header("Location: ../project.php?id=".$_POST['proj_delname']);
      }

    }
    else{
      header("Location: ../project.php?id=".$_POST['proj_delname']);
    }

  }
  else{
    header("Location: ../index.php");
  }
}
else{
  header("Location: ../project.php?id=".$_POST['projectname']);
}
}
else{
  header("Location: ../project.php?id=".$_POST['projectname']);
}
}
else{
  header("Location: ../login.php");
}

function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK );

        foreach( $files as $file ){
            delete_files( $file );
        }

        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );
    }
}

?>
