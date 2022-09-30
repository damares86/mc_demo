<?php
// require '../phpDebug/src/Debug/Debug.php';   			// if not using composer

// $debug = new \bdk\Debug(array(
//     'collect' => true,
//     'output' => true,
// ));


/*
==========================================================================

Mini Cms is a project by damares86 (https://github.com/damares86/mini_cms)

==========================================================================
*/


if(!$_POST['email']||!$_POST['password']){
  header("Location: ../inc/demo_dbdata.php?err=missing");
  exit;
} 

if(!is_file('../class/Database.php')){
  $db_name="cbnqfelh_minicmsdemo";
  $username="cbnqfelh_minicmsdemo";
  $db_password="Salomon-86";
  $host="localhost";
  $file_handle = fopen('../class/Database.php', 'w');
  fwrite($file_handle, '<?php');
  fwrite($file_handle, "\n");
  fwrite($file_handle, "class Database{");
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'private $db_name="'.$db_name.'";');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'private $username="'.$username.'";');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'private $password="'.$db_password.'";');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'private $host="'.$host.'";');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'public $conn;');
  fwrite($file_handle, "\n");
  fwrite($file_handle, "\n");
  fwrite($file_handle, "public function getConnection(){");
  fwrite($file_handle, "\n");
  fwrite($file_handle, '$this->conn = null;');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'try{');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '}catch(PDOException $exception){');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'echo "Connection error: " . $exception->getMessage();');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '}');
  fwrite($file_handle, "\n");
  fwrite($file_handle, 'return $this->conn;');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '}');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '}');
  fwrite($file_handle, "\n");

  fwrite($file_handle, '?>');
  
}


chmod('../class/Database.php',0777);

spl_autoload_register('autoloader');

function autoloader($class){
	include("../class/$class.php");
}

$database = new Database();
$db = $database->getConnection();

$user_email=$_POST['email'];
$password=$_POST['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$user=new User($db);

if(!is_file('../core/site.php')){
  $file_handle = fopen('../core/site.php', 'w');
  fwrite($file_handle, '<?php');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '$site= "'.$_SERVER['SERVER_NAME'].'";');
  fwrite($file_handle, "\n");
  fwrite($file_handle, '?>');
}

chmod('../core/site.php',0777);

/////////////////////////////////////////////////////////////

// create the db tables if not exists

/////////////////////////////////////////////////////////////

// creating user's table 
$db->query("CREATE TABLE IF NOT EXISTS accounts
                           ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                             username VARCHAR(50) NOT NULL,
                             password VARCHAR(255) NOT NULL,
                             email VARCHAR(255) NOT NULL,
                             rolename VARCHAR(50) NOT NULL,
                             last_login datetime DEFAULT CURRENT_TIMESTAMP)");

// creating role's table
$db->query("CREATE TABLE IF NOT EXISTS roles
                           ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                             rolename VARCHAR(255) NOT NULL)");

