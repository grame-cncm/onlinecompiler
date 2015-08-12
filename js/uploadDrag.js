
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

		for (x = 0; x < e.dataTransfer.files.length; x = x + 1) {
			formData.append('file[]',e.dataTransfer.files[x]);
		}

		xhr.open('post','fileDroped.php');
		xhr.send(formData);
	};

}());
