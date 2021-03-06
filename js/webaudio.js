var connection;
var sessionID;
var localMediaStream;
var recorder;
var waitAck;
var playerlist;

// For showing audio streams
var audioStreamTable;
var audioStreamTr;
var audioStreamTd;
var audioStreamOK;
var showAudioStreams;

// For showing mixing results
var progressElement;
var saveElements;
var mixPElement;
var numPlayers;

function initAudio( currentSessionID ) {
	sessionID = currentSessionID;
	location.hash = "#" + sessionID;
	hiSound = undefined;
	lowSound = undefined;
	showAudioStream = false;
	audioStreamTable = document.createElement( "table" );
	audioStreamTable.width = "90%";
	audioStreamTable.border = "0";
	audioStreamTable.align = "center";
	audioStreamTable.style.display = "none";
	audioStreamTr = new Array();
	audioStreamTd = new Array();
	var audioTdWidth = [ "30%", "60%" ];
	var audioAlign = [ "right", "center" ];
	for (var i = 0; i < 4; i++) {
		audioStreamTr[i] = document.createElement( "tr" );
		if ( i != 3 ) {
			for (var j = 0; j < 2; j++) {
				audioStreamTd[i * 2 + j] = document.createElement( "td" );
				audioStreamTd[i * 2 + j].width = audioTdWidth[j];
				audioStreamTd[i * 2 + j].align = audioAlign[j];
				audioStreamTr[i].appendChild( audioStreamTd[i * 2 + j] );
			}
		} else {
			audioStreamTd[i * 2] = document.createElement( "td" );
			audioStreamTd[i * 2].colSpan = "2";
			audioStreamTd[i * 2].align = "center";
			audioStreamTr[i].appendChild( audioStreamTd[i * 2] );
		}
		audioStreamTable.appendChild( audioStreamTr[i] );
	}
	audioStreamTd[0].innerHTML = "<b>Local Audio Stream: </b>";
	audioStreamTd[1].innerHTML = "<i>Loading...</i>";
	audioStreamTd[2].innerHTML = "<b>Remote Audio Streams: </b>";
	audioStreamTd[3].innerHTML = "<i>Loading...</i>";
	audioStreamTd[4].innerHTML = "&nbsp;";
	audioStreamTd[5].innerHTML = "&nbsp;";
	audioStreamOK = document.createElement("span");
	audioStreamOK.className = "jslink";
	audioStreamOK.onclick = toggleAudioStreams;
	audioStreamOK.innerHTML = "OK";
	audioStreamTd[6].appendChild( audioStreamOK );
	
	promptlayer.appendChild( audioStreamTable );

	connection = new RTCMultiConnection();
	connection.autoCloseEntireSession = true;
	connection.session = 'audio and data';
	connection.onmessage = messageMux;
	connection.onclose = leaveBandRoom;
	connection.onstream = function (stream) {
		if (stream.type === 'remote') {
			var mediaElement = stream.mediaElement;

			if (stream.direction !== RTCDirection.OneWay) {
				if ( audioStreamTd[3].innerHTML == "<i>Loading...</i>" )
					audioStreamTd[3].innerHTML = "";
				audioStreamTd[3].appendChild( mediaElement );
			} else {
				if ( audioStreamTd[1].innerHTML == "<i>Loading...</i>" )
					audioStreamTd[1].innerHTML = "";
				audioStreamTd[1].appendChild( mediaElement );
			}
			
			mediaElement.controls = true;
		} 
		
		if (stream.type === 'local') {
			mediaElement = stream.mediaElement;
			if ( audioStreamTd[1].innerHTML == "<i>Loading...</i>" )
				audioStreamTd[1].innerHTML = "";
			audioStreamTd[1].appendChild( mediaElement );
			mediaElement.controls = true;
			recorder = RecordRTC({
			    stream: stream.stream
			});
		}
	};

	if ( isAdmin )
		document.getElementById( "recordText" ).onclick = startRecorder;
}

