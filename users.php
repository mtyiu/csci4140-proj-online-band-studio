<?php

function saveUsers($onlineusers_file, $path){
    $file_save=fopen($path,"w+");
    flock($file_save,LOCK_EX);
    for($line=0;$line<count($onlineusers_file);$line++){
        fputs($file_save,$onlineusers_file[$line]."\n");
    };
    flock($file_save,LOCK_UN);
    fclose($file_save);
}

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

$user=$_POST['user'];

$conn = connect();
mysql_select_db("prjband", $conn);

$result = mysql_query("SELECT * FROM band WHERE (admin = '$user') OR (player1 = '$user') OR (player2 = '$user') OR (player3 = '$user') ");
$row = mysql_fetch_array($result);

$band = $row[name];

$path = "chatroom/" . $row[admin] . "users.txt";
$path2 = "chatroom/" . $row[admin] . "room.txt";

disconnect($conn);

$onlineusers_file=file($path,FILE_IGNORE_NEW_LINES);
if (isset($_POST['user'],$_POST['oper'])){
    $oper=$_POST['oper'];
    $userexist=in_array($user,$onlineusers_file);
    if ($userexist)$userindex=array_search($user,$onlineusers_file);
    
    if($oper=="signin" && $userexist==false){
        $onlineusers_file[]=$user;
        saveUsers($onlineusers_file, $path);
        echo "signin";
        exit();
    }
    
    if($oper=="signin" && $userexist==true){
        echo "signin";
        exit();
    }
    
    if($oper=="signout" && $userexist==true){
        array_splice($onlineusers_file,$userindex,1);
        saveUsers($onlineusers_file, $path);
		
		$conn = connect();
		mysql_select_db("prjband", $conn);
		
		$image_path = "image/" . $user . "/";
					
		$result_image = mysql_query("SELECT * FROM music_sheet WHERE user = '$user'");
		$row_image = mysql_fetch_array($result_image);
					
		if($row_image[total_image] != ""){
			$no_of_image = $row_image[total_image];
			$exten = $row_image[extension];
			
			$k = 0;
			while($k < $no_of_image){
				unlink($image_path . $k . "." . $exten);
				$k++;
			}
		}
		if($row[admin] == $user){
			unlink($path);
			unlink($path2);
			
			mysql_query("DELETE FROM band WHERE admin = '$user' ");
			mysql_query("DELETE FROM config WHERE user = '$user' ");
			mysql_query("DELETE FROM mixer WHERE band = '$band' ");
			mysql_query("DELETE FROM music_info WHERE band_name = '$band' ");
			
		}else{
			$no_player = $row[no_player] - 1;
			mysql_query("UPDATE band SET no_player = $no_player WHERE admin = '$row[admin]' ");
			if($row[player1] == $user){
				mysql_query("UPDATE band SET player1 = '' WHERE admin = '$row[admin]' ");
			}

			if($row[player2] == $user){
				mysql_query("UPDATE band SET player2 = '' WHERE admin = '$row[admin]' ");
			}

			if($row[player3] == $user){
				mysql_query("UPDATE band SET player3 = '' WHERE admin = '$row[admin]' ");
			}

		}
		
		mysql_query("DELETE FROM music_sheet WHERE user = '$user' ");
		disconnect($conn);
		
        echo "signout";
        exit();
    }
    
    if($oper=="signout" && $userexist==false){
        echo "usernotfound";
        exit();
    }
}
$olu=join("<br>",$onlineusers_file);
echo $olu;

?>