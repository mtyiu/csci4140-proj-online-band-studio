<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	function _post($str){
	    $val = !empty($_POST[$str]) ? $_POST[$str] : null;
	    return $val;
	}

	include('lock.php');
	function connect(){
		$conn = mysql_connect("localhost", "root", "csciband");
		if(!$conn) {
			die('Could not connect: ' . mysql_error());
		}		
		return $conn;
	}
	
	function disconnect($conn){
		mysql_close($conn);
	}

	$conn = connect();
	mysql_select_db("prjband", $conn);
	$result = mysql_query("SELECT * FROM acct WHERE user = '$login_session'");
	$row = mysql_fetch_array($result);
	$band_id = $row["band_id"];
?>
<html>
    <head>
        <title>Bandroom Admin</title>
        <link rel="stylesheet" type="text/css" href="414.css">
    </head>
    
    <script type="text/javascript" src="ajax.js"></script>
    <script type="text/javascript" src="chatbox.js"></script>
    <script type="text/javascript" src="firebase.js"></script>
    <script type="text/javascript" src="RTCMultiConnection-v1.2.js"></script>
	<script type="text/javascript" src="RecordRTC.js"></script>
	<script type="text/javascript" src="audio-recorder.js"></script>
    <script type="text/javascript" src="webaudio.js"></script>
    <script type="text/javascript" src="set_client.js"></script>
    <script type="text/javascript">
        var i=0;
        var sec=0;
        var finish=0;
		var uploaded=0;
		var record=0;

        setInterval(function(){
			var img1 = document.getElementById("left");
			var img2 = document.getElementById("right");
			var user = document.getElementById("sheet1").attributes["name"].value;
			var exten = document.getElementById("sheet2").attributes["name"].value;
			var no_of_image = document.getElementById("upload").attributes["name"].value;
			var period = document.getElementById("config").attributes["name"].value;
			
            if(sec == period && finish != 1 && uploaded == 1 && period != 0 && record == 1){
				sec = 0;
                var tmp = 0;
                var path = 0;
                
                if(img1.name == i){
                    tmp = i + 2;
                    path = "image/" + user + "/" + tmp + "." + exten;
                    if(i < no_of_image - 2){
                        img1.src = path;
                        i++;
                        img1.name = tmp;
                    }else{
                        finish = 1;
                    }
                }else{
                    if(img2.name == i){
                        tmp = i + 2;
                        path = "image/" + user + "/" + tmp + "." + exten;
                        if(i < no_of_image - 2){
                            img2.src = path;
                            i++;
                            img2.name = tmp;
                        }else{
                            finish = 1;
                        }
                    }
                }
            }else{
                if(finish != 1 && record == 1){
                    sec++;
                }
            }
            
        }, 1000);
		
		function start_record(){
			<?php echo "var bandid = $band_id;"; ?>
			if(record == 0){
				record = 1;
				document.getElementById("record").value = "Stop";
				// Get player list
				var request = new XMLHttpRequest();
				request.open( "GET", "getPlayerList.php?band_id=" + bandid, false );
				request.send();
				if ( request.readyState == 4 ) {
					if ( request.status != 200 ) {
						alert( "Error code = " + request.status );
						record = 0;
					} else {
						var playerList = JSON.parse( request.responseText );
						console.log( playerList );
						startRecorder( playerList );
					}
				}
			}else{
				var img1 = document.getElementById("left");
				var img2 = document.getElementById("right");
				var user = document.getElementById("sheet1").attributes["name"].value;
				var exten = document.getElementById("sheet2").attributes["name"].value;
			
				record = 0;
				sec = 0;
				finish = 0;
				i = 0;
				document.getElementById("record").value = "Record";

				if (uploaded) {
					var path = "image/" + user + "/" + 0 + "." + exten;
					img1.src = path;
					img1.name = "0";
				
					path = "image/" + user + "/" + 1 + "." + exten;
					img2.src = path;
					img2.name = "1";
				}

				stopRecorder();
			}
			
			return false;
		}
        
    </script>
    
    <body onload="signInOut();">
		
		<?php
			if(!empty($_FILES["sheet_zip"]) && $_FILES["sheet_zip"] != ""){
				if($_FILES["sheet_zip"]["error"] > 0){
					echo "<script type='text/javascript'>alert('Error: " . $_FILES["sheet_zip"]["error"] . "');</script>";
				}else{
					$conn = connect();
					mysql_select_db("prjband", $conn);
					
					$path = "image/" . $login_session . "/";
					
					$result = mysql_query("SELECT * FROM music_sheet WHERE user = '$login_session'");
					$row = mysql_fetch_array($result);
					
					if($row["total_image"] != ""){
						$no_of_image = $row["total_image"];
						$exten = $row["extension"];
						
						$k = 0;
						while($k < $no_of_image){
							unlink($path . $k . "." . $exten);
							$k++;
						}
					}

					mkdir($path);
					
					move_uploaded_file($_FILES["sheet_zip"]["tmp_name"], $path . $_FILES["sheet_zip"]["name"]);
					
					$zipname = $_FILES["sheet_zip"]["name"];
					
					$zip = new ZipArchive;
					$res = $zip->open($path . $zipname);
					if ($res === TRUE){
						$zip->extractTo($path);
						$zip->close();
					}else{
						echo "<script type='text/javascript'>alert('Failed.');</script>";
					}
					
					$zip = zip_open($path . $zipname);
					if($zip){
						$i = 0;
						$check = 1;
						
						while($zip_entry = zip_read($zip)){
							$entry = zip_entry_name($zip_entry);
							
							$file_parts = pathinfo($path . $entry);
							
							if(($file_parts['extension'] != "jpg" && $file_parts['extension'] != "png" && $file_parts['extension'] != "gif") || ($file_parts['extension'] != $prev_exten && $i != 0)){
								if($check == 1){
									echo "<script type='text/javascript'>alert('The format of file(s) contain(s) in " . $zipname . " is/are wrong.');</script>";
								}
								
								$check = 0;
							}else{
								chmod($path . $entry, 0755);
							}
							
							$prev_exten = $file_parts['extension'];
							$i++;
						}
						
						if($check == 1){
							
							$result = mysql_query("SELECT * FROM music_sheet WHERE user = '$login_session'");
							$row = mysql_fetch_array($result);
							
							if($row["user"] != ""){
								mysql_query("UPDATE music_sheet SET extension = '$prev_exten', total_image = $i WHERE user = '$login_session' ");
							}else{
								mysql_query("INSERT INTO music_sheet VALUES ('$login_session', '$prev_exten', $i)");
							}
							
							disconnect($conn);
						}else{
							$k = 0;
							while($k < $i){
								unlink($path . $k . ".jpg");
								unlink($path . $k . ".gif");
								unlink($path . $k . ".png");
								
								$k++;
							}
						}
						
						zip_close($zip);
						unlink($path . $zipname);
						rmdir($path);
						
						echo "<script type='text/javascript'>";
						echo "window.addEventListener( 'load', function() {";
						echo "document.getElementById('left').style.visibility='visible';";
						echo "document.getElementById('right').style.visibility='visible';";
						echo "uploaded=1;";
						echo "}, false);";
						echo "</script>";
					}
					
				}
			}
		?>
	
        <table style="width: 100%; height: 100%;">
            <tr>
                <td style="width: 50%; height: 20%; border: 1px solid;">
                    <div id="song_info">
                        <form action="admin.php" method="post" >
                            <h3><font color="white">Song Information</font></h3>
							<?php							
								$conn = connect();
								
								mysql_select_db("prjband", $conn);
								
								if (_post("song_name") != "" || _post("author") != "" || _post("tempo") != "" || _post("key") != "") {
									$song_name = _post("song_name");
									$author = _post("author");
									$tempo = _post("tempo");
									$key = _post("key");
									
									if ($song_name != "")
										mysql_query("UPDATE music_info SET song_name = '$song_name' WHERE band_id = '$band_id'");
									if ($author != "")
										mysql_query("UPDATE music_info SET author = '$author' WHERE band_id = '$band_id'");
									if ($tempo != "")
										mysql_query("UPDATE music_info SET tempo = $tempo WHERE band_id = '$band_id'");
									if ($key != "")
										mysql_query("UPDATE music_info SET song_key = '$key' WHERE band_id = '$band_id'");
								}
								
								$result = mysql_query("SELECT * FROM music_info WHERE band_id = $band_id");
								$row = mysql_fetch_array($result);
								
								if ( $row["band_id"] == "" ) {
									echo "<script>console.log('insert');</script>";
									mysql_query("INSERT INTO music_info VALUES ('$band_id', 'Untitled', 'N/A', 120, 'C')");
									$result = mysql_query("SELECT * FROM music_info WHERE band_id = '$band_id'");
									$row = mysql_fetch_array($result);
								}

								$song_name = $row["song_name"];
								$author = $row["author"];
								$tempo = $row["tempo"];
								$key = $row["song_key"];
							
								disconnect($conn);
							
								echo "<table><tr>";
								echo "<td align=right><font color='white'>Name:</font></td><td><input type='text' id='song_name' name='song_name' value='$song_name' /></td>";
								echo "<td align=right><font color='white'>Author:</font></td><td><input type='text' id='author' name='author' value='$author' /><br /></td>";
								echo "</tr><tr>";
								echo "<td align=right><font color='white'>Tempo:</font></td><td><input type='text' id='tempo' name='tempo' value='$tempo' /></td>";
								echo "<td align=right><font color='white'>Key:</font></td><td><input type='text' id='key' name='key' value='$key' /><br /></td>";
								echo "</tr></table>";
							?>
                            <input type="submit" value="Set" id="setSongBtn" />
                        </form>
                    </div>
                </td>
                
                <td style="width: 50%; height: 20%; border: 1px solid;">
					<?php
					$conn = connect();
					
					mysql_select_db("prjband", $conn);
					$result = mysql_query("SELECT * FROM music_sheet WHERE user = '$login_session' ");
					if ( $result ) {
						$row = mysql_fetch_array($result);
						$no_of_image = $row["total_image"];
					} else
						$no_of_image = 0;
                    echo "<div id='upload' name=$no_of_image >";
					disconnect($conn);
					?>
                        <form enctype="multipart/form-data" action="admin.php" method="post" >
                            <h3><font color='white'>Music Sheet Upload</font></h3>
                            <font color='white'>Choose File (.zip): <input type="file" name="sheet_zip" id="sheet_zip" accept="application/x-zip-compressed" /></font><br />
                            <a href="sample.php" target="_blank"><font color='grey'>Sample File</font></a><br />
                            <input type="submit" value="Upload" />
                        </form>
                    </div>
                </td>
            </tr>
			
            <tr>
                <td style="width: 50%; height: 55%; border: 1px solid;">
					<?php
						echo "<div id='sheet1' name=$login_session>";
						$conn = connect();
					
						mysql_select_db("prjband", $conn);
						
						$result = mysql_query("SELECT * FROM music_sheet WHERE user = '$login_session' ");
						if ( $result ) {
							$row = mysql_fetch_array($result);
							$exten = $row["extension"];
						}
						$exten = "";
						
						$path = "image/" . $login_session . "/";
						
						$path0 = $path . 0 . "." . $exten;
						$path1 = $path . 1 . "." . $exten;
					
						if($exten != ""){
							$vis = "visible";
							echo "<script type='text/javascript'>";
							echo "uploaded=1;";
							echo "</script>";
						}else{
							$vis = "hidden";
						}
						echo "<img id='left' align='right' name='0' style='visibility: $vis;' />";
						
						disconnect($conn);
					?>
                    </div>
                </td>
                
                <td style="width: 50%; height: 55%; border: 1px solid;">
					<?php
						echo "<div id='sheet2' name=$exten>\n";
						
						echo "<img id='right' name='1' style='visibility: $vis;' />\n";

						echo "<script type='text/javascript'>\n";
						echo "	if (uploaded) {\n";
						echo "		var ms0Element = document.getElementById('left');\n";
						echo "		var ms1Element = document.getElementById('right');\n";
						echo "		ms0Element.src = '$path0';\n";
						echo "		ms1Element.src = '$path1';\n";
						echo "	}\n";
						echo "</script>\n";
					?>
                    </div>
                </td>
            </tr>
            
            <tr>
                <td style="width: 50%; border: 1px solid;">
                    <div id="chatroom">
                        <form onsubmit="signInOut();return false" id="signInForm">
							<?php
								echo "<input id='userName' type='text' value='$login_session'>";
							?>
                            <input id="signInButt" name="signIn" type="submit" value="Sign In">
                            <span id="signInName">User name</span>
                        </form>
                    
                        <div id="chatBox" style="background-color: white;"></div>
                        
                        <div id="usersOnLine" style="background-color: white;"></div>
                        
                        <form onsubmit="sendMessage();return false" id="messageForm">
                            <input id="message" type="text">
                            <input id="send" type="submit" value="Send">
                            <div id="serverRes"></div>
                        </form>
                    </div>
                </td>
                
                <td style="width: 50%; border: 1px solid;">
					<?php
						$conn = connect();
						
						mysql_select_db("prjband", $conn);
						$result = mysql_query("SELECT * FROM config WHERE user = '$login_session'");
						$row = mysql_fetch_array($result);
						
						if($row["user"] == ""){
							mysql_query("INSERT INTO config VALUES ('$login_session', 0, 0)");
						}else{
							if(_post("metro") != "" || _post("flip") != ""){
								$metro = _post("metro");
								$flip = _post("flip");
								
								if($metro != ""){
									mysql_query("UPDATE config SET metronome = $metro WHERE user = '$login_session'");
								}
								
								if($flip != ""){
									mysql_query("UPDATE config SET auto_flip = $flip WHERE user = '$login_session'");
								}
							}
						}
						
						$result = mysql_query("SELECT * FROM config WHERE user = '$login_session'");
						$row = mysql_fetch_array($result);
						
						$metro = $row["metronome"];
						$flip = $row["auto_flip"];
						
						echo "<div id='config' name=$flip>";
						echo "<h3><font color='white'>Config</font></h3>";
						
						echo "<form action='admin.php' method='post' >";
						
						echo "<font color='white'>Metronome: </font><input type='text' id='metro' name='metro' value=$metro />";
						echo "<font color='white'>Auto Flip (in second): </font><input type='text' id='flip' name='flip' value=$flip />";
						
						disconnect($conn);
					?>
					<p id="loading_local">Loading local media stream...</p>
					<section id="local-media-stream"></section>
					
					<p id="loading_remote">Loading remote media stream...</p>
					<section id="remote-media-streams"></section>
					<script type="text/javascript">
						<?php
							$conn = connect();					
							mysql_select_db("prjband", $conn);
							$result = mysql_query("SELECT * FROM band WHERE band_id = '$band_id'");
							$row = mysql_fetch_array($result);								
							$band_id = $row["band_id"];
							disconnect($conn);
							echo "var band_id = $band_id;\n";
							echo "var username = \"$login_session\";\n";
							$isAdmin = ( $row["admin"] == $login_session ) ? true : false;
							if ( !$isAdmin ) {
								echo "setClient( band_id, username );";
								echo "initAudio(band_id, username, false);";
								echo "joinSession();";
							} else {
								echo "initAudio(band_id, username, true);";
								echo "openSession();";
							}
						?>
					</script>
					<input type="submit" value="Set" />
					</form>
					<a href="mixer.php" target="_blank"><font color='grey'>Mixer</font></a>
					<input type='button' id="record" value='Record' onclick="start_record();" />
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
