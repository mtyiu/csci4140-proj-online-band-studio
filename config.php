<?php
 
$mysql_hostname = "137.189.89.74";
$mysql_user = "1155000538";
$mysql_password = "A4LgOmdJ";
$mysql_database = "1155000538";
$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) 
or die("Opps some thing went wrong");
mysql_select_db($mysql_database, $bd) or die("Opps some thing went wrong");
?>