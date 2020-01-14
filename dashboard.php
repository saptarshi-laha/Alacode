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
else if(!($_GET['id'] == checkLoggedIn::isLoggedIn())){
  header("Location: dashboard.php?id=".checkLoggedIn::isLoggedIn());
}
else{
  //log
  $userid = $_GET['id'];
}

include('./includes/top.php');
echo '<title>Dashboard - À La Codé</title>';
include('./includes/header.php');
?>

                <h3 class="text-dark mb-4"><?php echo htmlspecialchars(Database::query('SELECT First_Name FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['First_Name']);
                echo ' ';
                echo htmlspecialchars(Database::query('SELECT Last_Name FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Last_Name']);
                ?></h3>
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-body text-center shadow"><img class="rounded-circle mb-3 mt-4" alt="profile_image" src="<?php echo htmlspecialchars("users/U_".Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']."/face");?>" width="160" height="160">
                                <div class="mb-3">
                                <form action="./php/uploadimg.php" enctype="multipart/form-data" method="post" id="imgupload">
                                  <input type="hidden" name="captcha" value="">
                                  <input type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('filehidden').click();" value="Change Photo"/>
                                  <div align="center" id="errorimg"><?php echo '<br/>'.htmlspecialchars(isset($_GET['error'])?'Error Uploading File':'');?></div align="center">
                                  <input type="file" name="img" id="filehidden" onchange="checkFileHeader();" hidden/>
                                </form>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="text-primary font-weight-bold m-0">Projects</h6>
                            </div>
                            <div class="card-body">
                              <nav style="overflow-y:scroll;max-height: 210px;height: 210px;">
                                <?php
                                echo "<ul>";

                                $num = Database::query('SELECT COUNT(Project_Name) FROM projects WHERE MEMBER_ID = :id1', array(':id1'=>$userid))[0]['COUNT(Project_Name)'];
                                $results = Database::query('SELECT * FROM projects WHERE MEMBER_ID = :id1', array(':id1'=>$userid));

                                for($i = 0; $i < $num; $i++){
                                  if($results[$i]['Creator_ID'] == $userid){
                                    echo  '<li><a href="./project.php?id='.$results[$i]['ID'].'">'.htmlspecialchars($results[$i]['Project_Name']).'</a><br/><strong>Owner</strong></li>';
                                  }
                                  else{
                                     echo  '<li><a href="./project.php?id='.$results[$i]['ID'].'">'.htmlspecialchars($results[$i]['Project_Name']).'</a><br/><strong>Member</strong></li>';
                                   }
                                   }

                                if($num == 0){
                                    echo "<center><b>No Projects Founds.</b></center>";
                                  }
                                echo "</ul>";

                                  ?>
                              </nav>
                              <div align="center"><?php echo '<br/>'.htmlspecialchars(isset($_GET['error3'])?'Error Adding Project':'').'<br/>';?></div>
                              <form action="./php/update.php" method="post">
                              <input type="hidden" name="captcha" value="">
                              <div align="center"><input name="proj_name" class="form-control" type="text" required="" minlength="5" maxlength="32" pattern="^(?=.{5,32}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$" /></div><br/>
                              <button name="project" class="btn btn-primary btn-sm" type="submit" style="padding: 10px;margin: 0px;margin-left: 230px;margin-bottom: 0px;margin-top: 0px;">Add Project</button>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col">
                                <div class="card shadow mb-3">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold">User Settings</p>
                                    </div>
                                    <div class="card-body">
                                        <form action="./php/update.php" method="post">
                                        <input type="hidden" name="captcha" value="">
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="form-group"><label for="username"><strong>Username</strong></label><input class="form-control" type="text" required="" minlength="5" maxlength="32" pattern="^[a-zA-Z0-9_.]*$" value="<?php echo htmlspecialchars(Database::query('SELECT Username FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Username']);?>" name="username"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group"><label for="email"><strong>Email Address</strong></label><input class="form-control" type="email" required="" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" minlength="5" maxlength="50" value="<?php echo htmlspecialchars(Database::query('SELECT Email FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Email']);?>" name="email"></div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <div class="form-group"><label for="first_name"><strong>First Name</strong></label><input class="form-control" type="text" required="" minlength="2" maxlength="30" pattern="^[A-Za-zÀ-ÿ'-]+$" value="<?php echo htmlspecialchars(Database::query('SELECT First_Name FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['First_Name']);?>" name="first_name"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group"><label for="last_name"><strong>Last Name</strong></label><input class="form-control" type="text" required="" minlength="2" maxlength="30" pattern="^[A-Za-zÀ-ÿ'-]+$" value="<?php echo htmlspecialchars(Database::query('SELECT Last_Name FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Last_Name']);?>" name="last_name"></div>
                                                </div>
                                            </div>
                                            <div align="center"><?php echo '<br/>'.htmlspecialchars(isset($_GET['error1'])?'Error Updating User Settings':'');?></div>
                                            <div class="form-group"><button class="btn btn-primary btn-sm" type="submit" name="unemfnln">Save Changes</button></div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold">Contact Settings</p>
                                    </div>
                                    <div class="card-body">
                                        <form action="./php/update.php" method="post">
                                        <input type="hidden" name="captcha" value="">
                                            <div class="form-group"><label for="address"><strong>Address</strong></label><input class="form-control" type="text" required="" minlength="10" maxlength="500" pattern="^[#.0-9a-zA-Z\s,-]+$" value="<?php echo htmlspecialchars(Database::query('SELECT Address FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Address']);?>" name="address"></div>
                                            <div class="form-group"><label for="address"><strong>Contact Number</strong></label><input class="form-control" type="text" required="" minlength="8" maxlength="10" pattern="^[0-9]*$"  value="<?php echo htmlspecialchars(Database::query('SELECT Contact_Number FROM r_users WHERE ID = :id', array(':id'=>$userid))[0]['Contact_Number']);?>" name="contact"></div>
                                            <div align="center"><?php echo '<br/>'.htmlspecialchars(isset($_GET['error2'])?'Error Updating Contact Settings':'');?></div>
                                            <div class="form-group"><button class="btn btn-primary btn-sm" type="submit" name="coadd">Save&nbsp;Changes</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>

                          function checkFileHeader(){
                            var file = document.getElementById('filehidden').files;
                            if(file[0].type == "image/jpeg" && file[0].size > 0 && file[0].size <= (1024 * 1024 * 10)){
                              var fileReader = new FileReader();
                              fileReader.onloadend = function(e) {
                                var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
                                var header = "";
                                for(var i = 0; i < arr.length; i++) {
                                  header += arr[i].toString(16);
                                }
                              switch (header) {
                                case "ffd8ffe0":
                                case "ffd8ffe1":
                                case "ffd8ffe2":
                                case "ffd8ffe3":
                                case "ffd8ffe8":
                                document.getElementById('errorimg').innerHTML = "";
                                if(document.getElementById('filehidden').value != ""){
                                  document.getElementById('imgupload').submit();
                                }
                                break;
                                default:
                                document.getElementById('imgupload').reset();
                                document.getElementById('errorimg').innerHTML = "<br/>We accept only JPG file format upto 10 MB.";
                              }

                              };
                              fileReader.readAsArrayBuffer(file[0]);
                            }
                            else{
                              document.getElementById('imgupload').reset();
                              document.getElementById('errorimg').innerHTML = "<br/>We accept only JPG file format upto 10 MB.";
                            }
                          }

                        </script>
                    </div>
              <?php
              include('./includes/footer.php');
               ?>
