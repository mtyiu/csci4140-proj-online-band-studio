<?php
	include('lock.php');
	include("config.php");
	
	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";

	header( "Content-type: text/plain" );

	$sql = "SELECT * FROM `acct` WHERE user='$login_session';";
	$result = mysql_query($sql) or die('MySQL query error #2');
	$row = mysql_fetch_array($result);
	$id = $row["band_id"];

	$mask = glob( "$TMP_DIR/${id}_*.wav" );
	array_map( "unlink", $mask );
	$mask = glob( "$OUTPUT_DIR/${id}_*.wav" );
	array_map( "unlink", $mask );
	$mask = glob( "$OUTPUT_DIR/${id}_*.log" );
	array_map( "unlink", $mask );
?>
