var playerlist;
var bandId;
var updateItv;

/* DOM manipulation */
var element;
var elementList;
var recordTextElement;
var recordButtonElement;
var wrapper;
var bglayer;
var promptlayer;
var h2Element;
var tableElement;
var trElements;
var tdElements;
var tdWidth;
var tdAlign;
var okPElement;
var loaded;
var left;

function initBandRoom( bandid ) {
	bandId = bandid;
	element = new Array();
	elementList = [ "song_title", "song_author", "song_tempo", "song_key",
					"adminName", "player1Name", "player2Name", "player3Name", "onlineCount" ];
	
	for ( var i = 0; i < elementList.length; i++ )
		element[ elementList[i] ] = document.getElementById( elementList[i] );
	recordTextElement = document.getElementById( "recordText" );
	recordButtonElement = document.getElementById( "recordStartStop" );

	updateItv = window.setInterval( function() { update(false); }, 1000 );
	preparePromptLayer();
	initAudio( bandid );
	if ( isAdmin ) {
		openSession();
	} else {
		joinSession();
	}
	initChatRoom();
	update(true);
	loaded = true;
	left = false;
}

function leaveBandRoom() {
	if ( !left ) {
		var xhr = new XMLHttpRequest();
		xhr.open( "POST", "exitRoom.php", false );
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		input = "band_id=" + bandId;
		xhr.send( input );
		if ( xhr.readyState == 4 ) {
			if ( xhr.status != 200 )
				console.log( "leaveBandRoom(): Request failed with code " + new String( xhr.status ) );
			else {
				if ( xhr.responseText != "OK" ) {
					console.log( xhr.responseText );
				}
			}
		}
		left = true;
	}
	window.location.assign( "welcome.php" );
}

function preparePromptLayer() {
	// DOM for prompt layer and background layer
	wrapper = document.createElement("div");
	wrapper.id = "wrapper";
	document.body.appendChild( wrapper );

	bglayer = document.createElement("div");
	bglayer.id = "bglayer";
	wrapper.appendChild( bglayer );

	promptlayer = document.createElement("div");
	promptlayer.id = "promptlayer";
	wrapper.appendChild( promptlayer );
	
	h2Element = document.createElement("h2");
	promptlayer.appendChild( h2Element );

	wrapper.style.display = "";
	setPromptLayer();
	wrapper.style.display = "none";
	window.addEventListener( "resize", setPromptLayer, false );
	window.addEventListener( "scroll", setPromptLayer, false );
}

function setPromptLayer( e ) {
	var width = document.body.clientWidth ? document.body.clientWidth : window.innerWidth;
	var height = document.body.clientHeight ? document.body.clientHeight : window.innerHeight;
	bglayer.style.width = width;
	bglayer.style.height = height;
	bglayer.style.left = window.pageXOffset;
	bglayer.style.top = window.pageYOffset;

	promptlayer.style.width = width * 0.5;
	promptlayer.style.height = promptlayer.myHeight ? promptlayer.myHeight : height * 0.5;
	promptlayer.style.left = window.pageXOffset + Math.round( (width - width * 0.5) / 2 );
	promptlayer.style.top = window.pageYOffset + Math.round( (height - parseInt(promptlayer.style.height)) / 2 );
}

