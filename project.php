<?php

include('./php/checkLoggedIn.php');
include('./php/csrf_magic.php');

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!checkLoggedIn::isLoggedIn()){
  //log
  header("Location: ./login.php");
}
else{
  //log
  $userid = checkLoggedIn::isLoggedIn();
  $projectid = $_GET['id'];
  $projData = Database::query('SELECT * FROM projects WHERE ID = :id', array(':id'=>$projectid));
  if(count($projData) == 1){
  $exists = Database::query('SELECT COUNT(Project_Name) FROM projects WHERE Creator_ID = :cid AND Member_ID = :mid AND Project_Name = :pname', array(':cid'=>$projData[0]['Creator_ID'], ':mid'=>$userid, ':pname'=>$projData[0]['Project_Name']))[0]['COUNT(Project_Name)'];
  if($exists == 1){
    $projects = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Member_ID = :mid AND Project_Name = :pname', array(':cid'=>$projData[0]['Creator_ID'], ':mid'=>$userid, ':pname'=>$projData[0]['Project_Name']));
  }
  else{
    header("Location: ./index.php");
  }
}
else{
  header("Location: ./index.php");
}
}

include('./includes/top.php');
echo '<title>Project - À La Codé</title>';
include('./includes/header.php');
?>

                <h3 class="text-dark mb-4"><?php echo htmlspecialchars('Project - '.$projects[0]['Project_Name']);?></h3>
                <div class="row mb-3">
                    <div class="col-lg-4">
                      <div class="card shadow mb-4">
                        <div class="card-header py-3">
                          <p class="text-primary m-0 font-weight-bold">Contibutors</p>
                        </div>
                            <div class="card-body">
                              <nav style="overflow-y:scroll;max-height: 210px;height: 210px;">
                                <?php
                                echo "<ul>";

                                $num = Database::query('SELECT COUNT(Member_ID) FROM projects WHERE Creator_ID = :id1 AND Project_Name = :pname', array(':id1'=>$projData[0]['Creator_ID'], ':pname'=>$projData[0]['Project_Name']))[0]['COUNT(Member_ID)'];

                                $results = Database::query('SELECT * FROM projects WHERE Creator_ID = :id1 AND Project_Name = :pname', array(':id1'=>$projData[0]['Creator_ID'], ':pname'=>$projData[0]['Project_Name']));

                                for($i = 0; $i < $num; $i++){
                                  if($results[$i]['Creator_ID'] == $results[$i]['Member_ID'] && $results[$i]['Role'] == -1){
                                    echo  '<li>'.htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$results[$i]['Member_ID']))[0]['Username']).'<br/><strong>Owner, Admin</strong></li>';
                                  }
                                  else if($results[$i]['Role'] == -1){
                                    echo  '<li>'.htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$results[$i]['Member_ID']))[0]['Username']).'<br/><strong>Admin</strong></li>';
                                  }
                                  else if($results[$i]['Role'] == 1){
                                     echo  '<li>'.htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$results[$i]['Member_ID']))[0]['Username']).'<br/><strong>Developer</strong></li>';
                                   }
                                   else if($results[$i]['Role'] == 0){
                                     echo '<li>'.htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$results[$i]['Member_ID']))[0]['Username']).'<br/><strong>Reviewer</strong></li>';
                                   }
                                   }

                                if($num == 0){
                                    echo "<center><b>No Contributors Founds.</b></center>";
                                  }
                                echo "</ul>";

                                  ?>
                              </nav>
                            </div>
                          </div>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 font-weight-bold">Comments</p>
                            </div>
                            <div class="card-body">
                              <nav style="overflow-y:scroll;max-height: 210px;height: 210px;">
                                <?php
                                echo "<ul>";

                                $num = Database::query('SELECT COUNT(Comment) FROM comments WHERE Creator_ID = :id1 AND Project_Name = :pname', array(':id1'=>$projects[0]['Creator_ID'], ':pname'=>$projects[0]['Project_Name']))[0]['COUNT(Comment)'];
                                $results = Database::query('SELECT * FROM comments WHERE Creator_ID = :id1 AND Project_Name = :pname', array(':id1'=>$projects[0]['Creator_ID'], ':pname'=>$projects[0]['Project_Name']));

                                for($i = 0; $i < $num; $i++){
                                    echo  '<li>'.htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$results[$i]['Sender_ID']))[0]['Username']).' - '.$results[$i]['Comment'].'</li>';
                                  }

                                if($num == 0){
                                    echo "<center><b>No Comments Founds.</b></center>";
                                  }
                                echo "</ul>";

                                  ?>
                              </nav>
                              <form action="./php/doAlertMessageAction.php" method="post">
                              <input type="hidden" name="captcha" value="">
                              <div align="center"><input name="proj_comm" class="form-control" type="text" required="" minlength="5" maxlength="32"/></div><br/>
                              <input name="ppid" type="hidden" value="<?php echo htmlspecialchars($projectid);?>">
                              <input name="pid" type="hidden" value="<?php echo htmlspecialchars($projects[0]['Creator_ID']);?>">
                              <input name="pname" type="hidden" value="<?php echo htmlspecialchars($projects[0]['Project_Name']);?>">
                              <button name="project" class="btn btn-primary btn-sm" type="submit" style="padding: 10px;margin: 0px;margin-left: 230px;margin-bottom: 0px;margin-top: 0px;">Comment</button>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold">Files</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                                            <table class="table dataTable my-0" id="dataTable" aria-describedby="dataTable_info">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" style="text-align:center;">Username</th>
                                                        <th scope="col" style="text-align:center;">Filename</th>
                                                        <th scope="col" style="text-align:center;">Read</th>
                                                        <th scope="col" style="text-align:center;">Edit</th>
                                                        <th scope="col" style="text-align:center;">Review</th>
                                                        <th scope="col" style="text-align:center;">Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                  <?php
                                                  $files = Database::query('SELECT * FROM files WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$projects[0]['Creator_ID'], ':pname'=>$projects[0]['Project_Name']));
                                                  for($i = 0; $i<count($files); $i++){
                                                    $username_current = Database::query('SELECT Username FROM r_users WHERE ID = :mid', array(':mid'=>$files[$i]['Member_ID']))[0]['Username'];
                                                    echo '<form action="./php/roles.php" method="post" target="_blank">';
                                                    echo '<input type="hidden" name="captcha" value="">';
                                                    echo '<input type="hidden" name="projid" value="'.$projectid.'">';
                                                    echo '<tr>';
                                                        echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                                        echo '<td style="text-align:center;">'.htmlspecialchars($files[$i]['Filename']).'</td>';
                                                        if($projects[0]['Role'] == 0 || $projects[0]['Role'] == -1 || $files[$i]['Member_ID'] == $userid){
                                                            echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="R" value="'.$files[$i]['ID'].'">Read</button></td>';
                                                        }
                                                        else{
                                                          echo '<td style="text-align:center;">Action Not Allowed</td>';
                                                        }
                                                        if(($projects[0]['Role'] == -1 || $files[$i]['Member_ID'] == $userid) && ($files[$i]['Creator_ID'] != $files[$i]['Member_ID'] || $files[$i]['Creator_ID'] == $userid)){
                                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="E" value="'.$files[$i]['ID'].'">Edit</button></td>';
                                                        }
                                                        else{
                                                          echo '<td style="text-align:center;">Action Not Allowed</td>';
                                                        }
                                                        echo '</form>';
                                                        echo '<form action="./php/roles.php" method="post">';
                                                        echo '<input type="hidden" name="captcha" value="">';
                                                        echo '<input type="hidden" name="projid" value="'.$projectid.'">';
                                                        if(($projects[0]['Role'] == 0 || $projects[0]['Role'] == -1) && ($files[$i]['Member_ID'] != $userid || $files[$i]['Creator_ID'] == $userid)){
                                                          if($files[$i]['Reviewed'] == 1){
                                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="REA" value="'.$files[$i]['ID'].'">Accept</button>&nbsp;&nbsp;<button class="btn btn-primary py-0" type="submit" name="RER" value="'.$files[$i]['ID'].'">Reject</button></td>';
                                                        }
                                                        else if($files[$i]['Reviewed'] == 0 ){
                                                          echo '<td style="text-align:center;">Accepted&nbsp;&nbsp;<button class="btn btn-primary py-0" type="submit" name="RER" value="'.$files[$i]['ID'].'">Reject</button></td>';
                                                        }
                                                        else if($files[$i]['Reviewed'] == -1){
                                                          echo '<td style="text-align:center;">Rejected&nbsp;&nbsp;<button class="btn btn-primary py-0" type="submit" name="REA" value="'.$files[$i]['ID'].'">Accept</button></td>';
                                                        }
                                                        }
                                                        else{
                                                          echo '<td style="text-align:center;">Action Not Allowed</td>';
                                                        }
                                                        if(($projects[0]['Role'] == -1 || $files[$i]['Member_ID'] == $userid) && ($files[$i]['Creator_ID'] != $files[$i]['Member_ID'] || $files[$i]['Creator_ID'] == $userid)){
                                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="D" value="'.$files[$i]['ID'].'">Delete</button></td>';
                                                        }
                                                        else{
                                                          echo '<td style="text-align:center;">Action Not Allowed</td>';
                                                        }
                                                    echo '</tr>';
                                                    echo '</form>';
                                                  }
                                                  ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr></tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <form action="./php/files.php" method="post">
                                        <input type="hidden" name="captcha" value="">
                                          <input type="hidden" name="projectname" value="<?php echo htmlspecialchars($projectid); ?>">
                                        <div align="center"><input name="filename" class="form-control" type="text" required="" minlength="5" maxlength="32" pattern="^(?=.{5,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$" /></div><br/>
                                        <button name="file" class="btn btn-primary btn-sm" type="submit" style="padding: 10px;margin: 0px;margin-left: 500px;margin-bottom: 0px;margin-top: 0px;">Add File</button>
                                        </form>
                                        <?php
                                        if($userid == $projData[0]['Creator_ID']){
                                          ?>
                                          <form action="./php/files.php" method="post">
                                            <input type="hidden" name="captcha" value="">;
                                            <button name="proj_del" class="btn btn-primary btn-sm" type="submit" style="padding: 10px;margin: 0px;margin-left: 485px;margin-bottom: 0px;margin-top: 10px;">Delete Project</button>
                                            <input type="hidden" name="proj_delname" value="<?php echo htmlspecialchars($projectid); ?>">
                                      </form>
                                      <?php } ?>
                                    </div>
                                </div>
                                <?php
                                if($projects[0]['Role'] == -1 && $projects[0]['Creator_ID'] == $userid){
                                  echo '<div class="card shadow mb-3">';
                                  echo '<div class="card-header py-3">';
                                  echo '<p class="text-primary m-0 font-weight-bold">Manage Members</p>';
                                  echo '</div>';
                                  echo '<div class="card-body">';
                                  echo '<div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">';
                                  echo '<table class="table dataTable my-0" id="dataTable">';
                                  echo '<thead>';
                                  echo '<tr>';
                                  echo '<th style="text-align:center;">Username</th>';
                                  echo '<th style="text-align:center;">Role</th>';
                                  echo '<th style="text-align:center;">Action</th>';
                                  echo '</tr>';
                                  echo '</thead>';
                                  echo '<tbody>';
                                  $users = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$projects[0]['Creator_ID'], ':pname'=>$projects[0]['Project_Name']));
                                  for($i = 0; $i<count($users); $i++){
                                      $username_current = Database::query('SELECT Username FROM r_users WHERE ID = :mid', array(':mid'=>$users[$i]['Member_ID']))[0]['Username'];
                                      echo '<form action="./php/roles.php" method="post">';
                                      echo '<input type="hidden" name="captcha" value="">';
                                      echo '<input type="hidden" name="projid" value="'.$projectid.'">';
                                      echo '<tr>';
                                      echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                      if($users[$i]['Role'] == -1){
                                        echo '<td style="text-align:center;">Admin</td>';
                                      }
                                      else if($users[$i]['Role'] == 0){
                                        echo '<td style="text-align:center;">Reviewer</td>';
                                      }
                                      else if($users[$i]['Role'] == 1){
                                        echo '<td style="text-align:center;">Developer</td>';
                                      }
                                      if($users[$i]['Member_ID'] != $userid && $users[$i]['Member_ID'] != $projects[0]['Creator_ID']){
                                        echo '<td style="text-align:center;">';
                                        if($users[$i]['Role'] == 0){
                                          echo '<button class="btn btn-primary py-0" type="submit" name="P" value="'.$users[$i]['ID'].'">Promote</button>&nbsp;&nbsp;';
                                          echo '<button class="btn btn-primary py-0" type="submit" name="DE" value="'.$users[$i]['ID'].'">Demote</button>&nbsp;&nbsp';
                                        }
                                        else if($users[$i]['Role'] == 1){
                                          echo '<button class="btn btn-primary py-0" type="submit" name="P" value="'.$users[$i]['ID'].'">Promote</button>&nbsp;&nbsp;';
                                        }
                                        else if($users[$i]['Role'] == -1){
                                          echo '<button class="btn btn-primary py-0" type="submit" name="DE" value="'.$users[$i]['ID'].'">Demote</button>&nbsp;&nbsp;';
                                        }
                                        echo '<button class="btn btn-primary py-0" type="submit" name="RE" value="'.$users[$i]['ID'].'">Remove</button></td>';
                                      }
                                      else{
                                        echo '<td style="text-align:center;">Action Not Allowed</td>';
                                      }
                                      echo '</tr>';
                                      echo '</form>';
                                      }
                                      echo '</tbody>';
                                      echo '<tfoot>';
                                      echo '<tr></tr>';
                                      echo '</tfoot>';
                                      echo '</table>';
                                      echo '</div>';
                                      echo '</div>';
                                      echo '</div>';
                              }
                                else if($projects[0]['Role'] == -1){
                                  echo '<div class="card shadow mb-3">';
                                  echo '<div class="card-header py-3">';
                                  echo '<p class="text-primary m-0 font-weight-bold">Manage Members</p>';
                                  echo '</div>';
                                  echo '<div class="card-body">';
                                  echo '<div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">';
                                  echo '<table class="table dataTable my-0" id="dataTable">';
                                  echo '<thead>';
                                  echo '<tr>';
                                  echo '<th style="text-align:center;">Username</th>';
                                  echo '<th style="text-align:center;">Role</th>';
                                  echo '<th style="text-align:center;">Action</th>';
                                  echo '</tr>';
                                  echo '</thead>';
                                  echo '<tbody>';
                                  $users = Database::query('SELECT * FROM projects WHERE Creator_ID = :cid AND Project_Name = :pname', array(':cid'=>$projects[0]['Creator_ID'], ':pname'=>$projects[0]['Project_Name']));
                                  for($i = 0; $i<count($users); $i++){
                                      $username_current = Database::query('SELECT Username FROM r_users WHERE ID = :mid', array(':mid'=>$users[$i]['Member_ID']))[0]['Username'];
                                      echo '<form action="./php/roles.php" method="post">';
                                      echo '<input type="hidden" name="captcha" value="">';
                                      echo '<input type="hidden" name="projid" value="'.$projectid.'">';
                                      echo '<tr>';
                                      echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                      if($users[$i]['Role'] == -1){
                                        echo '<td style="text-align:center;">Admin</td>';
                                      }
                                      else if($users[$i]['Role'] == 0){
                                        echo '<td style="text-align:center;">Reviewer</td>';
                                      }
                                      else if($users[$i]['Role'] == 1){
                                        echo '<td style="text-align:center;">Developer</td>';
                                      }
                                      if($users[$i]['Role'] != -1 && $users[$i]['Member_ID'] != $userid && $users[$i]['Member_ID'] != $projects[0]['Creator_ID']){
                                        echo '<td style="text-align:center;">';
                                        if($users[$i]['Role'] == 0){
                                          echo '<button class="btn btn-primary py-0" type="submit" name="DE" value="'.$users[$i]['ID'].'">Demote</button>&nbsp;&nbsp';
                                        }
                                        else if($users[$i]['Role'] == 1){
                                          echo '<button class="btn btn-primary py-0" type="submit" name="P" value="'.$users[$i]['ID'].'">Promote</button>&nbsp;&nbsp;';
                                        }
                                        echo '<button class="btn btn-primary py-0" type="submit" name="RE" value="'.$users[$i]['ID'].'">Remove</button></td>';
                                      }
                                      else{
                                        echo '<td style="text-align:center;">Action Not Allowed</td>';
                                      }
                                      echo '</tr>';
                                      echo '</form>';
                                      }
                                      echo '</tbody>';
                                      echo '<tfoot>';
                                      echo '<tr></tr>';
                                      echo '</tfoot>';
                                      echo '</table>';
                                      echo '</div>';
                                      echo '</div>';
                                      echo '</div>';
                              }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php include('./includes/footer.php') ?>
