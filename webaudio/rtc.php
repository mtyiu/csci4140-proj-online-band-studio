<html>
<head>
	<title>Web Audio API testing</title>
	<script src="https://webrtc-experiment.appspot.com/RTCMultiConnection-v1.3.js"></script>
	<script type="text/javascript">
		var connection = new RTCMultiConnection();

		connection.session = {
		    audio: true,
		    video: false
		};

		connection.onstream = function (e) {
		    if (e.type === 'local') mainVideo.src = e.blobURL;
		    if (e.type === 'remote') document.body.appendChild(e.mediaElement);
						}

						// searching/connecting pre-created session
						connection.connect('session-id');

						// to create/open a new session
						// it should be called "only-once" by the session-initiator
						[button-init-session].onclick = function() {
						    connection.open('session-id');
							};
	</script>
</head>
<body>

</body>
</html>