function toggleAudioStreams() {
	if ( !loaded )
		return;
	if ( showAudioStreams ) {
		// Turn Off
		audioStreamTable.style.display = "none";
		wrapper.style.display = "none";
	} else {
		// Turn on
		h2Element.innerHTML = "Audio Streams";
		audioStreamTable.style.display = "";
		promptlayer.myHeight = undefined;
		setPromptLayer();
		wrapper.style.display = "";
	}
	showAudioStreams = !showAudioStreams;
}

function messageMux( message ) {
	console.log( "Message received: " + message );
	switch( message ) {
		case "start":
			connection.send( 'ack' );
			if ( isAdmin ) rmAllPreviousFiles();
			startRecorder();
			break;
		case "end":
			connection.send( 'ack' );
			stopRecorder();
			break;
		case "ack":
			if ( waitAck ) {
				recordTimeoutHandler();
			}
			waitAck = false;
			break;
		case "exit":
			if ( !isAdmin )
				leaveBandRoom();
			break;
		default:
			if ( message.substr( 0, 4 ) == "CHAT" ) {
				var messageUsername = message.match(/^CHAT:<font class="chatUsername">\[(.*)\]<\/font>.+$/);
				if ( messageUsername && messageUsername != username )
					chatroomReceiveMessage( message.substr( 5, message.length - 5 ) );
			}
	}
}

function rmAllPreviousFiles() {
	if ( !isAdmin ) return;
	var xhr = new XMLHttpRequest();
	xhr.open( "GET", "rmAll.php", true );
	xhr.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
	xhr.send( null );
	xhr.onreadystatechange = function() {
		if ( xhr.readyState == 4 ) {
			if ( xhr.status != 200 ) {
				alert( "Error code = " + mixRequest.status );
			} else {
				alert( "Hihi" );
			}
		}
	};
}

function openSession() {
    var sessionid = location.hash.replace('#', '');
	connection.open( sessionid );
};

function joinSession() {
    var sessionid = location.hash.replace('#', '');
	connection.connect( sessionid );
};

function startRecorder() {
	recordButtonElement.className = "stopButton";
	recordTextElement.innerHTML = "Stop";
	recordTextElement.className = "stopRecordText";
	if (isAdmin) {
		recordTextElement.onclick = stopRecorder;
		connection.send( 'start' );
		if ( playerlist.length > 1 )
			waitAck = true;
		else
			recordTimeoutHandler();
	} else {
		recordTextElement.onclick = function (e) {};
		recordTimeoutHandler();
	}
}

function recordTimeoutHandler() {
	var currentTimeLeft;
	var timer = new Date();
	var startTime = timer.getTime();
	var startAlertElement;
	if ( wrapper.style.display == "none" ) {
		wrapper.style.display = "";
		promptlayer.myHeight = "70px";
		setPromptLayer();
		startAlertElement = h2Element;
	} else {
		startAlertElement = document.createElement( "h2" );
		promptlayer.insertBefore( startAlertElement, h2Element );
	}
	for ( var timeLeft = 0, timeBound = startTime + 5000; timeLeft + startTime < timeBound; timeLeft += 1000 ) {
		window.setTimeout( function() {
			var timeLeftInSeconds = (5000 - timeLeft) / 1000;
			return function (e) {
				startAlertElement.innerHTML = "Starting recording in " + timeLeftInSeconds + " seconds...";
			}
		}(), timeLeft );
	}
	currentTimeLeft = 5000 - (timer.getTime() - startTime);
	window.setTimeout( function() {
		recorder.recordAudio();
		startAutoFlip();
		promptlayer.myHeight = undefined;
		if ( startAlertElement === h2Element ) {
			setPromptLayer();
			wrapper.style.display = "none";
		} else {
			promptlayer.removeChild( startAlertElement );
		}
	}, currentTimeLeft );
}

