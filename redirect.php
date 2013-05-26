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
		// Update band
		$player = "player" . ($no_pp - 1);
		$sql = "UPDATE `band` SET $player='$login_session' WHERE band_id='$id' ;";
		$result = mysql_query( $sql ) or die( 'MySQL query error #2' );
		$sql = "UPDATE `band` SET `no_player`='$no_pp' WHERE band_id='$id';";
		$result = mysql_query( $sql ) or die( 'MySQL query error #3' );

		// Update acct
		$sql = "UPDATE acct SET band_id = $id WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		
		// Update config
		$sql = "DELETE FROM config WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		$sql = "INSERT INTO config VALUES ('$login_session', 0, 0)";
		$result = mysql_query( $sql );
		
		// Update mixer
		$sql = "DELETE FROM mixer WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		$sql = "INSERT INTO mixer VALUES ($id, '$login_session', 50, 50)";
		$result = mysql_query( $sql );
		
		// Update music_sheet
		$sql = "DELETE FROM music_sheet WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		echo "3";
	}
?>
