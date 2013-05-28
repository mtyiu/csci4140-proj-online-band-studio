<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	include('lock.php');
	include('config.php');
	
	// Fix band ID for later use
	$result = mysql_query("SELECT * FROM acct WHERE user = '$login_session'");
	$row = mysql_fetch_array($result);
	$band_id = $row["band_id"];
	
	// Kick the user out if the band room does not exist
	$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
	$row = mysql_fetch_array($result);
	if ( !$row ) {
		header( "Location: welcome.php" );
		exit;
	}
	$band_name = $row["name"];
	$isAdmin = $row["admin"] == $login_session ? true : false;
?>
<html>
<head>
	<title>Band Room #<? echo "$band_id: $band_name" ?></title>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">

	<script type="text/javascript" src="js/bandroom.js"></script>
	<script type="text/javascript" src="js/chatroom.js"></script>
	<script type="text/javascript" src="js/music_sheet.js"></script>
	<script type="text/javascript" src="js/firebase.js"></script>
	<script type="text/javascript" src="js/RTCMultiConnection-v1.2.js"></script>
	<script type="text/javascript" src="js/RecordRTC.js"></script>
	<script type="text/javascript" src="js/audio-recorder.js"></script>
	<script type="text/javascript" src="js/webaudio.js"></script>
	<script type="text/javascript">
		var username = "<?php echo $login_session; ?>";
		var isAdmin = <?php if ( $isAdmin ) echo "true"; else echo "false"; ?>;
		window.addEventListener( "load",
			function (e) {
				initBandRoom(<?php echo $band_id; ?>);
			}, false );
		window.addEventListener( "beforeunload",
			function (e) {
				leaveBandRoom();
			}, false );
	</script>
</head>
<body>
	<!-- HEADER START -->
	<div id="bandtitle">
		<h1 align="center">Band Room #<? echo "$band_id: $band_name" ?></h1>
		<?php
			// Generate music information
			$result = mysql_query("SELECT * FROM music_info WHERE band_id = '$band_id'");
			$row = mysql_fetch_array($result);
		?>
		<table width="100%">
			<tr>
			<td width="110"><p align="left"><b>Song Title: </b></p></td>
			<td><p align="left" id="song_title"><?php echo $row["song_name"]; ?></td>
			<td width="80"><p align="right"><b>Author: </b></p></td>
			<td><p align="left" id="song_author"><?php echo $row["author"]; ?></td>
			<td width="80"><p align="right"><b>Tempo: </b></p></td>
			<td width="50"><p align="left" id="song_tempo"><?php echo $row["tempo"]; ?></td>
			<td width="50"><p align="right"><b>Key: </b></p></td>
			<td width="50"><p align="left" id="song_key"><?php echo $row["song_key"]; ?></td>
			<td width="50"><p align="center" class="jslink" onclick="editSongInfo();">Edit</p></td>
			<td width="100"><p align="right" class="jslink" onclick=" leaveBandRoom();">Leave Room</p></td>
			</tr>
		</table>
		<?php
			// Generate player list
			$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
			$row = mysql_fetch_array($result);
			function printPlayerName( $name, $login_session ) {
				switch ( $name ) {
					case "":
						echo "<font color=red>N/A</font>"; break;
					case $login_session:
						echo "<b>$name</b>"; break;
					default:
						echo "$name";
				}
			}
		?>
		<table id="playerlist" align="center">
			<tr>
				<td width="60"><p class="playerTitle">Admin: </p></td>
				<td width="100"><p id="adminName" class="adminName"><?php echo printPlayerName( $row["admin"], $login_session ); ?></p></td>
				<td width="60"><p class="playerTitle">Player 1: </td>
				<td width="100"><p id="player1Name" class="playerName"><?php echo printPlayerName( $row["player1"], $login_session ); ?></p></td>
				<td width="60"><p class="playerTitle">Player 2: </td>
				<td width="100"><p id="player2Name" class="playerName"><?php echo printPlayerName( $row["player2"], $login_session ); ?></p></td>
				<td width="60"><p class="playerTitle">Player 3: </td>
				<td width="100"><p id="player3Name" class="playerName"><?php echo printPlayerName( $row["player3"], $login_session ); ?></p></td>
			</tr>
		</table>
	</div>
	<!-- HEADER END -->

	<!-- MUSIC SHEET START -->
	<div id="music_sheet_wrapper">
		<div id="music_sheet">
			<div id="oops_music_sheet">
				<p>Oops! You didn't upload your music sheet!</p><p onclick="setUploadForm();" id="uploadMusicSheetLink" class="musicSheetLink" style="font-size: 20pt;">Upload Music Sheet</p>
			</div>
		</div>
	</div>
	<script type="text/javascript">initMusicSheet();</script>
	<!-- MUSIC SHEET END -->
	<!-- FOOTER START -->
	<div id="footer-container">
	<div style="height: 10px;"></div>
	<div id="footer">
		<div id="chatroomBox" style="display: none;">
			<table id="chatroomBoxTable">
				<tr height="5" style="background: #AE0000;">
					<td width="5" id="chatroomBoxTopLeft"></td>
					<td id="chatroomBoxTop" colspan="2"></td>
				</tr>
				<tr height="30" style="background: #AE0000; color: #FFF;">
					<td width="5" id="chatroomBoxLeftBanner"></td>
					<td style="padding-bottom: 5px;" id="chatroomBoxMainBanner"><b>Chatroom</b></td>
					<td width="150" style="cursor: pointer; text-align: right; padding-right: 5px; padding-bottom: 5px;" id="chatroomBoxPopupTd"><input type="checkbox" id="popup"> Allow auto pop-up</td>
				</tr>
				<tr>
					<td width="5" id="chatroomBoxLeft"></td>
					<td id="chatroomContentTd" colspan="2"><div id="chatroomContent"></div></td>
				</tr>
				<tr height="25">
					<td colspan="3"><input type="text" id="chatroomMsgInput" style="width: 100%; height: 25px"></td>
				</tr>
			</table>
		</div>
		<table id="footerTable">
			<tr>
			<td width="40"><b id="recordStartStop" class="startButton"></b></td>
			<td width="50"><p id="recordText" class="startRecordText">Record</p></td>
			<td><p align="center" class="jslink" onclick="toggleAudioStreams();">View All Audio Streams</p></td>
			<td id="mixer"><p class="jslink" onclick="showMixer();">Mixer</p></td>
			<td id="musicsheetedit"><p class="jslink" onclick="setUploadForm();">Edit Music Sheet</p></td>
			<td id="autoflip"><b>Auto flip:</b>&nbsp;<font id="autoflip_s"><i>Disabled</i></font> </td>
			<td id="chatroomButton"><b>Chatroom(<span id="onlineCount">1</span>)</b></td>
			</tr>
		</table>
	</div>
	</div>
	<!-- FOOTER END -->
</body>
</html>
