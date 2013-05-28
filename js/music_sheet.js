var musicSheetWrapperDiv;
var musicSheetDiv;
var musicSheetOopsDiv;
var musicSheetDisplayDiv;
var musicSheetImagesDiv;
var musicSheetImgElements;
var musicSheetUploadFormDiv;
var musicSheetDisplayTd;
var currentPage;
var autoFlipItv;
var autoFlipInputElement;
var fileList;
var fileChanged;
var autoflip;

function initMusicSheet() {
	musicSheetWrapperDiv = document.getElementById( "music_sheet_wrapper" );
	musicSheetDiv = document.getElementById( "music_sheet" );
	musicSheetOopsDiv = document.getElementById( "oops_music_sheet" );
	fileList = undefined;
	musicSheetImagesDiv = undefined;
	currentPage = 0;
	fileChanged = false;
	autoFlipItv = undefined;
	autoflip = 0;
	window.addEventListener( "resize", setMusicSheetLayer, false );
	window.addEventListener( "scroll", setMusicSheetLayer, false );
	setMusicSheetLayer();

	/* Construct upload form */
	musicSheetUploadFormDiv = document.createElement( "div" );
	var uploadTitleElement = document.createElement( "h1" );
	uploadTitleElement.innerHTML = "Upload Music Sheet";
	musicSheetUploadFormDiv.appendChild( uploadTitleElement );
	var formPElement = document.createElement( "p" );
	musicSheetUploadFormDiv.appendChild( formPElement );
	var labelElement = document.createElement( "label" );
	labelElement.htmlFor = "fileselect";
	labelElement.innerHTML = "Music Sheet to upload (*.GIF, *.JPG, *.PNG are supported): ";
	formPElement.appendChild( labelElement );

	var fileInputElement = document.createElement( "input" );
	fileInputElement.type = "file";
	fileInputElement.accept = "image/gif, image/jpeg, image/png";
	fileInputElement.id = "fileselect";
	fileInputElement.multiple = true;
	formPElement.appendChild( fileInputElement );

	var fileDragDropElement = document.createElement( "div" );
	fileDragDropElement.id = "fileDragDrop";
	fileDragDropElement.innerHTML = "Or drop here";
	musicSheetUploadFormDiv.appendChild( fileDragDropElement );

	var notePElement = document.createElement( "p" );
	notePElement.innerHTML = "<i>Note: Your music sheet will be sorted by name.</i>";
	musicSheetUploadFormDiv.appendChild( notePElement );
	
	var autoFlipPElement = document.createElement( "p" );
	autoFlipPElement.innerHTML = "Auto flip interval (0 means disabled): ";
	musicSheetUploadFormDiv.appendChild( autoFlipPElement );
	autoFlipInputElement = document.createElement( "input" );
	autoFlipInputElement.type = "text";
	autoFlipInputElement.value = autoflip.toString();
	autoFlipInputElement.unit = "ms";
	autoFlipPElement.appendChild( autoFlipInputElement );
	autoFlipPElement.appendChild( document.createTextNode( "  " ) );
	var autoFlipSpanUnitElement = document.createElement( "span" );
	autoFlipSpanUnitElement.innerHTML = "ms";
	autoFlipSpanUnitElement.className = "musicSheetLink";
	autoFlipSpanUnitElement.onclick = function ( e ) {
		if ( e.target.innerHTML == "ms" ) {
			e.target.innerHTML = "bar";
			autoFlipInputElement.unit = "bar";
		} else {
			e.target.innerHTML = "ms";
			autoFlipInputElement.unit = "ms";
		}
	}
	autoFlipPElement.appendChild( autoFlipSpanUnitElement );
	autoFlipPElement.appendChild( document.createTextNode( "\u00A0\u00A0\u00A0" ) );
	var autoFlipSetElement = document.createElement( "span" );
	autoFlipSetElement.onclick = setAutoFlipInterval;
	autoFlipSetElement.innerHTML = "Set";
	autoFlipSetElement.className = "musicSheetLink";
	autoFlipPElement.appendChild( autoFlipSetElement );
	var uploadResultPElement = document.createElement( "p" );
	uploadResultPElement.style.color = "#888";
	uploadResultPElement.innerHTML = "Number of music sheets uploaded: <b>0</b>.";
	musicSheetUploadFormDiv.appendChild( uploadResultPElement );
	
	var fileDragHandler = function ( e ) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	};
	var fileSelectHandler = function ( e ) {
		fileChanged = true;
		fileDragHandler( e );
		var files = e.target.files || e.dataTransfer.files;
		fileList = new Array();
		var numFiles = 0;
		for ( var i = 0; i < files.length; i++ )
			if ( files[i].type == "image/gif" || files[i].type == "image/jpeg" || files[i].type == "image/png" ) {
				fileList.push( files[i] );
				numFiles++;
			}
		uploadResultPElement.style.color = "#F00";
		if ( e.target.files )
			uploadResultPElement.innerHTML = "Number of music sheets uploaded to input field: <b>" + numFiles + "</b>.";
		else 
			uploadResultPElement.innerHTML = "Number of music sheets uploaded to dropbox: <b>" + numFiles + "</b>.";
	}
	fileDragDropElement.addEventListener( "dragover", fileDragHandler, false );
	fileDragDropElement.addEventListener( "dragleave", fileDragHandler, false );
	fileDragDropElement.addEventListener( "drop", fileSelectHandler, false );
	fileInputElement.addEventListener( "change", fileSelectHandler, false );
	
	var musicSheetOKPElement = document.createElement( "p" );
	musicSheetOKPElement.innerHTML = "<span onclick=\"confirmMusicSheetUpload(); setAutoFlipInterval();\" class=\"musicSheetLink\">OK</span>&nbsp;&nbsp;<span onclick=\"cancelMusicSheetUpload();\" class=\"musicSheetLink\">Cancel</span>";
	musicSheetUploadFormDiv.appendChild( musicSheetOKPElement );

	/* Construct music sheet display layer */
	musicSheetDisplayDiv = document.createElement( "div" );
	musicSheetDisplayDiv.displaying = false;
	var musicSheetDisplayTable = document.createElement( "table" );
	musicSheetDisplayTable.width = "100%";
	musicSheetDisplayDiv.appendChild( musicSheetDisplayTable );
	var musicSheetDisplayTr = document.createElement( "tr" );
	musicSheetDisplayTable.appendChild( musicSheetDisplayTr );
	musicSheetDisplayTd = new Array();
	for ( var i = 0; i < 3; i++ ) {
		musicSheetDisplayTd[i] = document.createElement( "td" );
		if ( i != 1 ) {
			musicSheetDisplayTd[i].innerHTML = i == 0 ? "" : ("&gt;&gt;<br><br>" + i);
			musicSheetDisplayTd[i].width = "30";
			musicSheetDisplayTd[i].className = "nextPrevButton";
			musicSheetDisplayTd[i].addEventListener( "mouseover", nextPrevButtonHandler, false );
			musicSheetDisplayTd[i].addEventListener( "mouseout", nextPrevButtonHandler, false );
			musicSheetDisplayTd[i].addEventListener( "click", nextPrevButtonHandler, false );
		} else {
			musicSheetDisplayTd[i].addEventListener( "click", nextPrevButtonHandler, false );
		}
		musicSheetDisplayTd[i].align = "center";
		musicSheetDisplayTr.appendChild( musicSheetDisplayTd[i] );
	}
}

