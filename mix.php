<?php
	include('lock.php');
	include("config.php");

	header( "Content-type: text/plain" );

	$id = 1;
	$sql = "SELECT * FROM `mixer` WHERE band='$id';";
	$result = mysql_query($sql) or die('MySQL query error #1');

	while ( $row = mysql_fetch_array($result) ) {
		echo "$row[band] ";
		echo "$row[user] ";
		echo "$row[volume] ";
		echo "$row[pan]\n";
	}
?>
