<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include( 'lock.php' );
	include( "config.php" );
	$bandroomExist = array();
	for ($i = 0; $i < 4; $i++)
		$bandroomExist[$i] = false;
	if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" && $_POST[ 'bandname' ] != "" ) {
		$myband = addslashes( $_POST[ 'bandname' ] );
		$des = addslashes( $_POST[ 'de' ] );
		$tbl_name = "band";
		$numRecord = 0;
		for ($i = 0; $i < 4; $i++) {
			$sql = sprintf( "SELECT * FROM `band` WHERE band_id = %d;", $i + 1 );
			$result = mysql_query( $sql ) or die( 'MySQL query error' );
			$cnt = mysql_num_rows( $result );
			if ( $cnt == 1 ) {
				$bandroomExist[$i] = true;
				$numRecord++;
			} else {
				$id = $i + 1;
				break;
			}
		}
		$sql = "INSERT INTO $tbl_name (band_id, name, admin, no_player, content, player1, player2, player3) VALUES ('$id', '$myband', '$login_session', 1, '$des', '', '', '')";
		$result = mysql_query( $sql );
		if ( $result ) {
			header( "location: bandroom.php" );
		}
		$sql = "UPDATE acct SET band_id = $id WHERE user = '$login_session'";
		$result = mysql_query( $sql );
		
		// Update mixer
		$sql = "INSERT INTO mixer VALUES ($id, '$login_session', 50, 50)";
		$result = mysql_query( $sql );
		
		// Update music_info
		$sql = "INSERT INTO music_info VALUES ($id, 'Untited', 'N/A', 100, 'C')";
		$result = mysql_query( $sql );
	}
?>

<html>
	<head>
		<title>Create New Band Room - Online Band System</title>
		<link rel=stylesheet type="text/css" href="css/stylesheet.css">
		<script language="JavaScript">
			function logout() {
				window.location.assign("welcome.php");
			}
			function back() {
				window.location.assign("welcome.php");
			}
		</script>
	</head>
	<body>
		<div id="main_content">
			<div id="top_banner"></div>
			<div id="section2"></div>
			<div id="right_section2">
				<div class="title">
					<p>
						Welcome <font color="orange"><?php echo $login_session; ?></font>! 
						<button onclick="logout()" value="Logout">Logout</button>
					</p>
				</div>
				<div class="content">
					<form name="form2" action="create.php" method="post">
						<table width="600" border="0" cellspacing="4" bgcolor="#D2C2E0">
							<?php
								$new_id = 0;
								for ($i = 0; $i < 4; $i++) {
									$sql = sprintf( "SELECT * FROM `band` WHERE band_id = %d;", $i + 1 );
									$result = mysql_query( $sql ) or die( 'MySQL query error' );
									$cnt = mysql_num_rows( $result );
									if ( $cnt != 1 ) {
										$new_id = $i + 1;
										break;
									}
								}
								if ( $new_id == 0 ) {
									echo "<script type=\"text/javascript\">alert('Sorry. No new room can be made...:-('); window.location.assign('welcome.php');</script>";
									return;
								}
							?>
							<tr>
								<td width="200"><strong>ID:</strong>
								</td>
								<td><strong><?php echo $new_id; ?></strong>
								</td>
							</tr>
							<tr>
								<td width="200"><strong>Band Room's Name:</strong>
								</td>
								<td><strong><input name="bandname" type="text" maxlength="8"></strong>
								</td>
							</tr>
							<tr>
								<td width="200"><strong>Description:</strong>
								</td>
								<td><strong><input name="de" type="text" maxlength="300"></strong>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="submit" name="Submit" value="Create">
								</td>
							</tr>
						</table>
					</form>
					<button onClick="back()" value="back">Back</button>
					<p style="height: 20px;">&nbsp;</p>
					<p id="comments">CSCI 4140 Project - Online Band System by Gangnam Style<br />2013 Spring</p>
				</div>
			</div>
		</div>
	</body>
</html>

