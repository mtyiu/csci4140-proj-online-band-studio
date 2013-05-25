<?php
	include( 'lock.php' );
	include( "config.php" );

	$id = $_POST[ "f_name" ];
	$sql = "SELECT * FROM `band` WHERE band_id='$id' ;";
	$result = mysql_query( $sql ) or die( 'MySQL query error #1' );
	$row = mysql_fetch_array( $result );
	$no_pp = $row[ 'no_player' ] + 1;
	if ( $login_session == $row[ 'admin' ] ) {
		echo "1";
	} else if ( $login_session == $row[ 'player1' ] || $login_session == $row[ 'player2' ] || $login_session == $row[ 'player3' ] ) {
		echo "2";
	} else {
		$player = "player" . ($no_pp - 1);
		$sql = "UPDATE `band` SET $player='$login_session' WHERE band_id='$id' ;";
		$result = mysql_query( $sql ) or die( 'MySQL query error #2' );
		$sql = "UPDATE `band` SET `no_player`='$no_pp' WHERE band_id='$id';";
		$result = mysql_query( $sql ) or die( 'MySQL query error #3' );
		$sql = "UPDATE acct SET band_id = $id WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		echo "3";
	}
?>
