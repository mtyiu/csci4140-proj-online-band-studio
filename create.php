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
			if ( $cnt ) {
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
			header( "location: admin.php" );
		}
		$sql = "UPDATE acct SET band_id = $id WHERE user = '$login_session'";
		$result = mysql_query( $sql );
	}
?>

<html>
	<head>
		<title>Online Band Room</title>
		<link rel=stylesheet type="text/css" href="414.css">
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
					<p>Welcome
						<?php
							echo $login_session;
						?>
						<button onClick="logout()" value="Logout">Logout</button>
					</p>
				</div>
				<div class="content">
					<form name="form2" action="create.php" method="post">
						<table width="600" border="0" cellspacing="4" bgcolor="#D2C2E0">
							<?php
								$sql = "SELECT * FROM `band`";
								$result = mysql_query( $sql ) or die( 'MySQL query error' );
								$no_record = mysql_num_rows( $result );
							?>
							<tr>
								<td width="200"><strong>ID:</strong>
								</td>
								<td><strong><?php echo $no_record+1?></strong>
								</td>
							</tr>
							<tr>
								<td width="200"><strong>Band room's Name(max = 8):</strong>
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
				</div>
			</div>
		</div>
	</body>
</html>

