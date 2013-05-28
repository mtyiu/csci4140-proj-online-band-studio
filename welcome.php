<?php
	include("lock.php");
	include("config.php");
?>
<html>
<head>
	<title>Online Band System</title>
	<link rel=stylesheet type="text/css" href="css/stylesheet.css">
	<script type="text/javascript" src="js/welcome.js"></script>
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
				<p style="color:#FFF">
					Please choose one of the band rooms shown below or 
					<button onClick="create()" value="create">Create A New Band Room</button>
				</p>
				
				<p id="noRoom">Oops...there is no band room...:-(</p>
				<table width="609" border="0" cellspacing="0" cellpadding="0" id="bandroomTable">
					<tr>
						<td width="130" align="left" valign="top">
							<!-- LEFT COLUMN START -->
							<table width="130" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td id="bandSelect1" class="mainon" style="display: none;">&nbsp;</td>
							</tr>
							<tr>
								<td id="bandSelect2" class="mainoff" style="display: none;">&nbsp;</td>
							</tr>
							<tr>
								<td id="bandSelect3" class="mainoff" style="display: none;">&nbsp;</td>
							</tr>
							<tr>
								<td id="bandSelect4" class="mainoff" style="display: none;">&nbsp;</td>
							</tr>
							</table>
							<!-- LEFT COLUMN END -->
						</td>
						
						<td width="30" align="left" valign="top">&nbsp;</td>

						<td align="left" valign="top">
							<!-- RIGHT COLUMN START -->
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left" valign="top">
										<img src="images/box_left_top.gif" alt="" width="9" height="9">
									</td>
									<td align="left" valign="top" class="box_top_line">
										<img src="images/top_line.gif" alt="" width="4" height="9">
									</td>
									<td align="left" valign="top">
										<img src="images/box_right_top.gif" alt="" width="9" height="9">
									</td>
								</tr>
								<tr>
									<td rowspan="2" align="left" valign="top" class="box_left_line">&nbsp;</td>
									<td style="padding: 5px; background-color:#7b6c88;" align="left" valign="center">
										<strong>Room Information</strong>
									</td>
									<td rowspan="2" align="left" valign="top" class="box_right_line">&nbsp;</td>
								</tr>
								<tr>
									<td style="padding:5px; text-align:justify; background-color:#D2C2E0;" align="left" valign="top">
										<p><strong>Name:</strong> <span id="bandroomName"></span></p>
										<p><strong>Description:</strong> <span id="bandroomDescription"></span></p>
										<p><strong>Administrator:</strong> <span id="bandroomAdmin"></span></p>
										<p><strong>Number of Players:</strong> <span id="bandroomNumPlayers"></span></p>
										<p><strong>List of Players:</strong> <span id="bandroomPlayerList"></span></p>
										<img id="joinButton" style="cursor: pointer" align="right" src="images/join.png"></a>
									</td>
								</tr>
								<script type="text/javascript">initList();</script>
								<tr height="118">
									<td align="left" valign="top">
										<img src="images/box_left_bottom.gif" alt="" width="9" height="9">
									</td>
									<td align="left" valign="top" class="box_bottom_line">
										<img src="images/bottom_line.gif" alt="" width="4" height="9">
									</td>
									<td align="left" valign="top">
										<img src="images/box_right_bottom.gif" alt="" width="9" height="9">
									</td>
								</tr>
							</table>
							<!-- RIGHT COLUMN END -->
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
