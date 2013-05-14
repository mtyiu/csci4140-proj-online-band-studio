<?php
include('lock.php');
include("config.php");
?>


<html>
    <head>
        <title>Online Band Room</title>
        <link rel=stylesheet type="text/css" href="414.css">
        <script language="JavaScript">
            function logout()
            {
                window.location.assign("logout.php");
            }
            function create(no)
            {    
                if(no == 4){
                    alert("There are too much rooms");
                }
                else{
                     window.location.assign("create.php")
                }
                   
            }
        </script>
         <script language="javascript" src="action.js"></script>
    </head> 
    <body >
        
        <div id="main_content">
            <div id="top_banner"></div>
            <div id="section2"></div>
            <div id="right_section2">
                
                <div class="title">
                    
                    <p> Welcome <?php echo $login_session; ?>      <button  onClick="logout()" value="Logout">Logout</button>
                    
                 
                </div>               
                <div class="content">
                   <?php
                        $sql = "SELECT * FROM `band` ;";
                        $result = mysql_query($sql) or die('MySQL query error');
                        $no_record = mysql_num_rows($result); 
                        ?>
                    <p style="color:#FFF">Please choose one of band rooms showed below or 
                        <button  onClick="create(<?php echo $no_record?>)" value="create">Create New Band room</button> </p>
                   
                     <?php
                       
                        
                            if ($no_record==0){
                                echo "<p>There is no room....</p>";
                            }
                        else {
                        ?>
                  
                    <table width="609" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="130" align="left" valign="top">
                      <table width="130" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top">
                            <table width="130" border="0" cellspacing="0" cellpadding="0">
                            <?php
                       
                        for($i=1; $i<=$no_record;$i++){
                            $row = mysql_fetch_array($result);
                            
                                $te=$row['band_id']; 
                            if($no_record==1) {         
                                echo "<tr >";
                            echo "<td id=$no_record style=height:37px; class=mainon align=center valign=middle>{$row['name']}</td>";
                                echo "</tr>";
                            }
                            else{
                                
                                if($i==1){
                                    echo "<tr>";
                                 echo "<td id=$i class=mainon onmouseover=this.className='mainon' onmouseout=this.className='mainon' onclick=showInfo(this) align=center valign=middle >{$row['name']}</td>";
                                    echo "</tr>";
                                }
                                else{
                                    if($i==$no_record){
                                        echo "<tr>";
                                    echo "<td id=$i style=height:37px; class=mainoff onmouseover=this.className='mainon' onmouseout=this.className='mainoff' onclick=showInfo(this) align=center valign=middle>{$row['name']}</td>";
                                       echo "</tr>";
                                    }
                                    else{
                                        echo "<tr>";
                                    echo "<td id=$i class=mainoff onmouseover=this.className='mainon' onmouseout=this.className='mainoff' onclick=showInfo(this) align=center valign=middle>{$row['name']}</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                        }
                        ?>
                         
                           
                        </table></td>
                      </tr>
                      <tr>
                        <td height="32" align="left" valign="top">&nbsp;</td>
                      </tr>
                      
                  </table></td>
                  <td width="30" align="left" valign="top">&nbsp;</td>
                  <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td align="left" valign="top"><img src="box_left_top.gif" alt="" width="9" height="9" /></td>
                              <td align="left" valign="top" class="box_top_line"><img src="top_line.gif" alt="" width="4" height="9" /></td>
                              <td align="left" valign="top"><img src="box_right_top.gif" alt="" width="9" height="9" /></td>
                            </tr>
                            <tr>
                              <td rowspan="2" align="left" valign="top" class="box_left_line">&nbsp;</td>
                                <td style="padding:5px; text-align:justify; background-color:#7b6c88; " align="left" valign="top" ><strong>Room Information</strong></td>
                              <td rowspan="2" align="left" valign="top" class="box_right_line" >&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="padding:5px; text-align:justify; background-color:#D2C2E0;" align="left" valign="top" >
                                 <?php
                                $sql = "SELECT * FROM `band` WHERE band_id=1 ;";
                                $result = mysql_query($sql) or die('MySQL query error');   
                                $row = mysql_fetch_array($result);
                                echo "<p id=name ><strong>Name:</strong> {$row['name']}</p>";
                             echo "<p id=admin ><strong>Administrator:</strong> {$row['admin']}</p>";
                             echo "<p id=players ><strong>No. of Players Available:</strong> {$row['no_player']}</p>";
                            echo "<p id=desc ><strong>Descritbtion:</strong> {$row['content']}</p>";
                               ?>   
                                    <img align="right" src="join.png"  onmouseover="this.src='join2.png'" onmouseout="this.src='join.png'">
                                  
                              
                                <p>&nbsp;</p></td>
                            </tr>
                            <tr>
                              <td height="118" align="left" valign="top"><img src="box_left_bottom.gif" alt="" width="9" height="9" /></td>
                              <td align="left" valign="top" class="box_bottom_line"><img src="bottom_line.gif" alt="" width="4" height="9" /></td>
                              <td align="left" valign="top"><img src="box_right_bottom.gif" alt="" width="9" height="9" /></td>
                            </tr>
                        </table><table width="100%" border="0" cellspacing="0" cellpadding="0">
                           
                            
                         
                            </table></td>
                      </tr>
                      
                  </table></td>
                </tr>
            </table>
                    <?php } ?>  
                    <p></p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p> 
                </div>
            </div>
            
        </div>
        
    </body>
</html>