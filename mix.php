<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";

	include('lock.php');
	include("config.php");

//	header( "Content-type: text/plain" );

	$id = 1;
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
		$paths[$i] = sprintf( "%s/%s_%s.wav", $TMP_DIR, $row['band'], $row['user'] );
		$vols[$i] = intval( $row['volume'] );
		$pans[$i] = intval( $row['pan'] );
		$volsum += $vols[$i];
		$i++;
		$numChannels++;
	}

	for ( $i = 0; $i < $numChannels; $i++ ) {
		$vols[$i] = round( $vols[$i] / $volsum * 100 );
		$tmp_command = sprintf( "-a:%d -i %s -ea:%d -epp:%d \\\n", ($i + 1), $paths[$i], $vols[$i], $pans[$i] );
		$command = $command . $tmp_command;
	}
	$tmp_command = date( "Ymd-His" );
	$output_file = sprintf( "%s/%d_%s_mix.wav", $OUTPUT_DIR, $id, $tmp_command );
	$tmp_command = "-a:all -o $output_file";
	$command = "TERM=dumb " . $command . $tmp_command . " 2>&1";
	exec( $command, $output );
	echo "<pre>";
	echo "$command\n";
	foreach ($output as &$i) {
		echo $i . "\n";
	}
	echo "</pre>";
	echo "<a href=\"$output_file\">$output_file</a>";
?>