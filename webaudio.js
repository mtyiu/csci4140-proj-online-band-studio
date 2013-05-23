var connection;
var sessionID;
var localMediaStream;
var recorder;
var username;

function initAudio( currentSessionID, my_username ) {
	console.log( "initAudio" );
	sessionID = currentSessionID;
	username = my_username;
	location.hash = "#" + sessionID;
	connection = new RTCMultiConnection();
	connection.session = 'only-audio';
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

function startRecorder() {
	recorder.recordAudio();
}

function stopRecorder() {
	recorder.stopAudio( uploadWAV );
}

function uploadWAV(url) {
	var wavData = recorder.getBlob();
	var request = new XMLHttpRequest();
	request.open( "POST", "upload.php", true );
	request.setRequestHeader( "BAND_ID", sessionID );
	request.setRequestHeader( "USERNAME", username );
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
