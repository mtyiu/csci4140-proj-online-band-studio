<!DOCTYPE html>
<html lang="en">
    
    <head>
        <title>Jam Boardcasting</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <script>
            var hash = window.location.hash.replace('#', '');
            if (!hash.length) location.href = location.href + '#' + (Math.random() * 1000 << 10);
        </script>
        <style>
            html {
                background: #eee;
            }
            body {
                margin: 0;
                font-family:"Inconsolata", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", monospace;
                font-size: 1.2em;
                line-height: 1.2em;
            }
            article, footer {
                display: block;
                min-width: 900px;
                max-width: 900px;
                width: 80%;
            }
            article {
                margin: 2.5em auto 0 auto;
                border: 1px solid;
                border-color: #ddd #aaa #aaa #ddd;
                padding: 2em;
                background: #fff;
            }
            h1, h2 {
                font-weight: normal;
                display: inline;
                border-bottom: 1px solid black;
                padding: 0 0 3px 0;
                line-height: 36px;
            }
            :-moz-any-link:focus {
                color: #000;
                border: 0;
            }

            button, select {
                -moz-border-radius: 3px;
                -moz-transition: none;
                -webkit-transition: none;
                background: #0370ea;
                background: -moz-linear-gradient(top, #008dfd 0, #0370ea 100%);
                background: -webkit-linear-gradient(top, #008dfd 0, #0370ea 100%);
                border: 1px solid #076bd2;
                border-radius: 3px;
                color: #fff;
                display: inline-block;
                font-size: .8em;
                line-height: 1.3;
                padding: 5px 12px;
                text-align: center;
                text-shadow: 1px 1px 1px #076bd2;
                font-family: inherit;
            }
            button:hover {
                background: rgb(9, 147, 240);
            }
            button:active {
                background: rgb(10, 118, 190);
            }
            button[disabled] {
                background: none;
                border: 1px solid rgb(187, 181, 181);
                color: gray;
                text-shadow: none;
            }
            #remote-media-streams video {
                width: 10em;
            }
            #local-media-stream video {
                width: 34em;
            }

         
        
    
        </style>
        <!-- for HTML5 el styling -->
        <script>
            document.createElement('article');
            document.createElement('footer');
        </script>
        <script src="firebase.js"></script>
        <script src="RTCMultiConnection-v1.2.js"></script>
    </head>
    
    <body>
        <article> 


           
             <section>
                 <h2>Join a session:</h2>

                <button id="join-session">Join</button>
                 <h2>or open new session:</h2>

                <button id="open-session">Open Session</button>
            </section>
            <table style="width: 100%; border-left: 1px solid black;">
                <tr>
                    <td>
                         <h2 style="display: block; text-align: left;">Local Media Stream</h2>

                        <section id="local-media-stream"></section>
                    </td>
                </tr>
                <tr>
                    <td>
                         <h2 style="display: block; text-align: left;">Remote Media Streams</h2>

                        <section id="remote-media-streams"></section>
                    </td>
                </tr>
            </table>
            <script>
                var connection = new RTCMultiConnection();
                connection.session = 'only-audio';
                connection.onstream = function (stream) {
                    if (stream.type === 'remote') {
                        var mediaElement = stream.mediaElement;
						
                        if (stream.direction !== RTCDirection.OneWay) {
                            var remoteMediaStreams = document.getElementById('remote-media-streams');
							//remoteMediaStreams.innerHTML ="Buddy";
                            remoteMediaStreams.insertBefore(mediaElement, remoteMediaStreams.firstChild);
                        } else //document.getElementById('local-media-stream').innerHTML="Active";
						document.getElementById('local-media-stream').appendChild(mediaElement);
                        mediaElement.controls = false;
                    }

                    if (stream.type === 'local') {
                        mediaElement = stream.mediaElement;
						//document.getElementById('local-media-stream').innerHTML="Me";
                        document.getElementById('local-media-stream').appendChild(mediaElement);
                        mediaElement.controls = false;
                    }
                };

                var sessionid = location.hash.replace('#', '');

                document.getElementById('open-session').onclick = function () {
                    connection.open(sessionid);
                    disableButtons();

                    var hash = location.hash.replace('#', '');
                    //this.parentNode.innerHTML = '<h2><a href="' + location.href + '" target="_blank">Share this link</a></h2>';
                };

                document.getElementById('join-session').onclick = function () {
                    connection.connect(sessionid);
                    disableButtons();
                };

                function disableButtons() {
                    if (document.getElementById('join-session')) document.getElementById('join-session').disabled = true;
                    if (document.getElementById('open-session')) document.getElementById('open-session').disabled = true;
                }
            </script>
            <br/>
    </body>

</html>
