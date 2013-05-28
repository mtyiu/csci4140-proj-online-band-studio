<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";

	include('lock.php');
	include("config.php");

	header( "Content-type: text/plain" );

	$sql = "SELECT * FROM `acct` WHERE user='$login_session';";
	$result = mysql_query($sql) or die('MySQL query error #2');
	$row = mysql_fetch_array($result);
	$id = $row["band_id"];
	
	$sql = "SELECT * FROM `band` WHERE band_id='$id';";
	$result = mysql_query($sql) or die('MySQL query error #1');
	$row = mysql_fetch_array($result);
	if ( $row["admin"] != $login_session ) {
		$filename = glob( "$OUTPUT_DIR/${id}_*.wav" );
		if ( $filename )
			echo $filename[0];
		else
			echo "0";
		return;
	} else {
		$mask = "$OUTPUT_DIR/${id}_*.wav";
		array_map( "unlink", glob( $mask ) );
		$mask = "$OUTPUT_DIR/${id}_*.log";
		array_map( "unlink", glob( $mask ) );
	}

	$sql = "SELECT * FROM `mixer` WHERE band='$id';";
	$result = mysql_query($sql) or die('MySQL query error #1');

	$i = 0;
	$numChannels = 0;
	$paths = array();
	$vols = array();
	$pans = array();
	$volsum = 0;
	$command = "ecasound \\\n";
	$tmp_command;
	while ( $row = mysql_fetch_array($result) ) {
		if ( $row['user'] == "" )
			continue;
		$chkpath = sprintf( "%s/%s_%s.wav", $TMP_DIR, $row['band'], $row['user'] );
		if ( file_exists( $chkpath ) && filesize( $chkpath ) > 0 ) {
			$paths[$i] = $chkpath;
			$vols[$i] = intval( $row['volume'] );
			$pans[$i] = intval( $row['pan'] );
			$i++;
			$numChannels++;
		}
	}

	$tmp_command = date( "Ymd-His" );
	$output_file = sprintf( "%s/%d_%s_mix.wav", $OUTPUT_DIR, $id, $tmp_command );
	$output_log = sprintf( "%s/%d_%s.log", $OUTPUT_DIR, $id, $tmp_command );
	if ( $numChannels == 1 ) {
		copy( $paths[0], $output_file );
		echo "$output_file";
		exit;
	}

	for ( $i = 0; $i < $numChannels; $i++ ) {
		$tmp_command = sprintf( "-a:%d -i %s -ea:%d -epp:%d \\\n", ($i + 1), $paths[$i], $vols[$i], $pans[$i] );
		$command = $command . $tmp_command;
	}
	$tmp_command = "-a:all -o $output_file";
	$command = "TERM=dumb " . $command . $tmp_command . " 2>&1";
	exec( $command, $output );
	file_put_contents( $output_log, $output );

	echo "$output_file";
?>
