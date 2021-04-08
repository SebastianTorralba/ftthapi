<?php
try {
  $hostname = "192.168.5.10";
  $port = 3306;
  $dbname = "ipt.nocsis";
  $username = "cortes_edelar";
  $pw = "@edelarCortes19";
  $dbh = new PDO ("mysql:host=$hostname:$port;dbname=$dbname","$username","$pw",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (PDOException $e) {
  echo "Failed to get DB handle: " . $e->getMessage() . "\n";
  exit;
}
?>