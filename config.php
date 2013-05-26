<?php
	$mysql_hostname = "127.0.0.1";
	$mysql_user = "root";
	$mysql_password = "csciband";
	$mysql_database = "prjband";
	$conn = mysql_connect( $mysql_hostname, $mysql_user, $mysql_password ) or die( "Opps some thing went wrong" );
	mysql_select_db( $mysql_database, $conn ) or die( "Opps some thing went wrong" );
?>
