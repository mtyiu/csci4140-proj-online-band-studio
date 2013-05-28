var chatroomBox;
var chatroomBanner;
var chatroomPopupCheckbox;
var chatroomButton;
var chatroomMsgInput;
var chatroomContent;
var chatroomAutoPopup;
var chatroomUnreadMessage;

function initChatRoom() {
	chatroomBox = document.getElementById( "chatroomBox" );
	chatroomBanner = new Array();
	chatroomBanner.push( document.getElementById( "chatroomBoxTopLeft" ) );
	chatroomBanner.push( document.getElementById( "chatroomBoxTop" ) );
	chatroomBanner.push( document.getElementById( "chatroomBoxLeftBanner" ) );
	chatroomBanner.push( document.getElementById( "chatroomBoxMainBanner" ) );
	chatroomBanner.push( document.getElementById( "chatroomBoxPopupTd" ) );
	chatroomPopupCheckbox = document.getElementById( "popup" );
	chatroomButton = document.getElementById( "chatroomButton" );
	chatroomMsgInput = document.getElementById( "chatroomMsgInput" );
	chatroomContent = document.getElementById( "chatroomContent" );
	chatroomButton.addEventListener( "click", toggleChatRoom, false );
	chatroomButton.addEventListener( "mouseover", chatroomButtonMouseOverOut, false );
	chatroomButton.addEventListener( "mouseout", chatroomButtonMouseOverOut, false );
	chatroomBanner[3].addEventListener( "click", toggleChatRoom, false );
	chatroomBanner[3].addEventListener( "mouseover", chatroomBannerMouseOverOut, false );
	chatroomBanner[3].addEventListener( "mouseout", chatroomBannerMouseOverOut, false );
	chatroomBanner[4].addEventListener( "click", chatroomToggleAutoPopup, false );
	chatroomPopupCheckbox.addEventListener( "click", chatroomToggleAutoPopup, false );
	chatroomMsgInput.addEventListener( "keydown", chatroomSendMessage, false );
	chatroomAutoPopup = false;
	chatroomUnreadMessage = false;
}

function chatroomBannerMouseOverOut( e ) {
	var bgcolor;
	bgcolor = e.type == "mouseover" ? "#CF0000" : "#AE0000";
	for ( var i = 0; i < chatroomBanner.length; i++ )
		chatroomBanner[i].style.background = bgcolor;
	chatroomBanner[3].style.cursor = "pointer";
}

function chatroomButtonMouseOverOut( e ) {
	chatroomButton.className = (e.type == "mouseover") ? "over" : ((chatroomBox.style.display == "none") ? ( chatroomUnreadMessage ? "unread" : "" ) : "shown");
}

function toggleChatRoom() {
	chatroomUnreadMessage = false;
	if ( !loaded )
		return;
	chatroomBox.style.display = (chatroomBox.style.display == "none") ? "" : "none";
	chatroomButton.className = (chatroomBox.style.display == "none") ? "" : "shown";
}

function chatroomToggleAutoPopup( e ) {
	e.stopPropagation();
	if ( e.target !== chatroomPopupCheckbox )
		chatroomPopupCheckbox.checked = !chatroomPopupCheckbox.checked;
	chatroomAutoPopup = chatroomPopupCheckbox.checked;
}

function chatroomSendMessage( e ) {
	if ( e.keyCode == 13 ) { // Enter
		var message = chatroomMsgInput.value;
		if ( message == "" )
			return;
		var msgPElement = document.createElement( "p" );
		msgPElement.className = "message";
		msgPElement.align = "right";
		msgPElement.innerHTML = message;
		chatroomContent.appendChild( msgPElement );
		chatroomContent.scrollTop = chatroomContent.scrollHeight;
		chatroomMsgInput.value = "";
		message = "CHAT:<font class=\"chatUsername\">[" + username + "]</font> " + message;
		console.log( message );
		connection.send( message );
	}
}

function chatroomReceiveMessage( message ) {
	var msgPElement = document.createElement( "p" );
	msgPElement.className = "message";
	msgPElement.align = "left";
	msgPElement.innerHTML = message;
	chatroomContent.appendChild( msgPElement );
	chatroomContent.scrollTop = chatroomContent.scrollHeight;
	if ( chatroomAutoPopup ) {
		toggleChatRoom();
	} else {
		chatroomUnreadMessage = true;
		chatroomButton.className = "unread";
	}
}
