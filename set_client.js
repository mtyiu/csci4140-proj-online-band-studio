function setClient( bandid, username ) {
	var song = new Array();
	song["name"] = document.getElementById("song_name");
	song["author"] = document.getElementById("author");
	song["tempo"] = document.getElementById("tempo");
	song["key"] = document.getElementById("key");
	song["set"] = document.getElementById("setSongBtn");

	for ( var i in song ) {
		song[i].disabled = true;
	}

	song["set"].style.display = "none";

}
