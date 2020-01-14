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
  if(isset($_GET['id'])){
    $profile = $_GET['id'];

  $results = Database::query("SELECT * FROM r_users WHERE ID =:id" , array(':id'=>$profile));
  $total = count($results);

  if(!$total == 1){
    header("Location: dashboard.php?id=".$userid);
  }

  }
  else{
    header("Location: dashboard.php?id=".$userid);
  }
}

include('./includes/top.php');
echo '<title>Profile - À La Codé</title>';
include('./includes/header.php');
?>

                <h3 class="text-dark mb-4"><?php echo htmlspecialchars($results[0]['First_Name'].' '.$results[0]['Last_Name']); ?></h3>
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-body text-center shadow"><img class="rounded-circle mb-3 mt-4" alt="profile_image" src="<?php echo htmlspecialchars("users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$profile))[0]['Username']."/face");?>" width="160" height="160"></div>
                        </div>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="text-primary font-weight-bold m-0">Projects</h6>
                            </div>
                            <div class="card-body">
                              <nav style="overflow-y:scroll;max-height: 210px;height: 210px;">
                                <?php
                                echo "<ul>";

                                $num = Database::query('SELECT COUNT(Project_Name) FROM projects WHERE MEMBER_ID = :id1', array(':id1'=>$profile))[0]['COUNT(Project_Name)'];
                                $results1 = Database::query('SELECT * FROM projects WHERE MEMBER_ID = :id1', array(':id1'=>$profile));

                                for($i = 0; $i < $num; $i++){
                                  if($results1[$i]['Creator_ID'] == $profile){
                                    echo  '<li>'.htmlspecialchars($results1[$i]['Project_Name']).'<br/><strong>Owner</strong></li>';
                                  }
                                  else{
                                     echo  '<li>'.htmlspecialchars($results1[$i]['Project_Name']).'<br/><strong>Member</strong></li>';
                                   }
                                   }

                                if($num == 0){
                                    echo "<center><b>No Projects Founds.</b></center>";
                                  }
                                echo "</ul>";

                                  ?>
                              </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold">User Information</p>
                                    </div>
                                    <div class="card-body">
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="form-group"><label for="username"><strong>Username</strong></label><p><?php echo htmlspecialchars($results[0]['Username']); ?></p></div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group"><label for="email"><strong>Email Address</strong></label><p><?php echo htmlspecialchars($results[0]['Email']); ?></p></div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="form-group"><label for="first_name"><strong>First Name</strong></label><p><?php echo htmlspecialchars($results[0]['First_Name']); ?></p></div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group"><label for="last_name"><strong>Last Name</strong></label><p><?php echo htmlspecialchars($results[0]['Last_Name']); ?></p></div>
                                                </div>
                                          </div>
                                    </div>
                                </div>
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold">Messages</p>
                                    </div>
                                    <div class="card-body">
                                        <form action="./php/doAlertMessageAction.php" method="post">
                                          <input type="hidden" name="captcha" value="">
                                          <nav style="overflow-y:scroll;max-height: 210px;height: 210px;">
                                            <?php
                                            echo '<ul style="list-style-type: none;">';

                                            $num = Database::query('SELECT COUNT(Message) FROM messages WHERE (Receiver_ID = :id1 AND Sender_ID = :id2) OR (Receiver_ID = :id2 AND Sender_ID = :id1)', array(':id1'=>$profile, ':id2'=>$userid))[0]['COUNT(Message)'];
                                            $results1 = Database::query('SELECT * FROM messages WHERE (Receiver_ID = :id1 AND Sender_ID = :id2) OR (Receiver_ID = :id2 AND Sender_ID = :id1) ORDER BY ID', array(':id1'=>$profile, ':id2'=>$userid));

                                            for($i = 0; $i < $num; $i++){
                                              if($results1[$i]['Sender_ID'] == $userid){
                                                echo  '<li style="text-align:right;padding-right:20px;">'.htmlspecialchars($results1[$i]['Message']).'</li>';
                                              }
                                              else{
                                                 echo  '<li style="text-align:left;">'.htmlspecialchars($results1[$i]['Message']).'</li>';
                                               }
                                               }

                                            if($num == 0){
                                                echo "<center><b>No Messages Found.</b></center>";
                                              }
                                            echo "</ul>";

                                              ?>
                                          </nav>
                                          <input class="form-control" type="hidden" name="id" required="" value="<?php echo htmlspecialchars($profile); ?>">
                                          <input class="form-control" type="text" placeholder="Type your message here..." name="message" required="" maxlength="500">
                                         <div class="form-group"><br/><button class="btn btn-primary btn-sm" type="submit">Send Message</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-5"></div>
                <?php
                include('./includes/footer.php');
                ?>
