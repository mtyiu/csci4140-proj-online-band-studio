var Request = new_request();
var database;
var per=1;
function new_request(){
    var _factories = [
        function () { return new XMLHttpRequest(); },
        function () { return new ActiveXObject("Msxml2.XMLHTTP"); },
        function () { return new ActiveXObject("Microsoft.XMLHTTP"); }
    ];
    
    for(var i = 0; i < _factories.length; i++)
    {
        try {
            var factory = _factories[i];
            var r = factory();
            if(r != null)
            {
                return r;
            }
        }
        catch(e) {
            continue;
        }
    }
    
    return null;
}
function mouseout(e){
    var tmp= document.getElementById(e);
    tmp.className="mainoff";
}
function showInfo(ev)
{
    var e=ev.id;
    console.log("target:"+e);
    console.log("s:"+ev);
    console.log("per:"+per);
    if(per!=e){
        
        Request.open("POST", "info.php", true);
        
        Request.setRequestHeader("Content-type",
                                 "application/x-www-form-urlencoded");
        
        var input = "f_name="+e;
        Request.send(input);
        Request.onreadystatechange = function () {
            if(Request.readyState == 4)
            {
                if(Request.status != 200)
                    alert("Error code = " + new String(Request.status));
                else
                {   
                    
                    database = JSON.parse(Request.responseText);
                    
                    console.log(database[e-1].name);
                    info(e);
                    
                }
            }
        }
    }
}
function join(id){
     Request.open("POST", "redirect.php", true);
        
        Request.setRequestHeader("Content-type",
                                 "application/x-www-form-urlencoded");
        
        var input = "f_name="+id;
        Request.send(input);
        Request.onreadystatechange = function () {
            if(Request.readyState == 4)
            {
                if(Request.status != 200)
                    alert("Error code = " + new String(Request.status));
                else
                {   
                   var ans = Request.responseText;
                    
                    if (ans == "1"){
                        window.location.assign("admin.php");
                    }
                    if (ans == "2"){
                        window.location.assign("client.php");
                    }
                    if (ans == "3"){
                        window.location.assign("client.php");
                    }
                    console.log(ans);
                }
            }
        }
}
function info(e){
    var target=e-1;
    var pervious = document.getElementById(per);
    var pic = "jopic"+e;
    var pic_p = "jopic"+per;
    var join_pic = document.getElementById(pic_p);
     
    join_pic.id = pic;  
    if(database[target].no_player <4){
        join_pic.style.visibility="visible";
    }
    else{
        join_pic.style.visibility="hidden";
    }
   join_pic.onclick = function(){
        join(e);
        console.log("ccc");
    };
     console.log( join_pic.onclick );
    console.log(database[target].band_id);
    console.log("Caa:"+pervious.onclick);
    pervious.className="mainoff";
    pervious.onmouseover=function(){this.className="mainon"};
    pervious.onmouseout=function(){this.className="mainoff"};
    var name = document.getElementById("name");
    name.innerHTML="<strong>Name:  </strong>"+database[target].name;
    var admin = document.getElementById("admin");
    admin.innerHTML="<strong>Administrator:  </strong>"+database[target].admin;
    var players = document.getElementById("players");
    players.innerHTML="<strong>No. of Players Available:  </strong>"+database[target].no_player;
     var desc = document.getElementById("desc");
    desc.innerHTML="<strong>Descritbtion:  </strong>"+database[target].content;
    
    var tt = document.getElementById(e);
    console.log("Caaw2:"+tt.id);
    tt.className="mainon";
     tt.onmouseover=function(){this.className="mainon"};
     tt.onmouseout=function(){this.className="mainon"};
    per=tt.id;
    
}