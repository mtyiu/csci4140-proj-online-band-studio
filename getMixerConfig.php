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
	$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
	$row = mysql_fetch_array($result);
	if ( !$row ) {
		echo "The band ID is incorrect.";
		exit;
	}

	if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" ) {
		$vol = array();
		$pan = array();
		$band_id = $_GET[ "band_id" ];
		if ( $band_id != "" ) {
			$result = mysql_query( "SELECT * FROM mixer WHERE band = '$band_id'" );
			while ( $row = mysql_fetch_array( $result ) ) {
				if ( $row["user"] != "" ) {
					$vol[ $row["user"] ] = $row["volume"];
					$pan[ $row["user"] ] = $row["pan"];
				}
			}
		}
		$ret = array();
		array_push( $ret, $vol );
		array_push( $ret, $pan );
		echo json_encode( $ret );
	} else if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
		$ret = file_get_contents( "php://input" );
		$ret = json_decode( $ret );
		mysql_query( "DELETE FROM mixer WHERE band = '$band_id'" );
		for ( $i = 0; $i < count( $ret ); $i += 3 ) {
			$j = $i + 1;
			$k = $i + 2;
			$result = mysql_query("INSERT INTO mixer VALUES ($band_id, '$ret[$i]', $ret[$j], $ret[$k])") or die( "Failed to update database." );
		}
		echo "OK";
	}

?>
