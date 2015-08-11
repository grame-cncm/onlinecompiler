
(function() {
    function $id(id) {
	return document.getElementById(id);
    }

    function FileDragHover(e) {
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
    }
    
    function FileSelectHandler(e) {
	FileDragHover(e);
	var files = e.target.files || e.dataTransfer.files;
	f = files[0];
	UploadFile(f);
	window.setTimeout('__goto__',250);
	//document.location.replace("goto_codeFaust.php");
	//__goto__
    }

    function UploadFile(file) {
	if (location.host.indexOf("sitepointstatic") >= 0) return
	var xhr = new XMLHttpRequest();
	if (xhr.upload && file.size <= $id("MAX_FILE_SIZE").value) {
	    xhr.open("POST", $id("upload").action, true);
	    xhr.setRequestHeader("X_FILENAME", file.name);
	    xhr.send(file);
	}
    }

    function Init() {
	var filedrag = $id("filedrag");
	var xhr = new XMLHttpRequest();
	if (xhr.upload) {
	    filedrag.addEventListener("dragover", FileDragHover, false);
	    filedrag.addEventListener("dragleave", FileDragHover, false);
	    filedrag.addEventListener("drop", FileSelectHandler, false);
	    filedrag.style.display = "block";
	}
    }

    if (window.File && window.FileList && window.FileReader) {
	Init();
    }

})();