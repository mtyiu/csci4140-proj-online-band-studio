<html>
<head>
	<title>Web Audio API testing</title>
	<script type="text/javascript">
		function callback(stream) {
			var context = new window.webkitAudioContext();
			var mediaStreamSource = context.createMediaStreamSource( stream );

			mediaStreamSource.connect( context.destination );
			
			var textNode = document.createTextNode( stream );
			var bodyElement = document.getElementsByTagName("body")[0];
			bodyElement.appendChild( textNode );
		}

		window.addEventListener( "load", function() {
				navigator.webkitGetUserMedia( {audio:true}, callback,
					function(e) {
						alert('Error getting audio');
						console.log(e);
					}
				);
			},
			false );
	</script>
</head>
<body>

</body>
</html>