var mixerTable;
var mixerTr;
var mixerTd;
var panElement;
var volElement;
var panTexElement;
var volTextEletment;
function showMixer() {
	if ( !loaded ) return;
	h2Element.innerHTML = "Mixer Configuration";
	promptlayer.myHeight = "280px";
	setPromptLayer();
	wrapper.style.display = "";
	
	mixerTable = document.createElement( "table" );
	mixerTable.width = "95%";
	mixerTable.border = "0";
	mixerTable.cellSpacing = "1";
	mixerTable.align = "center";
	mixerTr = new Array();
	mixerTd = new Array();
	var mixerTdWidth = [ "15%", "20%", "20%", "20%", "20%" ];
	for ( var i = 0; i < 3; i++ ) {
		mixerTr[i] = document.createElement( "tr" );
		for ( var j = 0; j < 5; j++ ) {
			mixerTd[i * 5 + j] = document.createElement( "td" );
			mixerTd[i * 5 + j].width = mixerTdWidth[j];
			mixerTd[i * 5 + j].align = "center";
			mixerTr[i].appendChild( mixerTd[i * 5 + j] );
		}
		mixerTd[i * 5].style.backgroundColor = "#DDD";
		mixerTable.appendChild( mixerTr[i] );
	}
	mixerTr[0].style.height = "30px";
	mixerTr[2].style.height = "200px";
	mixerTd[0 * 5].innerHTML = "<b>Player</b>";
	mixerTd[1 * 5].innerHTML = "<b>Pan</b>";
	mixerTd[2 * 5].innerHTML = "<b>Volume</b>";

	panTextElement = new Array();
	volTextElement = new Array();
	panElement = new Array();
	volElement = new Array();
	for ( var i = 0; i < playerlist.length; i++ ) {
		mixerTd[i + 1].innerHTML = playerlist[i];
		panTextElement[i] = document.createElement( "input" );
		panTextElement[i].type = "text";
		panTextElement[i].style.width = "50px";
		panTextElement[i].value = "50";
		panTextElement[i].i = i;
		panTextElement[i].onchange = function( e ) {
			var i = this.i;
			if ( (this.value.toString().search(/^[0-9]+$/) == 0) )
				panElement[i].value = this.value;
			else
				this.value = panElement[i].value;
		};
		panElement[i] = document.createElement( "input" );
		panElement[i].type = "range";
		panElement[i].className = "pan";
		panElement[i].value = "50";
		panElement[i].min = 0;
		panElement[i].max = 100;
		panElement[i].step = 1;
		panElement[i].i = i;
		panElement[i].onchange = function( e ) {
			var i = this.i;
			panTextElement[i].value = this.value;
		};
		volTextElement[i] = document.createElement( "input" );
		volTextElement[i].type = "text";
		volTextElement[i].style.width = "50px";
		volTextElement[i].value = "50";
		volTextElement[i].i = i;
		volTextElement[i].onchange = function( e ) {
			var i = this.i;
			if ( (this.value.toString().search(/^[0-9]+$/) == 0) )
				volElement[i].value = this.value;
			else
				this.value = volElement[i].value;
		};
		volElement[i] = document.createElement( "input" );
		volElement[i].type = "range";
		volElement[i].className = "vol";
		volElement[i].value = "50";
		volElement[i].min = 0;
		volElement[i].max = 100;
		volElement[i].step = 1;
		volElement[i].i = i;
		volElement[i].onchange = function( e ) {
			var i = this.i;
			volTextElement[i].value = this.value;
		};
		
		mixerTd[1 * 5 + i + 1].appendChild( panTextElement[i] );
		mixerTd[1 * 5 + i + 1].appendChild( panElement[i] );
		mixerTd[2 * 5 + i + 1].appendChild( volTextElement[i] );
		mixerTd[2 * 5 + i + 1].appendChild( volElement[i] );
	}
	var xhr = new XMLHttpRequest();
	xhr.open( "GET", "getMixerConfig.php?band_id=" + bandId.toString(), false );
	xhr.send();
	if (xhr.readyState == 4) {
		if ( xhr.status != 200 )
			console.log( "showMixer(): Request failed with code " + new String( xhr.status ) );
		else {
			var ret = JSON.parse( xhr.responseText );
			var vol = ret[0];
			var pan = ret[1];
			var playerName;
			for ( var i = 0; i < 4; i++ ) {
				playerName = mixerTd[i + 1].innerHTML;
				if ( vol[playerName] != undefined ) {
					volElement[i].value = volTextElement[i].value = vol[playerName];
					panElement[i].value = panTextElement[i].value = pan[playerName];	
				}
			}
			
		}
	}
	
	promptlayer.appendChild( mixerTable );

	okPElement = document.createElement("p");
	okPElement.align = "center";
	okPElement.innerHTML = "<a href=\"javascript:submitMixerConfig();\">Submit</a>&nbsp;&nbsp;&nbsp;<a href=\"javascript:hideMixer();\">Cancel</a>";
	promptlayer.appendChild( okPElement );

	promptlayer.myHeight = "400px";
	setPromptLayer();
	wrapper.style.display = "";
}