function stopRecorder() {
	recordButtonElement.className = "startButton";
	recordTextElement.innerHTML = "Record";
	recordTextElement.className = "startRecordText";
	if (isAdmin) {
		recordTextElement.onclick = startRecorder;
		connection.send( 'end' );
	}
	recorder.stopAudio( uploadWAV );
	
	/* DOM Manipulation */
	h2Element.innerHTML = "Uploading to the server for mixing...";
	
	progressElement = document.createElement( "progress" );
	progressElement.max = 100;
	progressElement.value = 0;
	progressElement.style.width = "100%";

	tableElement = document.createElement( "table" );
	tableElement.width = "90%";
	tableElement.border = "0";
	tableElement.align = "center";
	promptlayer.appendChild( tableElement );

	trElements = new Array();
	tdElements = new Array();
	saveElements = new Array();
	tdWidth = [ "30%", "50%", "10%" ];
	tdAlign = [ "right", "left", "center" ];
	numPlayers = playerlist.length;
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
			tdElements[i * 3 + 2].appendChild( saveElements[i] );
		} else {
			saveElements[i].href = "tmp/" + sessionID + "_" + playerlist[i] + ".wav";
			tdElements[i * 3 + 1].innerHTML = "Uploading...";
		}
		tableElement.appendChild( trElements[i] );
	}
	okPElement = document.createElement("p");
	okPElement.align = "center";
	okPElement.innerHTML = "<span onclick=\"disablePrompt();\" class=\"jslink\">OK</span>";
	promptlayer.appendChild( okPElement );
	wrapper.style.display = "";
}

function uploadWAV(url, blob) {
	for (var i = 0; i < numPlayers; i++) {
		if ( playerlist[i] == username ) {
			saveElements[i].href = url;
			break;
		}
	}
	var uploadRequest = new XMLHttpRequest();
	uploadRequest.open( "POST", "upload.php", false );
	uploadRequest.setRequestHeader( "BAND_ID", sessionID );
	uploadRequest.setRequestHeader( "USERNAME", username );
	uploadRequest.upload.onprogress = function (e) {
			var percent = Math.round( e.loaded / e.total * 100 );
			progressElement.value = percent;
		};
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
						req.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
						req.send();
					}
					if ( req.finished )
						window.clearInterval( req.intervalEvent );
				}
			}(), 1000 );
		request.open( "GET", request.url, true );
		request.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
		request.send();
		getFileStatusRequests.push( request );
	}

	var checkAllFinished = function() {
		for ( var i = 0; i < getFileStatusRequests.length; i++ ) {
			if ( !getFileStatusRequests[i].finished )
				return;
		}

		mixPElement = document.createElement("p");
		mixPElement.align = "center";
		mixPElement.innerHTML = "Mixing your song...";
		promptlayer.insertBefore( mixPElement, okPElement );
		
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
		};
		if ( !isAdmin ) {
			mixRequest.finished = false;
			mixRequest.intervalEvent = window.setInterval(
				function() {
					var req = mixRequest;
					return function( e ) {
						if ( !req.finished ) {
							req.open( "GET", "mix.php", true );
							req.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
							req.send();
						}
						if ( req.finished )
							window.clearInterval( req.intervalEvent );
					}
				}(), 1000 );
		}
		mixRequest.open( "GET", "mix.php", true );
		mixRequest.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
		mixRequest.send();
	}
}

function disablePrompt() {
	promptlayer.removeChild( tableElement );
	if ( mixPElement != undefined )
		promptlayer.removeChild( mixPElement );
	promptlayer.removeChild( okPElement );
	for ( var i = 0; i < numPlayers; i++ ) {
		if ( playerlist[i] == username )
			tdElements[i * 3 + 1].removeChild( progressElement );
		tdElements[i * 3 + 2].removeChild( saveElements[i] );
		for ( var j = 0; j < 3; j++ ) {
			trElements[i].removeChild( tdElements[i * 3 + j] );
		}
		tableElement.removeChild( trElements[i] );
	}
	wrapper.style.display = "none";
	progressElement = undefined;
	tableElement = undefined;
	trElements = undefined;
	tdElements = undefined;
	saveElements = undefined;
	mixPElement = undefined;
	okPElement = undefined;
	tdWidth = undefined;
	tdAlign = undefined;
	numPlayers = undefined;
}
