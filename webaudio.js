var connection;
var sessionID;
var localMediaStream;

function initAudio( currentSessionID ) {
	console.log( "initAudio" );
	sessionID = currentSessionID;
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
			localMediaStream = stream;
			mediaElement = stream.mediaElement;
			document.getElementById('local-media-stream').appendChild(mediaElement);
			mediaElement.controls = true;
			
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
