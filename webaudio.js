var connection;
var sessionID;
var localMediaStream;
var recorder;
var playerlist;
var username;
var isAdmin;
var wrapper;
var bglayer;
var promptlayer;

function initAudio( currentSessionID, my_username, isadmin ) {
	console.log( "initAudio" );
	sessionID = currentSessionID;
	username = my_username;
	isAdmin = isadmin;
	location.hash = "#" + sessionID;
	connection = new RTCMultiConnection();
	connection.autoCloseEntireSession = true;
	connection.session = 'audio and data';
	connection.onmessage = messageMux;
	connection.onleave = function( userid, extra ) {
		console.log( userid );
		if (!isadmin) signInOut();
	}
	connection.onstream = function (stream) {
		if (stream.type === 'remote') {
			var mediaElement = stream.mediaElement;

			if (stream.direction !== RTCDirection.OneWay) {
				var remoteMediaStreams = document.getElementById('remote-media-streams');
				remoteMediaStreams.insertBefore(mediaElement, remoteMediaStreams.firstChild);
			} else
				document.getElementById('local-media-stream').appendChild(mediaElement);
			
			mediaElement.controls = true;

			var loadingRemoteElement = document.getElementById('loading_remote');
			loadingRemoteElement.style.display = "none";

		} 
		
		if (stream.type === 'local') {
			mediaElement = stream.mediaElement;
			document.getElementById('local-media-stream').appendChild(mediaElement);
			mediaElement.controls = true;

			recorder = RecordRTC({
			    stream: stream.stream
			});

			var loadingLocalElement = document.getElementById('loading_local');
			loadingLocalElement.style.display = "none";
		}
	};

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

	wrapper.style.display = "none";
	setPromptLayer();
	window.addEventListener( "resize", setPromptLayer, false );
	window.addEventListener( "scroll", setPromptLayer, false );
}

function messageMux( message ) {
	console.log( message );
	switch( message ) {
		case "start":
		case "end":
			start_record();
			break;
	}
}

function setPromptLayer( e ) {
	var width = document.body.clientWidth ? document.body.clientWidth : window.innerWidth;
	var height = document.body.clientHeight ? document.body.clientHeight : window.innerHeight;
	bglayer.style.width = width;
	bglayer.style.height = height;
	bglayer.style.left = window.pageXOffset;
	bglayer.style.top = window.pageYOffset;

	promptlayer.style.width = width * 0.5;
	promptlayer.style.height = height * 0.5;
	promptlayer.style.left = window.pageXOffset + Math.round( (width - width * 0.5) / 2 );
	promptlayer.style.top = window.pageYOffset + Math.round( (height - height * 0.5) / 2 );
}

function openSession() {
	console.log( "openSession" );
    var sessionid = location.hash.replace('#', '');
	connection.open(sessionid);
};

function joinSession() {
	console.log( "joinSession" );
    var sessionid = location.hash.replace('#', '');
	connection.connect(sessionid);
};

function startRecorder( playerList ) {
	playerlist = playerList;
	if (isAdmin)
		connection.send( 'start' );
	recorder.recordAudio();
}

function stopRecorder() {
	if (isAdmin)
		connection.send( 'end' );
	recorder.stopAudio( uploadWAV );
	wrapper.style.display = "";
}

function uploadWAV(url) {
	var wavData = recorder.getBlob();
	var request = new XMLHttpRequest();
	request.open( "POST", "upload.php", true );
	request.setRequestHeader( "BAND_ID", sessionID );
	request.setRequestHeader( "USERNAME", username );

	/* DOM Manipulation */
	var h1Element = document.createElement("h2");
	h1Element.innerHTML = "Uploading to the server for mixing...";
	promptlayer.appendChild( h1Element );
	
	var progressElement = document.createElement( "progress" );
	progressElement.max = 100;
	progressElement.value = 0;
	progressElement.style.width = "100%";

	var tableElement = document.createElement( "table" );
	tableElement.width = "90%";
	tableElement.border = "0";
	tableElement.align = "center";
	promptlayer.appendChild( tableElement );

	var trElements = [];
	var tdElements = [];
	var tdWidth = [ "35%", "55%" ];
	var tdAlign = [ "right", "left" ];
	var numPlayers = playerlist.length;
	for (var i = 0; i < numPlayers + 1; i++) {
		trElements[i] = document.createElement("tr");
		for (var j = 0; j < 2; j++) {
			tdElements[i * 2 + j] = document.createElement("td");
			tdElements[i * 2 + j].width = tdWidth[j];
			tdElements[i * 2 + j].height = "30";
			tdElements[i * 2 + j].align = tdAlign[j];
			trElements[i].appendChild( tdElements[i * 2 + j] );
		}
		if ( i < numPlayers ) {
			tdElements[i * 2].innerHTML = "<b>" + playerlist[i] + ": </b>";
			if ( playerlist[i] == username )
				tdElements[i * 2 + 1].appendChild( progressElement );
			else
				tdElements[i * 2 + 1].innerHTML = "Uploading...";
		}
		tableElement.appendChild( trElements[i] );
	}

	request.upload.addEventListener( "progress",
		function (e) {
			var percent = Math.round( e.loaded / e.total * 100 );
			console.log( percent );
			progressElement.value = percent;
		},
		false );
	request.send( wavData );

	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status != 200)
				alert( "Error code = " + new String( request.status ) );
			else {
				console.log( request.responseText );
				console.log( "WAV uploaded." );
			}
		}
	};

	createDownloadLink(url);
}
function createDownloadLink(url) {
	var li = document.createElement('li');
	var au = document.createElement('audio');
	var hf = document.createElement('a');
	
	au.controls = true;
	au.src = url;
	hf.href = url;
	hf.download = url;
	hf.innerHTML = hf.download;
	li.appendChild(au);
	li.appendChild(hf);
	var dllinkElement = document.getElementById("dllink");
	dllinkElement.appendChild(li);
}