function nextPrevButtonHandler( e ) {
	e.preventDefault();
	if ( e.target.innerHTML == "" )
		return;
	switch (e.type) {
		case "mouseover":
			e.target.className = "nextPrevButtonHover";
			break;
		case "mouseout":
			e.target.className = "nextPrevButton";
			break;
		case "click":
			if ( e.target.innerHTML.substr( 0, 4 ) == "&gt;" ) {
				nextPage();
			} else if ( e.target.innerHTML.substr( 0, 4 ) == "&lt;" ) {
				prevPage();
			} else {
				nextPage();
			}
	}
}

function startAutoFlip() {
	if ( autoflip <= 0 )
		return;
	autoFlipItv = window.setInterval( nextPage, autoflip );
}

function nextPage() {
	if ( currentPage == fileList.length )
		return;
	musicSheetDisplayTd[1].removeChild( musicSheetImagesDiv[currentPage - 1] );
	currentPage++;
	musicSheetDisplayTd[1].appendChild( musicSheetImagesDiv[currentPage - 1] );
	musicSheetDisplayTd[0].innerHTML = "&lt;&lt;<br><br>" + (currentPage - 1);
	if ( currentPage != fileList.length ) {
		musicSheetDisplayTd[2].innerHTML = "&gt;&gt;<br><br>" + (currentPage + 1);
	} else {
		musicSheetDisplayTd[2].innerHTML = "";
		musicSheetDisplayTd[2].className = "nextPrevButton";
		if ( autoFlipItv != undefined ) {
			window.clearInterval( autoFlipItv );
			autoFlipItv = undefined;
		}
	}
}
function prevPage() {
	musicSheetDisplayTd[1].removeChild( musicSheetImagesDiv[currentPage - 1] );
	currentPage--;
	musicSheetDisplayTd[1].appendChild( musicSheetImagesDiv[currentPage - 1] );
	musicSheetDisplayTd[2].innerHTML = "&gt;&gt;<br><br>" + (currentPage + 1);
	if ( currentPage > 1 ) {
		musicSheetDisplayTd[0].innerHTML = "&lt;&lt;<br><br>" + (currentPage - 1);
	} else {
		musicSheetDisplayTd[0].innerHTML = "";
		musicSheetDisplayTd[0].className = "nextPrevButton";
	}
}

