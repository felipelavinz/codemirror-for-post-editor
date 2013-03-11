(function($, window){
	$(function(){
		var wpEditor = document.getElementById('content');
		var editorCodeMirror = CodeMirror.fromTextArea( wpEditor, {
			mode: 'text/html',
			indentWithTabs: true,
			theme: 'monokai',
			lineWrapping: true,
			lineNumbers: true,
			autoCloseTags: true
		} );
	});
})(jQuery, window);