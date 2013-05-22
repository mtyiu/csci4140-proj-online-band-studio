var connection;
var sessionID;

function adminInitAudio( currentSessionID ) {
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
		}

		if (stream.type === 'local') {
			mediaElement = stream.mediaElement;
			document.getElementById('local-media-stream').appendChild(mediaElement);
			mediaElement.controls = true;
		}
	};
	sessionID = currentSessionID;
}

function openSession() {
	connection.open(sessionID);
};

function joinSession() {
	connection.connect(sessionID);
};
