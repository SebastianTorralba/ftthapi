<?php

try {
  $hostname = "192.168.5.2";
  $port = 1433;
  $dbname = "db_internet";
  $username = "iptsystem";
  $pw = "iptsystem";
  $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname","$username","$pw");
} catch (PDOException $e) {
  echo "Failed to get DB handle: " . $e->getMessage() . "\n";
  exit;
}

?>
