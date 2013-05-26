var connection;
var sessionID;
var localMediaStream;
var recorder;
var time;
var waitAck;
var playerlist;
var username;
var isAdmin;
var wrapper;
var bglayer;
var promptlayer;

function initAudio( currentSessionID, my_username, isadmin ) {
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
	};
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
			var timer = new Date();
			time = timer.getTime();
			connection.send( 'ack' );
			start_record();
			break;
		case "ack":
			if ( waitAck ) {
				var timer = new Date();
				console.log( "[ACK] Timeout: " + (5000 - (timer.getTime() - time)) );
				window.setTimeout( recorder.recordAudio, 5000 - (timer.getTime() - time) );
			}
			waitAck = false;
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
    var sessionid = location.hash.replace('#', '');
	connection.open( sessionid );
};

function joinSession() {
    var sessionid = location.hash.replace('#', '');
	connection.connect( sessionid );
};

function startRecorder( playerList ) {
	playerlist = playerList;
	if (isAdmin) {
		var timer = new Date();
		time = timer.getTime();
		connection.send( 'start' );
		waitAck = true;
	} else {
		var timer = new Date();
		console.log( "[START] Timeout: " + (5000 - (timer.getTime() - time)) );
		window.setTimeout( recorder.recordAudio, 5000 - (timer.getTime() - time) );
	}
}

function stopRecorder() {
	if (isAdmin)
		connection.send( 'end' );
	recorder.stopAudio( uploadWAV );
	wrapper.style.display = "";
}

function uploadWAV(url, blob) {
	var wavData = recorder.getBlob();
	
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

	var trElements = new Array();
	var tdElements = new Array();
	var saveElements = new Array();
	var tdWidth = [ "30%", "50%", "10%" ];
	var tdAlign = [ "right", "left", "center" ];
	var numPlayers = playerlist.length;
	for (var i = 0; i < numPlayers; i++) {
		trElements[i] = document.createElement("tr");
		for (var j = 0; j < 3; j++) {
			tdElements[i * 3 + j] = document.createElement("td");
			tdElements[i * 3 + j].width = tdWidth[j];
			tdElements[i * 3 + j].height = "30";
			tdElements[i * 3 + j].align = tdAlign[j];
			trElements[i].appendChild( tdElements[i * 3 + j] );
		}
		tdElements[i * 3].innerHTML = "<b>" + playerlist[i] + ": </b>";
		saveElements[i] = document.createElement("a");
		saveElements[i].target = "_blank";
		saveElements[i].innerHTML = "Save";
		if ( playerlist[i] == username ) {
			tdElements[i * 3 + 1].appendChild( progressElement );
			saveElements[i].href = url;
			tdElements[i * 3 + 2].appendChild( saveElements[i] );
		} else {
			saveElements[i].href = "tmp/" + sessionID + "_" + playerlist[i] + ".wav";
			tdElements[i * 3 + 1].innerHTML = "Uploading...";
		}
		tableElement.appendChild( trElements[i] );
	}

	var uploadRequest = new XMLHttpRequest();
	uploadRequest.open( "POST", "upload.php", true );
	uploadRequest.setRequestHeader( "BAND_ID", sessionID );
	uploadRequest.setRequestHeader( "USERNAME", username );
	uploadRequest.upload.addEventListener( "progress",
		function (e) {
			var percent = Math.round( e.loaded / e.total * 100 );
			progressElement.value = percent;
		},
		false );
	uploadRequest.onreadystatechange = function() {
		if (uploadRequest.readyState == 4) {
			if (uploadRequest.status != 200)
				alert( "Error code = " + new String( uploadRequest.status ) );
			else {
				progressElement.value = 100;
				console.log( uploadRequest.responseText );
			}
		}
	};
//	uploadRequest.send( wavData );
	uploadRequest.send( blob );

	var getFileStatusRequests = new Array();
	for (var i = 0; i < numPlayers; i++) {
		var request = new XMLHttpRequest();
		request.finished = false;
		request.i = i;
		request.url = "getFileStatus.php?filename=" + sessionID + "_" + playerlist[i];
		request.onreadystatechange = function() {
			var req = this;
			var i = req.i;
			if (req.readyState == 4) {
				if ( req.status != 200 )
					alert( "Error code = " + new String( req.status ) );
				else {
					if ( req.responseText == "0" ) {
						if ( playerlist[i] != username )
							tdElements[i * 3 + 1].innerHTML = "Uploading...";
					} else if ( req.responseText == "1" ) {
						req.finished = true;
						if ( playerlist[i] != username ) {
							tdElements[i * 3 + 1].innerHTML = "Uploaded.";
							tdElements[i * 3 + 2].appendChild( saveElements[i] );
						}
						checkAllFinished();
					}
				}
			}
		}
		request.intervalEvent = window.setInterval(
			function() {
				var req = request;
				return function( e ) {
					if ( !req.finished ) {
						req.open( "GET", req.url, true );
						req.send();
					}
					if ( req.finished )
						window.clearInterval( req.intervalEvent );
				}
			}(), 1000 );
		request.open( "GET", request.url, true );
		request.send();
		getFileStatusRequests.push( request );
	}

	var checkAllFinished = function() {
		for ( var i = 0; i < getFileStatusRequests.length; i++ ) {
			console.log( i + ": " + getFileStatusRequests[i].finished );
			if ( !getFileStatusRequests[i].finished )
				return;
		}

		var mixPElement = document.createElement("p");
		mixPElement.align = "center";
		mixPElement.innerHTML = "Mixing your song...";
		promptlayer.appendChild( mixPElement );
		var mixRequest = new XMLHttpRequest();
		mixRequest.onreadystatechange = function() {
			if ( mixRequest.readyState == 4 ) {
				if ( mixRequest.status != 200 ) {
					alert( "Error code = " + mixRequest.status );
				} else {
					if ( mixRequest.responseText != "0" ) {
						mixRequest.finished = true;
						mixPElement.innerHTML = "Mixing completed. <a href=\"" + mixRequest.responseText + "\" target=\"_blank\">Download Link</a>";
					}
					console.log( mixRequest.responseText );
				}
			}
		}
		if ( !isAdmin ) {
			mixRequest.finished = false;
			mixRequest.intervalEvent = window.setInterval(
				function() {
					var req = mixRequest;
					return function( e ) {
						if ( !req.finished ) {
							req.open( "GET", "mix.php", true );
							req.send();
						}
						if ( req.finished )
							window.clearInterval( req.intervalEvent );
					}
				}(), 1000 );
		}
		mixRequest.open( "GET", "mix.php", true );
		mixRequest.send();
	}
}
