<?php
include("config.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // username and password sent from Form 
    $myusername=addslashes($_POST['username']); 
    $mypassword=addslashes($_POST['password']); 
    $tbl_name="acct"; 
    $sql="SELECT * FROM $tbl_name WHERE user='$myusername' and pass='$mypassword'";
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    $active=$row['active'];
    $count=mysql_num_rows($result);
    
    
    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1)
    {
        session_register("myusername");
        $_SESSION['login_user']=$myusername;
        
        header("location: welcome.php");
    }
    else 
    {
        $error="Invalid password or user name";
    }
}else{
    $error="";
}
?>
<html>
    <head>
        <title>Online Band Room</title>
        <link rel=stylesheet type="text/css" href="414.css">

    </head> 
    <body style="color:#FFF">
        
        <div id="main_content">
            <div id="top_banner"></div>
            <div id="section2"></div>
            <div id="right_section2">
                
                <p align="center"><img src="title.png"></p>
               
                    
                    <table width="300" border="0" align="center" cellpadding="0" cellspacing="1" >
                        <tr>
                            <form name="form1" method="post">
                                <td><strong style="color:#b90000"><?php echo $error ?></strong>
                                    <table width="100%" border="0" cellpadding="3" cellspacing="1" >
                                        <tr>
                                            <td colspan="3"><strong>Member Login </strong></td>
                                        </tr>
                                        <tr>
                                            <td width="78">Username</td>
                                            <td width="6">:</td>
                                            <td width="294"><input name="username" type="text"></td>
                                        </tr>
                                        <tr>
                                            <td>Password</td>
                                            <td>:</td>
                                            <td><input name="password" type="password" ></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><input type="submit" name="Submit" value="Login"></td>
                                        </tr>
                                    </table>
                                </td>
                            </form>
                        </tr>
                    </table>
              
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </div>
            
        </div>
        
    </body>
</html>