function submitMixerConfig() {
	var xhr = new XMLHttpRequest();
	var input = new Array();
	var playerName;
	for ( var i = 0; i < 4; i++ ) {
		playerName = mixerTd[i + 1].innerHTML;
		if ( playerName != "" ) {
			input.push( playerName );
			input.push( volElement[i].value );
			input.push( panElement[i].value );
		}
	}
	xhr.open( "POST", "getMixerConfig.php", false );
	xhr.send( JSON.stringify(input) );
	if (xhr.readyState == 4) {
		if ( xhr.status != 200 )
			console.log( "submitMixerConfig(): Request failed with code " + new String( xhr.status ) );
		else {
			if ( xhr.responseText == "OK" ) {
				hideMixer();
			} else {
				okPElement.innerHTML = "<p><font color=red><b>[Error] " + xhr.responseText + "</b></font></p>" + "<a href=\"javascript:cancelSongInfo();\">Cancel</a>";
			}
		}
	}
}

function hideMixer() {
	var numPlayer = 0;
	for ( var i = 0; i < 4; i++ ) {
		playerName = mixerTd[i + 1].innerHTML;
		if ( playerName != "" )
			numPlayer++;
	}
	for ( var i = 0; i < numPlayer; i++ ) {
		mixerTd[1 * 5 + i + 1].removeChild( panTextElement[i] );
		mixerTd[1 * 5 + i + 1].removeChild( panElement[i] );
		mixerTd[2 * 5 + i + 1].removeChild( volTextElement[i] );
		mixerTd[2 * 5 + i + 1].removeChild( volElement[i] );
		panTextElement[i] = undefined;
		panElement[i] = undefined;
		volTextElement[i] = undefined;
		volElement[i] = undefined;
	}
	for ( var i = 0; i < 3; i++ ) {
		for ( var j = 0; j < 5; j++ ) {
			mixerTr[i].removeChild( mixerTd[i * 5 + j] );
			mixerTd[i * 5 + j] = undefined;
		}
		mixerTable.removeChild( mixerTr[i] );
		mixerTr[i] = undefined;
	}
	mixerTr = undefined;
	mixerTd = undefined;
	panElement = undefined;
	volElement = undefined;
	panTextElement = undefined;
	volTextElement = undefined;
	promptlayer.removeChild( mixerTable );
	mixerTable = undefined;
	promptlayer.removeChild( okPElement );
	wrapper.style.display = "none";
}

