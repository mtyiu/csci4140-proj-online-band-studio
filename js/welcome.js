var numRooms;
var joinButtonElement;
var noRoomPElement;
var bandroomTable;
var element;
var elementList = [ "bandroomName", "bandroomDescription", "bandroomAdmin", "bandroomNumPlayers", "bandroomPlayerList" ];
var bandroomSelectButtons;
var selectedBandroom = 1;
var bandroomObj;

function logout() {
	window.location.assign( "logout.php" );
}

function create() {
	window.location.assign( "create.php" );
}

function joinButtonHandler( e ) {
	switch ( e.type ) {
		case "mouseover":
			e.target.src = "images/join2.png";
			break;
		case "mouseout":
			e.target.src = "images/join.png";
			break;
		case "click":
			join();
			break;
	}
}

function join() {
	if ( bandroomObj[ selectedBandroom - 1][3] == 4 ) {
		alert( "Sorry. This room is full :-(" );
		return;
	}
	var xhr = new XMLHttpRequest();
	xhr.open( "POST", "redirect.php", false );
	xhr.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
	var input = "f_name=" + selectedBandroom;
	xhr.send( input );
	if ( xhr.readyState == 4 ) {
		if ( xhr.status != 200 )
			alert( "Error code = " + new String( xhr.status ) );
		else {
			window.location.assign( "bandroom.php" );
		}
	}
}

function selectButtonHandler( e ) {
	switch ( e.type ) {
		case "mouseover":
			e.target.className = "mainon";
			break;
		case "mouseout":
			e.target.className = ( e.target.i == selectedBandroom ) ? "mainon" : "mainoff";
			break;
		case "click":
			var i = e.target.i;
			var results = bandroomObj[i - 1];
			joinButton.i = i;
			bandroomSelectButtons[ selectedBandroom - 1 ].className = "mainoff";
			e.target.className = "mainon";
			selectedBandroom = i;
			for ( var j = 0; j < 4; j++ ) {
				element[j].innerHTML = (results[j] == "") ? "<font color=\"#888888\">N/A</font>" : results[j];
			}
			element[4].innerHTML = results[2];
			for ( var j = 4; j <= 6; j++ ) {
				if ( results[j] != "" )
					element[4].innerHTML += ", " + results[j];
			}
			break;
	}
}

function updateBandRoomList( sync ) {
	var xhr = new XMLHttpRequest();
	var url = "getBandRoomList.php";
	xhr.open( "GET", url, !sync );
	xhr.setRequestHeader( "If-Modified-Since", (new Date(0)).toGMTString() );
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if ( xhr.status != 200 )
				console.log( "updateBandRoomList(): Request failed with code " + new String( xhr.status ) );
			else {
				var ret = JSON.parse( xhr.responseText );
				var tmp = 1;
				bandroomObj = ret;
				numRooms = 0;
				for ( var i = 0; i < 4; i++ ) {
					if ( ret[i].length != 0 )
						tmp = i + 1;
				}
				selectedBandroom = ( ret[selectedBandroom - 1].length == 0 ) ? tmp : selectedBandroom;
				for ( var i = 0; i < 4; i++ ) {
					var results = ret[i];
					if ( results.length == 0 ) {
						bandroomSelectButtons[ i ].style.display = "none";
						bandroomSelectButtons[ i ].innerHTML = " ";
					} else {
						bandroomSelectButtons[ i ].style.display = "";
						bandroomSelectButtons[ i ].innerHTML = results[0];
						if ( selectedBandroom == i + 1 ) {
							bandroomSelectButtons[ i ].className = "mainon";
							for ( var j = 0; j < 4; j++ ) {
								element[j].innerHTML = (results[j] == "") ? "<font color=\"#888888\">N/A</font>" : results[j];
							}
							element[4].innerHTML = results[2];
							for ( var j = 4; j <= 6; j++ ) {
								if ( results[j] != "" )
									element[4].innerHTML += ", " + results[j];
							}
						} else {
							bandroomSelectButtons[ i ].className = "mainoff";
						}
						numRooms++;
					}
				}
				if ( numRooms == 0 ) {
					noRoomPElement.style.display = "";
					bandroomTable.style.display = "none";
				} else {
					noRoomPElement.style.display = "none";
					bandroomTable.style.display = "";
				}
			}
		}
	};
	xhr.send(null);
}

function initList() {
	joinButtonElement = document.getElementById( "joinButton" );
	noRoomPElement = document.getElementById( "noRoom" );
	bandroomTable = document.getElementById( "bandroomTable" );
	element = new Array();
	bandroomSelectButtons = new Array();
	for ( var i = 0; i < elementList.length; i++ )
		element[i] = document.getElementById( elementList[i] );
	for ( var i = 0; i < 4; i++ ) {
		bandroomSelectButtons[i] = document.getElementById( "bandSelect" + (i + 1) );
		bandroomSelectButtons[i].i = i + 1;
		bandroomSelectButtons[i].addEventListener( "mouseover", selectButtonHandler, false );
		bandroomSelectButtons[i].addEventListener( "mouseout", selectButtonHandler, false );
		bandroomSelectButtons[i].addEventListener( "click", selectButtonHandler, false );
	}
	joinButtonElement.addEventListener( "mouseover", joinButtonHandler, false );
	joinButtonElement.addEventListener( "mouseout", joinButtonHandler, false );
	joinButtonElement.addEventListener( "click", joinButtonHandler, false );
	updateBandRoomList( true );
}

window.addEventListener( "load", function( e ) {
	window.setInterval( function() { updateBandRoomList( false ); }, 1000 );
}, false );
