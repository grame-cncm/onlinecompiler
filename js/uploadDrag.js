(function  () {

	var dropzone = document.getElementById('filedrag');

	dropzone.ondragover = function() {
		this.className = 'hover';
		return false;
	};

	dropzone.ondragleave = function(){
		this.className = '';
		return false;
	};

	dropzone.ondrop = function(e){
		e.preventDefault();
		this.className = '';
		var formData = new FormData(),
		xhr = new XMLHttpRequest(),
		x;

		// Analyze what was dropped
		// console.log("TYPES : " + e.dataTransfer.types);
		// for (x = 0; x < e.dataTransfer.types.length; x = x + 1) {
		// 	var type = e.dataTransfer.types.item(x);
		// 	console.log("Type " + x + " : " + type);
		// 	console.log("Data " + x + " : " + e.dataTransfer.getData(type));
		// }

		// if (e.dataTransfer.files.length == 0) {
		// 	// we are not droping a file, but maybe a text or an URL
		// 	var code = e.dataTransfer.getData("text");
		// 	console.log("TEXT : " + e.dataTransfer.getData("text"));
		// 	var myblob = new Blob([code], { type: 'application/octet-stream', endings: 'native' });
		// 	formData.append('file[]', myblob, "untitled.dsp");
		// } else {
		// 	// we are dropping a file
		// 	for (x = 0; x < e.dataTransfer.files.length; x = x + 1) {
		// 		var f = e.dataTransfer.files[x];
		// 		console.log("--> "+ x + " : " + f);
		// 		formData.append('file[]',f);
		// 	}
		// }

		if (e.dataTransfer.files.length > 0) {
			// we are dropping a file
			for (x = 0; x < e.dataTransfer.files.length; x = x + 1) {
				var f = e.dataTransfer.files[x];
				console.log("FILE : "+ x + " : " + f.filename);
				formData.append('file[]',f);
			}
		} else {
			var url = e.dataTransfer.getData("URL");
			if (url.length > 0) {
				console.log("URL : " + url);
			}
			// we are not droping a file, but maybe a text or an URL
			var code = e.dataTransfer.getData("text");
			console.log("TEXT : " + e.dataTransfer.getData("text"));
			var myblob = new Blob([code], { type: 'application/octet-stream', endings: 'native' });
			formData.append('file[]', myblob, "untitled.dsp");
		}




		xhr.open('post','fileDroped.php');
		xhr.send(formData);

		// goto marker will be replaced here
		window.setTimeout('__goto__', 250);
	};

}());
