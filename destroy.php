<?php

require 'admin/phpDebug/src/Debug/Debug.php';   			// if not using composer

$debug = new \bdk\Debug(array(
    'collect' => true,
    'output' => true,
));

if(session_status() == PHP_SESSION_ACTIVE){
 session_destroy();
}

if(is_file("admin/class/Database.php")){

include ("admin/class/Database.php");
$database=new Database();
$db = $database->getConnection();

$query = "DROP TABLE `accounts`, `color`, `contacts`, `default_page`, `files`, `menu`,`page`, `password_reset_temp`, `plugins`, `roles`, `settings`, `verify`, `view_home`";
    
$stmt = $database->conn->prepare($query);

$stmt->execute();

$dir="uploads/";


if(is_dir($dir)){
  function rmdir_recursive($dir) {
    foreach(scandir($dir) as $file) {
      if ('.' === $file || '..' === $file) continue;
      if (is_dir($dir.'/'.$file)) rmdir_recursive($dir.'/'.$file);
      else unlink($dir.'/'.$file);
    }
    rmdir($dir);
  }
}

$dir="misc/";

rmdir_recursive($dir);

$page_arr=array("contact.php","index.php","login.php","destroy.php");

foreach (glob("*.php") as $row){
  if(!in_array($row,$page_arr)){
    unlink($row);
  }
}


foreach (glob("admin/inc/pages/*") as $row){
  unlink($row);
}

copy('admin/backup/index.json', 'admin/inc/pages/index.json');
chmod('admin/inc/pages/index.json',0777);



unlink("admin/class/Database.php");
}

header("Location: admin/inc/demo_dbdata.php");
exit;