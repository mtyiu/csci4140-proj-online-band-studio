<?php
	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";
	header( "Content-type: text/plain" );

	if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" ) {
		$filename = $_GET[ "filename" ];
		$path = "$TMP_DIR/$filename.wav";
		if ( file_exists ( $path ) )
			echo "1";
		else
			echo "0";
	} else {
		echo "-1";
	}
?>
