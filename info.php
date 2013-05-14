
<?php 

header("Content-type: text/plain");

$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpass = 'csciband';
$dbname = 'prjband';
try {
     
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser, $dbpass);// open a mysql database connection
    $id = $_POST["f_name"];
    $query = $dbh->prepare( "SELECT * FROM `band`;");
    $query->execute(); 

    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

    $dbh=null;
    }
catch(PDOException $e){
 
    echo $e->getMessage();
}
?>
