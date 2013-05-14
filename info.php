
<?php 

header("Content-type: text/plain");

$dbhost = '137.189.89.74';
$dbuser = '1155000538';
$dbpass = 'A4LgOmdJ';
$dbname = '1155000538';
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
