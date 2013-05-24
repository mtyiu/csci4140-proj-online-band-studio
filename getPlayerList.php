<?php
	// Global variables
	$OUTPUT_DIR = "output";
	$TMP_DIR = "tmp";

	function connect(){
		$conn = mysql_connect("localhost", "root", "csciband");
		if(!$conn)
		{
			die('Could not connect: ' . mysql_error());
		}
		
		return $conn;
	}
	
	function disconnect($conn){
		mysql_close($conn);
	}

	header( "Content-type: text/plain" );
	
	if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" ) {
		$band_id = $_GET[ "band_id" ];
		if ( $band_id != "" ) {
			// Generate player list
			$conn = connect();
			mysql_select_db("prjband", $conn);
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
        
			$str = json_encode( $playerlist );
			echo $str;
			disconnect($conn);

			// Remove all previous files
			$mask = "$TMP_DIR/$band_id_*.wav";
			array_map( "unlink", glob( $mask ) );
			$mask = "$OUTPUT_DIR/$band_id_*.wav";
			array_map( "unlink", glob( $mask ) );
		}
	}
?>
