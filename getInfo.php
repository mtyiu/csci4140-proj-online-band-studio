<?php
	include( 'config.php' );
	include( 'lock.php' );

	header( "Content-type: text/plain" );
	
	if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" ) {
		$band_id = $_GET[ "band_id" ];
		if ( $band_id != "" ) {
			// Generate music information
			mysql_select_db("prjband", $conn);
			$result = mysql_query("SELECT * FROM music_info WHERE band_id = '$band_id'");
			$row = mysql_fetch_array($result);
			$info = array();
        
			array_push( $info, $row["song_name"] );
			array_push( $info, $row["author"] );
			array_push( $info, $row["tempo"] );
			array_push( $info, $row["song_key"] );
			
			// Generate player list
			$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
			$row = mysql_fetch_array($result);
			$playerlist = array();
        
			if ( $row["admin"] != "" )
				array_push( $playerlist, $row["admin"] );
			if ( $row["player1"] != "" )
				array_push( $playerlist, $row["player1"] );
			if ( $row["player2"] != "" )
				array_push( $playerlist, $row["player2"] );
			if ( $row["player3"] != "" )
				array_push( $playerlist, $row["player3"] );
        
			// Generate final JSON string
			$ret = array();
			$ret["info"] = $info;
			$ret["player"] = $playerlist;
			$str = json_encode( $ret );
			echo $str;
        
		}
	}
	mysql_close($conn);
?>
