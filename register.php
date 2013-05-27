<?php
	include( "config.php" );
	session_start();
	
	$error = "";

	// Check if a session already exist
	if ( isset( $_SESSION['login_user'] ) ) {
		$user_check = $_SESSION['login_user'];
		$ses_sql = mysql_query("SELECT user FROM acct WHERE user='$user_check'");
		$row = mysql_fetch_array($ses_sql);
		$login_session = $row['user'];
		if( isset( $login_session ) ) {
		    header( "Location: welcome.php" );
		}
	}

	if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
		// username and password sent from Form 
		$myusername = addslashes( $_POST[ 'username' ] );
		$mypassword = addslashes( $_POST[ 'password' ] );
		$mypasswordConfirm = addslashes( $_POST[ 'passwordConfirm' ] );
		$tbl_name = "acct";
		$sql = "SELECT * FROM $tbl_name WHERE user='$myusername'";
		$result = mysql_query( $sql );
		if ( $row = mysql_fetch_array( $result ) )
			$error = "The account is already registered.";
		else if ( $mypassword != $mypasswordConfirm )
			$error = "The input passwords do not match with each other.";
		else {
			// Everything seems okay.
			$sql = "SELECT COUNT(*) AS count FROM acct";
			$result = mysql_query( $sql );
			$row = mysql_fetch_array( $result );
			$id = $row[ "count" ] + 1;
			$sql = "INSERT INTO acct VALUES ($id, '$myusername', '$mypassword', 0)";
			mysql_query( $sql );
			echo "<script type=\"text/javascript\">alert(\"Account registered. Please login!\"); window.location.assign(\"index.php\");</script>";
		}
	}
?>
<html>
	<head>
		<title>Online Band Room</title>
		<link rel=stylesheet type="text/css" href="css/stylesheet.css">
	</head>
	<body style="color:#FFF">
		<div id="main_content">
			<div id="top_banner"></div>
			<div id="section2"></div>
			<div id="right_section2">
				<p align="center">
					<img src="images/title.png">
				</p>
				<table width="300" border="0" align="center" cellpadding="0" cellspacing="1">
					<tr>
						<form name="form1" method="post">
							<td><strong style="color:#b90000"><?php echo $error; ?></strong>

								<table width="100%" border="0" cellpadding="3" cellspacing="1">
									<tr>
										<td colspan="3"><strong>Registration</strong>
										</td>
									</tr>
									<tr>
										<td width="78">Username</td>
										<td width="6">:</td>
										<td width="294">
											<input name="username" type="text">
										</td>
									</tr>
									<tr>
										<td>Password</td>
										<td>:</td>
										<td>
											<input name="password" type="password">
										</td>
									</tr>
									<tr>
										<td>Confirm Password</td>
										<td>:</td>
										<td>
											<input name="passwordConfirm" type="password">
										</td>
									</tr>
									<tr>
										<td colspan="3" align="center">
											<input type="submit" name="submit" value="Register">
											<input type="button" value="Cancel" onclick="javascript: window.location.assign('index.php');">
										</td>
									</tr>
								</table>
							</td>
						</form>
					</tr>
				</table>
			</div>
		</div>
	</body>

</html>

