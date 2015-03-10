
<script type="text/javascript" src="<?php echo TEMPLATE_PATH; ?>swfupload/swfupload.js"></script>
<script type="text/javascript">
var swfu;
$(function () {
	var settings_object = {
			post_params: {"PHPSESSID": "<?php echo session_id(); ?>"},
			upload_url : URL_PATH+"/templates/swfupload/upload/",
			flash_url : TEMPLATE_PATH+"swfupload/swfupload.swf",
			flash9_url : TEMPLATE_PATH+"swfupload/swfupload_fp9.swf",
			
			file_size_limit : "20 MB",
			button_placeholder_id : "SWFU",
			button_width: 180,
			button_height: 18,
			button_text : '<span class="button">Upload files</span>',
			button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; }',
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,



			swfupload_preload_handler : preLoad,
			swfupload_load_failed_handler : loadFailed,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,

			custom_settings : {
				upload_target : "divFileProgressContainer"
			}
		};
		
	swfu = new SWFUpload(settings_object);
	$("#directory td").hover(fpOver,fpOut).click(fpClick);
});
function fpOver () {
	$(this).css('background','lightblue');	
}
function fpOut() {
	$(this).css('background','none');	
}
function fpClick() {
	if ($(this).attr("data-path")=='dir') {
		Filepicker.select($(this).attr("data-path"));
	} else {
		Filepicker.reload($(this).attr("data-path"));
	}
}
function preLoad() {
	if (!this.support.loading) {
		alert("You need the Flash Player 9.028 or above to use SWFUpload.");
		return false;
	}
}
function loadFailed() {
	alert("Something went wrong while loading SWFUpload. If this were a real application we'd clean up and then give you an alternative");
}
function fileQueueError(file, errorCode, message) {
	try {
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "You have attempted to queue too many files.";
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		default:
			alert(message);
			break;
		}


	} catch (ex) {
		this.debug(ex);
	}

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadProgress(file, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);

		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		progress.setStatus("Uploading...");
		progress.toggleCancel(true, this);
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);

		if (serverData === "OK") {
			addImage(file.name);

			progress.setStatus("Upload Complete.");
			progress.toggleCancel(false);
		} else {
			progress.setStatus("Error.");
			progress.toggleCancel(false);
			alert(serverData);

		}


	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("All files received.");
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	}
}
function addImage(name) {
	var end = name.substr(name.length-3);
	var src = "";
	var path = ROOT_PATH+"media/"+name;
	if (end == 'jpg' || end == 'png' || end == 'gif') {
		var src = path;
	}
	var td = $("<td data-path=\""+path+"\"><img src=\""+src+"\" /><br />"+name+"</td>");
	td.hover(fpOver,fpOut).click(fpClick);
	var tr = $("#directory tr:last-child");
	if (tr.find("td").length >= 7) {
		tr = $("<tr />");
		$("#directory").append(tr);
	}
	tr.append(td);
}
function uploadError(file, errorCode, message) {
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Stopped");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
		default:
			alert(message);
			break;
		}


	} catch (ex3) {
		this.debug(ex3);
	}

}
function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
</script>
<style type="text/css">
#directory td {
	font-size:10px; 
	width: 60px; 
	height: 60px; 
	text-align:center;
	display:inline;
}
#directory img {
	height: 50px; 
	width:50px;
}

.progressWrapper {
	width: 357px;
	overflow: hidden;
}

.progressContainer {
	margin: 5px;
	padding: 4px;
	border: solid 1px #E8E8E8;
	background-color: #F7F7F7;
	overflow: hidden;
}
/* Message */
.message {
	margin: 1em 0;
	padding: 10px 20px;
	border: solid 1px #FFDD99;
	background-color: #FFFFCC;
	overflow: hidden;
}
/* Error */
.red {
	border: solid 1px #B50000;
	background-color: #FFEBEB;
}

/* Current */
.green {
	border: solid 1px #DDF0DD;
	background-color: #EBFFEB;
}

/* Complete */
.blue {
	border: solid 1px #CEE2F2;
	background-color: #F0F5FF;
}

.progressName {
	font-size: 8pt;
	font-weight: 700;
	color: #555;
	width: 323px;
	height: 14px;
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
}

.progressBarInProgress,
.progressBarComplete,
.progressBarError {
	font-size: 0;
	width: 0%;
	height: 2px;
	background-color: blue;
	margin-top: 2px;
}

.progressBarComplete {
	width: 100%;
	background-color: green;
	visibility: hidden;
}

.progressBarError {
	width: 100%;
	background-color: red;
	visibility: hidden;
}

.progressBarStatus {
	margin-top: 2px;
	width: 337px;
	font-size: 7pt;
	font-family: Arial;
	text-align: left;
	white-space: nowrap;
}

a.progressCancel {
	font-size: 0;
	display: block;
	height: 14px;
	width: 14px;
	background-image: url(../images/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}

a.progressCancel:hover {
	background-position: 0px 0px;
}

/* -- SWFUpload Object Styles ------------------------------- */
.swfupload {
	vertical-align: top;
}
</style>
<div style="width: 500px; height: 500px;">
<?php 
echo $this->path;
$mediapath = SITE_PATH."/media/";
$dir = new DirectoryIterator($mediapath);
echo "<table id=\"directory\">";

echo "<tr>";
$i=0;
$picformats = array('png','jpg','gif');
foreach ($dir as $file) {
	if (!$dir->isDot()) {
		$path = URL_PATH.'/media/'.$file;
		if (strlen($file)>4 && in_array(substr($file,-3),$picformats)) {
			$picture = $path;
		} else {
			$picture = "";
		}
		if ($i++%7==0) echo "</tr><tr>";
		echo "<td data-path=\"$path\" data-type=\"".($dir->isDir() ? 'dir' : 'file')."\">
			<img src=\"$picture\" /><br />
			$file
			</td>";
	}
}
echo "</tr></table>";
?>
</div>
<div id="SWFU"></div>
<div id="divFileProgressContainer" style="height: 75px;"></div>