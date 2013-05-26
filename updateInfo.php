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
		echo "Band ID mismatch.\n";
		$hi = _post("band_id");
		echo "$hi VS $band_id";
		exit;
	}
	$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
	$row = mysql_fetch_array($result);
	if ( !$row ) {
		echo "The band ID is incorrect.";
		exit;
	}

	if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
		mysql_select_db("prjband", $conn);
		$title = _post( "title" );
		$author = _post( "author" );
		$tempo = _post( "tempo" );
		$key = _post( "key" );
		$result = mysql_query("UPDATE music_info SET song_name = '$title', author = '$author', tempo = $tempo, song_key = '$key' WHERE band_id = '$band_id'");
	}

	if ( $result )
		echo "OK";
	else
		echo "Song information update failed.";
?>
