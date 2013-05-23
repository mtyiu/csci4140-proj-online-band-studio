<?php
	$lastreceived = $_POST[ 'lastreceived' ];
	$user = $_POST[ 'user' ];

	function connect( ) {
		$conn = mysql_connect( "localhost", "root", "csciband" );
		if ( !$conn ) {
			die( 'Could not connect: ' . mysql_error() );
		}

		return $conn;
	}

	function disconnect( $conn ) {
		mysql_close( $conn );
	}

	$conn = connect();
	mysql_select_db( "prjband", $conn );

	$result = mysql_query( "SELECT * FROM band WHERE (admin = '$user') OR (player1 = '$user') OR (player2 = '$user') OR (player3 = '$user') " );
	$row = mysql_fetch_array( $result );

	$path = "chatroom/" . $row[ admin ] . "room.txt";

	disconnect( $conn );

	$room_file = file( $path, FILE_IGNORE_NEW_LINES );
	for ( $line = 0; $line < count( $room_file ); $line++ ) {
		$messageArr = split( "<!@!>", $room_file[ $line ] );
		if ( $messageArr[ 0 ] > $lastreceived )
			echo $messageArr[ 1 ] . "<br>";
	}
	echo "<SRVTM>" . $messageArr[ 0 ];
?>