$db->query("INSERT INTO roles
                            (id, rolename)
                            VALUES ('1','Admin')
                            ");

$db->query("INSERT INTO roles
                            (id, rolename)
                            VALUES ('2','Manager')
                            ");
$db->query("INSERT INTO roles
                            (id, rolename)
                            VALUES ('3','Editor')
                            ");

$db->query("CREATE TABLE settings (
  id int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  site_name VARCHAR(255) NOT NULL,
  site_description VARCHAR(255) NOT NULL,
  use_text INT (1) DEFAULT '1',
  footer TEXT NOT NULL,
  dashboard_language VARCHAR(255) NOT NULL,
  theme VARCHAR(255) NOT NULL,
  dm INT (1) DEFAULT 1)");

$db->query("CREATE TABLE IF NOT EXISTS page
              ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                page_name VARCHAR(255) NOT NULL,
                no_mod INT (1) DEFAULT '0',
                layout VARCHAR(255) NOT NULL DEFAULT 'default',
                header INT (1) DEFAULT '1',
                use_name INT (1) DEFAULT '1',
                use_desc INT (1) DEFAULT '1',
                img VARCHAR(255) NOT NULL DEFAULT 'visual.jpg',
                counter INT(5) DEFAULT '1',
                content JSON)
                ");

$db->query("CREATE TABLE IF NOT EXISTS default_page
              ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                page_name VARCHAR(255) NOT NULL,
                header INT (1) DEFAULT '1',
                use_name INT (1) DEFAULT '1',
                use_desc INT (1) DEFAULT '1',
                img VARCHAR(255) NOT NULL DEFAULT 'visual.jpg')
                ");


$db->query("CREATE TABLE IF NOT EXISTS menu
                            ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              pagename VARCHAR(255) NOT NULL,
                              inmenu INT(1) DEFAULT '0',
                              itemorder INT ( 5 ) DEFAULT 0,
                              parent BOOLEAN DEFAULT 0,
                              childof VARCHAR(255) DEFAULT 'none')
                              ");

chmod("../inc/func/regCheck.php",0777);


$db->query("CREATE TABLE IF NOT EXISTS plugins
                            ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              plugin_name VARCHAR(255) NOT NULL,
                              link VARCHAR(255) DEFAULT NULL,
                              second_page VARCHAR(255) NULL,
                              description VARCHAR(255) NOT NULL,
                              icon VARCHAR(255) NOT NULL,
                              title VARCHAR(255) NOT NULL,
                              sub_show_title VARCHAR(255) DEFAULT NULL,
                              sub_show_link VARCHAR(255) DEFAULT NULL,
                              sub_add_title VARCHAR(255) DEFAULT NULL,
                              sub_add_link VARCHAR(255) DEFAULT NULL,                              
                              active INT(1) NOT NULL)
                              ");

$db->query("CREATE TABLE IF NOT EXISTS view_home
                ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  name_function VARCHAR(255) NOT NULL)");

$db->query("INSERT INTO view_home
          (id, name_function)
          VALUES ('1','color')
          ");


$db->query("CREATE TABLE IF NOT EXISTS files
                            ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                              filename VARCHAR(255) NOT NULL,
                              title VARCHAR(255) NOT NULL)
                              ");

$db->query("CREATE TABLE IF NOT EXISTS color
                              ( id INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                color VARCHAR(50) NOT NULL)");
$db->query("INSERT INTO color
                              (id, color)
                              VALUES ('1','#008db1')
                              ");
$db->query("INSERT INTO color
                            (id, color)
                            VALUES ('2','#00cc99')
                            ");
$db->query("INSERT INTO color
                           (id, color)
                           VALUES ('3','#000000')
                           ");

$db->query("INSERT INTO color
                            (id, color)
                            VALUES ('4','#ffffff')
                            ");

$db->query("INSERT INTO accounts
(id, username, password,email,rolename)
VALUES ('1','admin', '". $password_hash ."','". $user_email ."','Admin')
");


$db->query("INSERT INTO settings
(id, site_name, site_description,footer,dashboard_language,theme,dm)
VALUES ('1','Mini Cms', 'Create your own website','Your footer text','en','damares','1')
");

$db->query("INSERT INTO page 
(id, page_name, no_mod, layout, header, use_name, use_desc, img, counter,content) 
VALUES ('1','index', '1', 'default', '1', '1', '1', 'visual.jpg', '1',
'[{\"name\":\"index\"},{\"block1_type\":\"t\",\"block1\":\"<p>This is your homepage.</p>\",\"block1_bg\":\"none\",\"block1_text\":\"none\"}]')
");

$db->query("INSERT INTO default_page 
(id, page_name, header, use_name, use_desc, img) 
VALUES ('1','Login', '1', '1', '1', 'visual.jpg')
");

$db->query("INSERT INTO default_page 
(id, page_name, header, use_name, use_desc, img) 
VALUES ('2','Contact', '1', '1', '1', 'visual.jpg')
");


$db->query("INSERT INTO default_page 
(id, page_name, header, use_name, use_desc, img) 
VALUES ('3','Gallery', '1', '1', '1', 'visual.jpg')
");

$db->query("INSERT INTO menu 
(id, pagename, inmenu,itemorder,parent,childof) 
VALUES ('1','index', '0','0','1','none')
");


$db->query("INSERT INTO menu 
(id, pagename, inmenu,itemorder,parent,childof) 
VALUES ('2','Login', '1','0','1','none')
");

$db->query("INSERT INTO menu 
(id, pagename, inmenu,itemorder,parent,childof) 
VALUES ('3','Contact', '0','0','1','none')
");

$db->query("CREATE TABLE `password_reset_temp` (
  `email` varchar(250) NOT NULL PRIMARY KEY,
  `token` varchar(250) NOT NULL,
  `expDate` datetime NOT NULL
)
");

$db->query("CREATE TABLE `verify` (
  `id` INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `public` varchar(250) NOT NULL,
  `secret` varchar(250) NOT NULL,
  `active` INT ( 5 ) DEFAULT 0
)");

$db->query("INSERT INTO verify
(id, public, secret, active) 
VALUES ('1','PUBLIC_KEY', 'SECRET_KEY', '0')
");


$db->query("CREATE TABLE `contacts` (
  `id` INT ( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `label` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
)");

$db->query("INSERT INTO contacts 
(id, label, email) 
VALUES ('1','noreply', 'noreply@yoursite.com')
");

$db->query("INSERT INTO contacts 
(id, label, email) 
VALUES ('2','Info', 'info@yoursite.com')
");


header("Location: ../index.php");