function setMusicSheetLayer( e ) {
	var height = document.body.clientHeight ? document.body.clientHeight : window.innerHeight;
	height = height - 240;
	musicSheetWrapperDiv.style.height = height;

	if ( musicSheetImgElements != undefined ) {
		for ( var i = 0; i < musicSheetImgElements.length; i++ )
			musicSheetImgElements[i].style.height = height;
	}
}

function setUploadForm() {
	fileChanged = false;
	musicSheetOopsDiv.style.display = "none";
	if ( musicSheetDisplayDiv.displaying ) {
		musicSheetDiv.removeChild( musicSheetDisplayDiv );
		musicSheetDisplayDiv.displaying = false;
	}
	musicSheetDiv.appendChild( musicSheetUploadFormDiv );
}

function setAutoFlipInterval() {
	var useMs = autoFlipInputElement.unit == "ms" ? true : false;
	autoflip = parseInt( autoFlipInputElement.value );
	if ( isNaN( autoflip ) || autoflip < 0 )
		autoflip = 0;
	var autoFlipText = document.getElementById( "autoflip_s" );
	autoFlipText.innerHTML = autoflip == 0 ? "<i>Disabled</i>" : (autoflip.toString() + " " + autoFlipInputElement.unit);
	if ( !useMs && autoflip > 0 ) {
		var bpm = parseInt( element[ "song_tempo" ].innerHTML );
		autoflip = Math.round( 60 * 1000 / bpm * 4 * autoflip );
	}
}

function confirmMusicSheetUpload() {
	if ( !fileChanged || !fileList || fileList.length == 0 ) {
		cancelMusicSheetUpload();
		return;
	}
	fileList.sort( function (x, y) {
		if ( x.name < y.name ) return -1;
		if ( x.name > y.name ) return 1;
		return 0;
	} );
	musicSheetImagesDiv = new Array();
	musicSheetImgElements = new Array();
	for ( var i = 0; i < fileList.length; i++ ) {
		musicSheetImagesDiv[i] = document.createElement( "div" );
		var reader = new FileReader();
		reader.i = i;
		reader.onload = function ( e ) {
			var index = this.i;
			musicSheetImgElements[index] = document.createElement( "img" );
			musicSheetImgElements[index].src = e.target.result;
			musicSheetImagesDiv[index].appendChild( musicSheetImgElements[index] );
			musicSheetImgElements[index].style.height = musicSheetWrapperDiv.style.height;
		}
		reader.readAsDataURL( fileList[i] );
	}
	musicSheetDisplayTd[1].innerHTML = "";
	musicSheetDisplayTd[1].appendChild( musicSheetImagesDiv[0] );
	currentPage = 1;
	if ( fileList.length == 1 )
		musicSheetDisplayTd[2].innerHTML = "";
	musicSheetDiv.removeChild( musicSheetUploadFormDiv );
	musicSheetDiv.appendChild( musicSheetDisplayDiv );
	musicSheetDisplayDiv.displaying = true;
}

function cancelMusicSheetUpload() {
	musicSheetDiv.removeChild( musicSheetUploadFormDiv );
	if ( !fileChanged && fileList && fileList.length > 0 ) {
		musicSheetDiv.appendChild( musicSheetDisplayDiv );
		musicSheetDisplayDiv.displaying = true;
	} else {
		musicSheetOopsDiv.style.display = "";
	}
}
