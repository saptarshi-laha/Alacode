<?php
include('./php/checkLoggedIn.php');
include('./php/csrf_magic.php');

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!checkLoggedIn::isLoggedIn()){
  header("Location: login.php");
}
else{
  $userid = checkLoggedIn::isLoggedIn();
  $results1 = Database::query('SELECT * FROM alerts WHERE Receiver_ID = :id AND New_alert = :x', array(':id'=>$userid, ':x'=>1));
  $results2 = Database::query('SELECT * FROM alerts WHERE Receiver_ID = :id AND New_alert = :x', array(':id'=>$userid, ':x'=>0));
  $total1 = count($results1);
  $total2 = count($results2);
  Database::query('UPDATE alerts SET New_Alert = :x WHERE Receiver_ID = :id', array(':x'=>0, ':id'=>$userid));
}

include('./includes/top.php');
echo '<title>Alerts - À La Codé</title>';
include('./includes/header.php');

?>
                  <div class="card shadow">
                      <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold">Alerts</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable my-0" id="dataTable" aria-describedby="dataTable_info">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align:center;">Username</th>
                                        <th scope="col" style="text-align:center;">Alert</th>
                                        <th scope="col" style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    for($i = 0; $i<$total1; $i++){
                                      $temp1 = explode(";", $results1[$i]['Alert']);
                                      $temp2 = explode(" " ,$temp1[0]);
                                      $username_current = end($temp2);
                                    echo '<form action="./php/doAlertMessageAction.php" method="post">';
                                    echo '<input type="hidden" name="captcha" value="">';
                                    echo '<tr style="background-color:#CBFFE7;">';
                                        echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                        echo '<td style="text-align:center;">'.htmlspecialchars($temp1[0]).'</td>';
                                        if($temp1[1] == "accept=no"){
                                          echo '<input type="hidden" name="uid" value="'.Database::query('SELECT ID FROM r_users WHERE Username = :username', array(':username'=>$username_current))[0]['ID'].'">';
                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="A" value="'.$results1[$i]['ID'].'">Accept</button></td>';
                                        }
                                        else{
                                          echo '<input type="hidden" name="uid" value="'.Database::query('SELECT ID FROM r_users WHERE Username = :username', array(':username'=>$username_current))[0]['ID'].'">';
                                          echo '<td style="text-align:center;">'.htmlspecialchars("No Action Required.").'</td>';
                                        }
                                    echo '</tr>';
                                    echo '</form>';
                                  }

                                  for($i = 0; $i<$total2; $i++){
                                    $temp1 = explode(";", $results2[$i]['Alert']);
                                    $temp2 = explode(" " ,$temp1[0]);
                                    $username_current = end($temp2);
                                    echo '<form action="./php/doAlertMessageAction.php" method="post">';
                                    echo '<input type="hidden" name="captcha" value="">';
                                    echo '<tr>';
                                        echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                        echo '<td style="text-align:center;">'.htmlspecialchars($temp1[0]).'</td>';
                                        if($temp1[1] == "accept=no"){
                                          echo '<input type="hidden" name="uid" value="'.Database::query('SELECT ID FROM r_users WHERE Username = :username', array(':username'=>$username_current))[0]['ID'].'">';
                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="A" value="'.$results2[$i]['ID'].'">Accept</button></td>';
                                        }
                                        else if($temp1[1] == "accept=yes"){
                                          echo '<input type="hidden" name="uid" value="'.Database::query('SELECT ID FROM r_users WHERE Username = :username', array(':username'=>$username_current))[0]['ID'].'">';
                                          echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="R" value="'.$results2[$i]['ID'].'">Resign</button></td>';
                                        }
                                        else{
                                          echo '<input type="hidden" name="uid" value="'.Database::query('SELECT ID FROM r_users WHERE Username = :username', array(':username'=>$username_current))[0]['ID'].'">';
                                          echo '<td style="text-align:center;">'.htmlspecialchars("No Action Required.").'</td>';
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
                    </div>
                <?php
                include('./includes/footer.php');
                 ?>
