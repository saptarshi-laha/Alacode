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
  $results1 = Database::query('SELECT * FROM messages WHERE Receiver_ID = :id AND New_Message = :x', array(':id'=>$userid, ':x'=>1));
  $results2 = Database::query('SELECT * FROM messages WHERE Receiver_ID = :id AND New_Message = :x', array(':id'=>$userid, ':x'=>0));
  $total1 = count($results1);
  $total2 = count($results2);
  Database::query('UPDATE messages SET New_Message = :x WHERE Receiver_ID = :id', array(':x'=>0, ':id'=>$userid));
}

include('./includes/top.php');
echo '<title>Messages - À La Codé</title>';
include('./includes/header.php');

?>

                    <div class="card shadow">
                      <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold">Messages</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable my-0" id="dataTable" aria-describedby="dataTable_info">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align:center;">Username</th>
                                        <th scope="col" style="text-align:center;">Message</th>
                                        <th scope="col" style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    for($i = 0; $i<$total1; $i++){
                                    $username_current = Database::query('SELECT Username FROM r_users WHERE ID=:id', array(':id'=>$results1[$i]['Sender_ID']))[0]['Username'];
                                    echo '<form action="./profile.php" method="get">';
                                    echo '<input type="hidden" name="captcha" value="">';
                                    echo '<tr style="background-color:#CBFFE7;">';
                                    echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                    echo '<td style="text-align:center;">'.htmlspecialchars($results1[$i]['Message']).'</td>';
                                    echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="id" value="'.$results1[$i]['Sender_ID'].'">Message</button></td>';
                                    echo '</tr>';
                                    echo '</form>';
                                  }

                                  for($i = 0; $i<$total2; $i++){
                                    $username_current = Database::query('SELECT Username FROM r_users WHERE ID=:id', array(':id'=>$results2[$i]['Sender_ID']))[0]['Username'];
                                    echo '<form action="./profile.php" method="get">';
                                    echo '<input type="hidden" name="captcha" value="">';
                                    echo '<tr>';
                                    echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                    echo '<td style="text-align:center;">'.htmlspecialchars($results2[$i]['Message']).'</td>';
                                    echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="id" value="'.$results2[$i]['Sender_ID'].'">Message</button></td>';
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
