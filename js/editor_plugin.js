(function($, window) {
	window.codeMirrorEditor = {};
	tinymce.create('tinymce.plugins.codeMirrorEditorPlugin', {
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			ed.addCommand('init_codemirror_editor', t._init_codemirror_editor, t);
			ed.addButton('codemirror_editor',{
				title : 'CodeMirror Editor',
				cmd : 'init_codemirror_editor',
				image : url + '/img/editor.png'
			});
		},

		getInfo : function() {
			return {
				longname : 'CodeMirror Editor for WordPress',
				author : 'Felipe Lavín;',
				authorurl : 'http://www.yukei.net',
				infourl : 'http://www.yukei.net',
				version : '0.2'
			};
		},
		// Private methods
		_init_codemirror_editor : function() { // open a popup window
			var codeMirror_container = document.getElementById('codemirror_editor-container');
			var setEditorSize = function(){
				var _window = $(window),
					height = Math.floor( _window.height() * 0.9 ) - 28,
					width = Math.floor( _window.width() * 0.95 ),
					dialog = $(codeMirror_container);
				dialog.dialog('option', {
					width: width,
					height: height
				}).dialog('option', 'position', {
					at: 'center'
				});
				codeMirrorEditor.editor.setSize( dialog.innerWidth(), dialog.innerHeight() );
			};
			if ( ! codeMirrorEditor.editor ) {
				codeMirrorEditor.editor = CodeMirror( codeMirror_container, {
					mode: 'text/html',
					indentWithTabs: true,
					theme: 'monokai',
					lineWrapping: true,
					lineNumbers: true,
					autofocus: true,
					autoCloseTags: {
						whenClosing: true,
						whenOpening: true,
						indentTags: []
					}
				} );
			}
			/**
			 * @todo: agregar boton para insertar imágenes y documentos; sobreescribiendo send_to_editor
			 */
			$(codeMirror_container).dialog({
				dialogClass: 'wp-dialog',
				resizable: false,
				modal: true,
				draggable: false,
				closeOnEscape: true,
				buttons: [{
					text: 'Insert Media',
					click: function(){
						var _send_to_editor_original = window.send_to_editor;
						window.send_to_editor = function( html ){
							var editorDoc = codeMirrorEditor.editor.getDoc(),
								currentPosition = editorDoc.getCursor(),
								currentLine = editorDoc.getLine( currentPosition.line );
							var newLine = currentLine.substring(0, currentPosition.ch ) + html + currentLine.substring( currentPosition.ch + 1 );
							editorDoc.setLine( currentPosition.line, newLine );
							codeMirrorEditor.editor.focus();
							try {
								tb_close();
							} catch (e) { }
							window.send_to_editor = _send_to_editor_original;
						};
						$('#wp-content-media-buttons a').trigger('click');
					}
				}, {
					text: 'Update entry',
					click: function(){
						$(this).dialog('close');
					}
				}],
				open: function(event, ui){
					tinymce.activeEditor.save();
					setEditorSize();
					codeMirrorEditor.editor.setValue( document.getElementById('content').value );
					codeMirrorEditor.editor.refresh();
				},
				beforeClose: function(event, ui){
					// update wp-editor value
					var value = codeMirrorEditor.editor.getValue('<br>');
					value = $('<div id="codemirror_editor-temp-buffer">' + value +'</div>');
					// kind of a quick-n-dirty hack to avoid unnecessary line breaks
					value.find('* > br').remove();
					$('#content').val( value.html() ).trigger('change');
					tinymce.activeEditor.load();
				},
				close: function(event, ui){
					tinymce.activeEditor.focus();
				}
			});
			$(window).resize(function(){ setEditorSize(); });
			return true;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('codemirror_editor', tinymce.plugins.codeMirrorEditorPlugin);
})(jQuery, window);