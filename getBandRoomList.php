<?php
	include( 'config.php' );
	include( 'lock.php' );

	header( "Content-type: text/plain" );
	
	$results = array();
	for ($i = 0; $i < 4; $i++) {
		$sql = sprintf( "SELECT * FROM `band` WHERE band_id = %d;", $i + 1 );
		$result = mysql_query( $sql ) or die( 'MySQL query error' );
		$row = mysql_fetch_array( $result );
		$ret = array();
		if ( $row ) {
			array_push( $ret, $row[ "name" ] );
			array_push( $ret, $row[ "content" ] );
			array_push( $ret, $row[ "admin" ] );
			array_push( $ret, $row[ "no_player" ] );
			array_push( $ret, $row[ "player1" ] );
			array_push( $ret, $row[ "player2" ] );
			array_push( $ret, $row[ "player3" ] );
		}
		array_push( $results, $ret );
	}
	echo json_encode( $results );
	mysql_close($conn);
?>
