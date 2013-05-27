<?php
	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";

	header( "Content-type: text/plain" );

	// Get file metadata
	$data = file_get_contents( "php://input" );
	
	$file["name"] = sprintf("%s_%s.wav", $_SERVER['HTTP_BAND_ID'], $_SERVER['HTTP_USERNAME']);
	$file["size"] = strlen( $data );

	// Write file contents into a temporary file
	$tmp_filepath = sprintf( "%s/%s", $TMP_DIR, $file["name"] );
	file_put_contents( $tmp_filepath, $data );

	echo "File uploaded $tmp_filepath.";
?>
