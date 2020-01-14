<?php
class Captcha{
 public static function createRequest($token){

   $url = 'https://www.google.com/recaptcha/api/siteverify';

   $data = [
   'secret'=>'6LeKh8cUAAAAAId8Byl7lPqIK_mc7RZm22XgJUL5',
   'response'=>$token,
   'remoteip'=>$_SERVER['REMOTE_ADDR']
   ];

   $options = array(
      'http'=>array(
        'header'=>'Content-type: application/x-www-form-urlencoded\r\n',
        'method'=>'POST',
        'content'=>http_build_query($data)
      )
   );

   $context = stream_context_create($options);
   $response = file_get_contents($url, false, $context);

   $returnval = json_decode($response, true);
   $successscore = array($returnval['success'], $returnval['score']);
   return $successscore;
 }
}
?>
