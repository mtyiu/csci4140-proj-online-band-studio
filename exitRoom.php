<?php
	include( 'config.php' );
	include( 'lock.php' );
	
	function _post($str){
	    $val = !empty($_POST[$str]) ? $_POST[$str] : null;
	    return $val;
	}

	header( "Content-type: text/plain" );
	
	$result = mysql_query("SELECT * FROM acct WHERE user = '$login_session'");
	$row = mysql_fetch_array($result);
	$band_id = $row["band_id"];
	if ( _post("band_id") != $band_id ) {
		echo _post("band_id");
		echo "\n$band_id";
		echo "Band ID mismatch.\n";
		exit;
	}
	$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
	$row = mysql_fetch_array($result);
	if ( !$row ) {
		echo "The band ID is incorrect.";
		exit;
	}

	$isAdmin = ($row[ "admin" ] == $login_session);
	$numPlayer = $row[ "no_player" ];


	if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
		mysql_query("UPDATE acct SET band_id = 0 WHERE user = '$login_session'");
		if ( $isAdmin )
			$player = "admin";
		else if ( $row[ "player1" ] == $login_session )
			$player = "player1";
		else if ( $row[ "player2" ] == $login_session )
			$player = "player2";
		else if ( $row[ "player3" ] == $login_session )
			$player = "player3";
		
		if ( $isAdmin ) {
			mysql_query("DELETE FROM band WHERE band_id = $band_id");
			mysql_query("DELETE FROM music_info WHERE band_id = $band_id");
		} else {
			$numPlayer--;
			mysql_query("UPDATE band SET $player = '', no_player = $numPlayer WHERE band_id = $band_id");
		}
		
		mysql_query("DELETE FROM mixer WHERE user = '$login_session'");
	}

	if ( $result )
		echo "OK";
	else
		echo "Logout failed.";
?>
