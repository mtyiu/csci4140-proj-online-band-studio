<?php
$message=strip_tags($_POST['message']);
$message=stripslashes($message);
$user=$_POST['user'];

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

$path = "chatroom/" . $row[admin] . "room.txt";

disconnect($conn);

$room_file=file($path,FILE_IGNORE_NEW_LINES);

$room_file[]=time()."<!@!>".$user.": ".$message;
if (count($room_file)>20)$room_file=array_slice($room_file,1);
$file_save=fopen($path,"w+");
flock($file_save,LOCK_EX);
for($line=0;$line<count($room_file);$line++){
    fputs($file_save,$room_file[$line]."\n");
};
flock($file_save,LOCK_UN);
fclose($file_save);
echo "sentok";
exit();
?>