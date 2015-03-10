{% macro jquery() %}
	<script type="text/javascript" src="{{ MEDIA_PATH }}/js/jquery.js"></script>
{% endmacro %}
{% macro fancybox() %}
	<script type="text/javascript" src="{{ TEMPLATE_MEDIA_PATH }}fancybox/jquery.fancybox.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="{{ TEMPLATE_MEDIA_PATH }}fancybox/jquery.fancybox.css" />
{% endmacro %}
{% macro contextmenu() %}
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/jquery.contextMenu.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="{{ TEMPLATE_MEDIA_PATH }}form/styles/jquery.contextMenu.css" />
{% endmacro %}
{% macro datebox() %}
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/DateBox.js" type="text/javascript"></script>
{% endmacro %}
{% macro combobox() %}
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/ComboBox.js" type="text/javascript"></script>
{% endmacro %}
{% macro form() %}
	{{ _self.f() }}
	<script src="{{ TEMPLATE_MEDIA_PATH }}swfupload/swfupload.js" type="text/javascript"></script>
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/Data.js" type="text/javascript"></script>
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/Filepicker.js" type="text/javascript"></script>
	<script src="{{ TEMPLATE_MEDIA_PATH }}form/js/Form.js" type="text/javascript"></script>
{% endmacro %}
{% macro f() %}
	<script src="{{ TEMPLATE_MEDIA_PATH }}common/js/F.js" type="text/javascript"></script>
{% endmacro %}
{% macro richtext(css) %}
<script type="text/javascript" src="{{ TEMPLATE_MEDIA_PATH }}tinymce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$().ready(function() {
		$('textarea.richtext').tinymce({
			// Location of TinyMCE script
			script_url : '{{ TEMPLATE_MEDIA_PATH }}tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "{{ css }}",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js"

		});
	});
</script>
	
{% endmacro %}