function editSongInfo() {
	if ( !loaded ) return;
	h2Element.innerHTML = "Edit Song Information";

	tableElement = document.createElement( "table" );
	tableElement.width = "90%";
	tableElement.border = "0";
	tableElement.align = "center";
	promptlayer.appendChild( tableElement );

	trElements = new Array();
	tdElements = new Array();
	inputTextElements = new Array();
	tdWidth = [ "30%", "60%" ];
	tdAlign = [ "right", "left" ];
	songProp = [ "Song Title: ", "Author: ", "Tempo: ", "Key: " ];
	
	for (var i = 0; i < 4; i++) {
		trElements[i] = document.createElement("tr");
		for (var j = 0; j < 2; j++) {
			tdElements[i * 2 + j] = document.createElement("td");
			tdElements[i * 2 + j].width = tdWidth[j];
			tdElements[i * 2 + j].height = "30";
			tdElements[i * 2 + j].align = tdAlign[j];
			trElements[i].appendChild( tdElements[i * 2 + j] );
		}
		inputTextElements[i] = document.createElement("input");
		inputTextElements[i].type = "text";
		inputTextElements[i].style.width = "100%";
		inputTextElements[i].value = element[ elementList[i] ].innerHTML;

		tdElements[i * 2].innerHTML = "<b>" + songProp[i] + "</b>";
		tdElements[i * 2 + 1].appendChild( inputTextElements[i] );
		tableElement.appendChild( trElements[i] );
	}

	okPElement = document.createElement("p");
	okPElement.align = "center";
	okPElement.innerHTML = "<a href=\"javascript:submitSongInfo();\">Submit</a>&nbsp;&nbsp;&nbsp;<a href=\"javascript:cancelSongInfo();\">Cancel</a>";
	promptlayer.appendChild( okPElement );

	promptlayer.myHeight = "280px";
	setPromptLayer();
	wrapper.style.display = "";
}

function submitSongInfo() {
	var xhr = new XMLHttpRequest();
	var input;
	input = "band_id=" + bandId +
			"&title=" + encodeURIComponent( inputTextElements[0].value ) + 
			"&author=" + encodeURIComponent( inputTextElements[1].value ) + 
			"&tempo=" + encodeURIComponent( inputTextElements[2].value ) + 
			"&key=" + encodeURIComponent( inputTextElements[3].value );
	xhr.open( "POST", "updateInfo.php", false );
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.send(input);
	if (xhr.readyState == 4) {
		if ( xhr.status != 200 )
			console.log( "submitSongInfo(): Request failed with code " + new String( xhr.status ) );
		else {
			if ( xhr.responseText == "OK" ) {
				for ( var i = 0; i < 4; i++ ) {
					element[ elementList[i] ].innerHTML = inputTextElements[i].value;
				}
				cancelSongInfo();
			} else {
				okPElement.innerHTML = "<p><font color=red><b>[Error] " + xhr.responseText + "</b></font></p>" + "<a href=\"javascript:cancelSongInfo();\">Cancel</a>";
			}
		}
	}
}

function cancelSongInfo() {
	promptlayer.removeChild( tableElement );
	for ( var i = 0; i < 4; i++ ) {
		tdElements[i * 2 + 1].removeChild( inputTextElements[i] );
		for ( var j = 0; j < 2; j++ ) {
			trElements[i].removeChild( tdElements[i * 2 + j] );
			tdElements[i * 2 + j] = undefined;
		}
		tableElement.removeChild( trElements[i] );
		trElements[i] = undefined;
	}
	promptlayer.removeChild( okPElement );

	tableElement = undefined;
	okPElement = undefined;
	wrapper.style.display = "none";
}

function update( sync ) {
	var xhr = new XMLHttpRequest();
	xhr.open( "GET", "getInfo.php?band_id=" + bandId, !sync );
	xhr.send();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if ( xhr.status != 200 )
				console.log( "update(): Request failed with code " + new String( xhr.status ) );
			else {
				var ret = JSON.parse( xhr.responseText );
				for ( var i = 0; i < 4; i++ ) {
					element[ elementList[i] ].innerHTML = ret["info"][i];
				}
				playerlist = ret["player"];
				isAdmin = ret["player"][0] == username ? true : false;
				for ( var i = 0; i < 4; i++ ) {
					if ( i < ret["player"].length ) {
						if ( ret["player"][i] == username )
							element[ elementList[i + 4] ].innerHTML = "<b>" + ret["player"][i] + "</b>";
						else 
							element[ elementList[i + 4] ].innerHTML = ret["player"][i];
					} else {
						element[ elementList[i + 4] ].innerHTML = "<font color=red>N/A</font>";
					}
				}
				element[ elementList[8] ].innerHTML = playerlist.length.toString();
			}
		}
	}
}

