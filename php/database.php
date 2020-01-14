<?php
class Database{



    private static function setupDBTable(){
      include('creds.php');
      $pdo = new PDO('mysql:host=127.0.0.1;charset=utf8', $unroot, $pwroot);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->query("CREATE DATABASE IF NOT EXISTS alacode");
      $pdo->query("USE alacode");
      $sql = "CREATE TABLE IF NOT EXISTS r_users(ID INT(100) AUTO_INCREMENT PRIMARY KEY NOT NULL, Username VARCHAR(32) NOT NULL UNIQUE, Password VARCHAR(512) NOT NULL, Email VARCHAR(50) NOT NULL UNIQUE, First_Name VARCHAR(30) NOT NULL,
     Last_Name VARCHAR(50) NOT NULL, Address VARCHAR(500) NOT NULL, Contact_Number VARCHAR(10) NOT NULL UNIQUE, Role INT(1) NOT NULL DEFAULT 1);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS cookies(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Cookie VARCHAR(500) NOT NULL UNIQUE, User_ID INT(100) NOT NULL, FOREIGN KEY (User_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS messages(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Sender_ID INT(255) NOT NULL, Receiver_ID INT(255) NOT NULL, New_Message TINYINT(1) NOT NULL DEFAULT 1, Message VARCHAR(500) NOT NULL, FOREIGN KEY (Sender_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION, FOREIGN KEY (Receiver_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS alerts(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Sender_ID INT(255) NOT NULL, Receiver_ID INT(255) NOT NULL, New_Alert TINYINT(1) NOT NULL DEFAULT 1, Alert VARCHAR(500) NOT NULL, FOREIGN KEY (Sender_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION, FOREIGN KEY (Receiver_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS projects(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Creator_ID INT(255) NOT NULL, Project_Name VARCHAR(500) NOT NULL, Member_ID INT(255) NOT NULL, Role INT(1) NOT NULL DEFAULT 1, FOREIGN KEY (Creator_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION, FOREIGN KEY (Member_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS comments(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Sender_ID INT(255) NOT NULL, Creator_ID INT(255) NOT NULL, Comment VARCHAR(255) NOT NULL, Project_Name VARCHAR(255) NOT NULL, FOREIGN KEY (Sender_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION, FOREIGN KEY (Creator_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS files(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Member_ID INT(255) NOT NULL, Creator_ID INT(255) NOT NULL, Filename VARCHAR(255) NOT NULL, Project_Name VARCHAR(255) NOT NULL, Reviewed TINYINT(1) NOT NULL DEFAULT 1, FOREIGN KEY (Member_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION, FOREIGN KEY (Creator_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
      $sql = "CREATE TABLE IF NOT EXISTS fpassword(ID INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, Reset_ID INT(255) NOT NULL, Email VARCHAR(255) NOT NULL, Token VARCHAR(255) NOT NULL, Valid VARCHAR(255) NOT NULL, FOREIGN KEY (Reset_ID) REFERENCES r_users(ID) ON DELETE CASCADE ON UPDATE NO ACTION);";
      $pdo->query($sql);
    }

    private static function connect(){
        include('creds.php');
      $pdo = new PDO('mysql:host=127.0.0.1;dbname=alacode;charset=utf8', $unroot, $pwroot);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;

    }

    public static function query($query, $parameters = array()){
      self::setupDBTable();
      $statement = self::connect()->prepare($query);
      $statement->execute($parameters);

      if(explode(' ', $query)[0] == 'SELECT'){
        $data = $statement->fetchAll();
        return $data;
      }

    }

}

?>
