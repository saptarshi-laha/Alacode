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
  if(isset($_GET['search'])){
    $search = $_GET['search'];

  $results = Database::query("SELECT * FROM r_users WHERE Username LIKE CONCAT('%', :search, '%') AND ID!=:id" , array(':search'=>$search, ':id'=>$userid));
  $total = count($results);

  }
  else{
    header("Location: dashboard.php?id=".$userid);
  }
}

include('./includes/top.php');
echo '<title>Search - À La Codé</title>';
include('./includes/header.php');
?>
                        <div class="card shadow">
                          <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold"><?php echo htmlspecialchars('Results for '.$search.':'); ?></p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable my-0" id="dataTable" aria-describedby="dataTable_info">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align:center;">Username</th>
                                        <th scope="col" style="text-align:center;">Name</th>
                                        <th scope="col" style="text-align:center;">Projects Created/Contributed</th>
                                        <th scope="col" style="text-align:center;">Message</th>
                                        <th scope="col" style="text-align:center;">Project Title</th>
                                        <th scope="col" style="text-align:center;">Add to Project</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  for($i = 0; $i<$total; $i++){
                                    $username_current = $results[$i]['Username'];
                                    $userid_current = $results[$i]['ID'];
                                    echo '<form action="./php/doAction.php" method="post">';
                                    echo '<input type="hidden" name="captcha" value="">';
                                    echo '<input type="hidden" name="search" value="'.htmlspecialchars($search).'">';
                                    echo '<tr>';
                                        echo '<td style="text-align:center;"><img class="rounded-circle mr-2" width="30" height="30" src="'.htmlspecialchars("./users/U_".$username_current."/face").'">'.$username_current.'</td>';
                                        echo '<td style="text-align:center;">'.htmlspecialchars($results[$i]['First_Name'].' '.$results[$i]['Last_Name']).'</td>';
                                        $num = Database::query('SELECT COUNT(Project_Name) FROM projects WHERE MEMBER_ID = :id1', array(':id1'=>$userid_current))[0]['COUNT(Project_Name)'];
                                        echo '<td style="text-align:center;">'.$num.'</td>';
                                        echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="M" value="'.htmlspecialchars($results[$i]['ID']).'">Message</button></td>';
                                        echo '<td style="text-align:center;"><select class="form-control" name="S">';
                                        $dir = Database::query('SELECT Project_Name FROM projects WHERE Creator_ID = :cid AND Member_ID = :mid', array(':cid'=>$userid, ':mid'=>$userid));
                                        $exists = Database::query('SELECT Project_Name FROM projects WHERE Creator_ID = :cid AND Member_ID = :mid', array(':cid'=>$userid, ':mid'=>$userid_current));
                                        $dir = array_diff(array_column($dir, 'Project_Name'), array_column($exists, 'Project_Name'));
                                        $dir = array_values($dir);
                                        for($j=0;$j<count($dir);$j++) {
                                          if($dir[$j] != ''){
                                            echo '<option value="'.htmlspecialchars($dir[$j]).'">'.htmlspecialchars($dir[$j]).'</option>';
                                          }
                                        }
                                        echo '</select></td>';
                                        echo '<td style="text-align:center;"><button class="btn btn-primary py-0" type="submit" name="P" value="'.htmlspecialchars($results[$i]['ID']).'">Add to Project</button></td>';
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
