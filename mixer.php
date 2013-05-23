<html>
	<head>
		<title>Bandroom mixer</title>
	</head>
	
	<script type="text/javascript">
	
    function updateTextInput(val, num) {
		switch(num){
			case 1:
				document.getElementById('vol1').value=val;
				break;
			case 2:
				document.getElementById('pan1').value=val;
				break;
			case 3:
				document.getElementById('vol2').value=val;
				break;
			case 4:
				document.getElementById('pan2').value=val;
				break;
			case 5:
				document.getElementById('vol3').value=val;
				break;
			case 6:
				document.getElementById('pan3').value=val;
				break;
			case 7:
				document.getElementById('vol4').value=val;
				break;
			case 8:
				document.getElementById('pan4').value=val;
				break;
		}
		
    }
	
	</script>
	<body>
		<table cellpadding='10px'>
			<form action="mixer.php" method="post" >
				<?php
					include('lock.php');

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
					
					
					$conn = connect();
					
					mysql_select_db("prjband", $conn);
					
					$result = mysql_query("SELECT * FROM band WHERE admin = '$login_session'");
					$row = mysql_fetch_array($result);
					
					$band_id = $row[band_id];
					
					$admin = $row[admin];
					
					$result2 = mysql_query("SELECT * FROM mixer WHERE band = '$band_id'");
					$row2 = mysql_fetch_array($result2);
					
					if($row2[band] == ""){
						mysql_query("INSERT INTO mixer VALUES (0, '$band_id', '$row[admin]', 50, 50)");
					
						$i=1;
						while($i <= 3){
							$player = "player" . $i;
							echo "<script>console.log( \"INSERT INTO mixer VALUES ($i, '$band_id', '$row[$player]', 50, 50)\" );</script>";
							mysql_query("INSERT INTO mixer VALUES ($i, '$band_id', '$row[$player]', 50, 50)");
							$i++;
						}
					}else{
						$result2 = mysql_query("SELECT * FROM mixer WHERE band = '$band_id'");
						while($row2 = mysql_fetch_array($result2)){
							if($row2[id] == 0){
								$vol1 = $row2[volume];
								$pan1 = $row2[pan];
							}
							
							if($row2[id] == 1){
								$vol2 = $row2[volume];
								$pan2 = $row2[pan];
							}
							
							if($row2[id] == 2){
								$vol3 = $row2[volume];
								$pan3 = $row2[pan];
							}
							
							if($row2[id] == 3){
								$vol4 = $row2[volume];
								$pan4 = $row2[pan];
							}
						}
					}
					
					mysql_query("UPDATE mixer SET user = '$row[admin]' WHERE (id = 0) AND (band = '$band_id') ");
					$i = 1;
					while($i <= 3){
						$player = "player" . $i;
						mysql_query("UPDATE mixer SET user = '$row[$player]' WHERE (id = $i) AND (band = '$band_id') ") or die("$i");;
						$i++;
					}
					
					if($_POST["player1_vol"] != ""){
						$vol1 = $_POST["player1_vol"];
						mysql_query("UPDATE mixer SET volume = $vol1 WHERE user = '$row[admin]' ");
					}
					
					if($_POST["player1_pan"] != ""){
						$pan1 = $_POST["player1_pan"];
						mysql_query("UPDATE mixer SET pan = $pan1 WHERE user = '$row[admin]' ");
					}
					
					if($_POST["player2_vol"] != ""){
						$vol2 = $_POST["player2_vol"];
						mysql_query("UPDATE mixer SET volume = $vol2 WHERE user = '$row[player1]' ");
					}
					
					if($_POST["player2_pan"] != ""){
						$pan2 = $_POST["player2_pan"];
						mysql_query("UPDATE mixer SET pan = $pan2 WHERE user = '$row[player1]' ");
					}
					
					if($_POST["player3_vol"] != ""){
						$vol3 = $_POST["player3_vol"];
						mysql_query("UPDATE mixer SET volume = $vol3 WHERE user = '$row[player2]' ");
					}
					
					if($_POST["player3_pan"] != ""){
						$pan3 = $_POST["player3_pan"];
						mysql_query("UPDATE mixer SET pan = $pan3 WHERE user = '$row[player2]' ");
					}
					
					if($_POST["player4_vol"] != ""){
						$vol4 = $_POST["player4_vol"];
						mysql_query("UPDATE mixer SET volume = $vol4 WHERE user = '$row[player3]' ");
					}
					
					if($_POST["player4_pan"] != ""){
						$pan4 = $_POST["player4_pan"];
						mysql_query("UPDATE mixer SET pan = $pan4 WHERE user = '$row[player3]' ");
					}
				
					disconnect($conn);
					
					echo "<tr>";
						echo "<td>";
							echo "<h3>Player 1 (admin)</h3>";
						echo "</td>";
						
						if($row[player1] != ""){
							echo "<td>";
								echo "<h3>Player 2 ($row[player1]) </h3>";
							echo "</td>";
						}
						
						if($row[player2] != ""){
							echo "<td>";
								echo "<h3>Player 3 ($row[player2]) </h3>";
							echo "</td>";
						}
						
						if($row[player2] != ""){
							echo "<td>";
								echo "<h3>Player 4 ($row[player3]) </h3>";
							echo "</td>";
						}
					echo "</tr>";
					
					$width = "25px";
					
					echo "<tr>";
						echo "<td>";
							echo "<h4>Volume</h4><input type='range' required name='player1_vol' min='0' max='100' value='$vol1' step='1' onchange='updateTextInput(this.value, 1);' /><input type='text' id='vol1' value='$vol1' style='width: $width;' ><br />";
							echo "<h4>Pan</h4><input type='range' name='player1_pan' min='0' max='100' value='$pan1' step='1' onchange='updateTextInput(this.value, 2);' /><input type='text' id='pan1' value='$pan1' style='width: $width;' ><br />";
						echo "</td>";
						
						if($row[player1] != ""){
							echo "<td>";
								echo "<h4>Volume</h4><input type='range' name='player2_vol' min='0' max='100' value='$vol2' step='1' onchange='updateTextInput(this.value, 3);' /><input type='text' id='vol2' value='$vol2' style='width: $width;' ><br />";
								echo "<h4>Pan</h4><input type='range' name='player2_pan' min='0' max='100' value='$pan2' step='1' onchange='updateTextInput(this.value, 4);' /><input type='text' id='pan2' value='$pan2' style='width: $width;' ><br />";
							echo "</td>";
						}
						
						if($row[player2] != ""){
							echo "<td>";
								echo "<h4>Volume</h4><input type='range' name='player3_vol' min='0' max='100' value='$vol3' step='1' onchange='updateTextInput(this.value, 5);' /><input type='text' id='vol3' value='$vol3' style='width: $width;' ><br />";
								echo "<h4>Pan</h4><input type='range' name='player3_pan' min='0' max='100' value='$pan3' step='1' onchange='updateTextInput(this.value, 6);' /><input type='text' id='pan3' value='$pan3' style='width: $width;' ><br />";
							echo "</td>";
						}
						
						if($row[player3] != ""){
							echo "<td>";
								echo "<h4>Volume</h4><input type='range' name='player4_vol' min='0' max='100' value='$vol4' step='1' onchange='updateTextInput(this.value, 7);' /><input type='text' id='vol4' value='$vol4' style='width: $width;' ><br />";
								echo "<h4>Pan</h4><input type='range' name='player4_pan' min='0' max='100' value='$pan4' step='1' onchange='updateTextInput(this.value, 8);' /><input type='text' id='pan4' value='$pan4' style='width: $width;' ><br />";
							echo "</td>";
						}
					echo "</tr>";
				?>
				<tr>
					<td>
						<input type="submit" value="Submit" />
					</td>
				</tr>
			</form>
		</table>
	</body>
</html>
