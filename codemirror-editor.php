<?php
/*
Plugin Name: CodeMirror for Post Editor
Plugin URI: http://www.yukei.net
Description: Write beautiful code using the CodeMirror editor on your posts
Version: 1.0.0
Author: Felipe Lavín
Author URI: http://www.yukei.net
License: GPL3
*/

class Code_Mirror_Editor{
	const codemirror_version = '5.0.0';
	const plugin_ver = '5';
	public function init(){
		add_action('admin_enqueue_scripts', array($this, 'enqueue_stuff'));
		add_action('admin_footer', array($this, 'add_container'));
		add_filter('mce_buttons', array($this, 'add_editor_button'));
		add_filter('mce_external_plugins', array($this, 'register_button'));
		add_action('wp_ajax_get_codemirror_editor', array($this, 'get_editor'));
	}
	public function add_container(){
		if ( get_current_screen()->base !== 'post' )
			return;
		echo '<div id="codemirror_editor-container"></div>';
	}
	public function enqueue_stuff(){
		// only enqueue stuff on the post editor page
		if ( get_current_screen()->base !== 'post' )
			return;
		wp_enqueue_script('codemirror-main', $this->plugin_url('/assets/codemirror/lib/codemirror.js'), array(), self::codemirror_version, true);
		wp_enqueue_script('codemirror-xml',  $this->plugin_url('/assets/codemirror/mode/xml/xml.js'), array('codemirror-main'), self::codemirror_version, true);
		// wp_enqueue_script('codemirror-css',  $this->plugin_url('/assets/codemirror/mode/css/css.js'), array('codemirror-main'), self::codemirror_version, true);
		// wp_enqueue_script('codemirror-js',   $this->plugin_url('/assets/codemirror/mode/javascript/javascript.js'), array('codemirror-main'), self::codemirror_version, true);
		wp_enqueue_script('codemirror-html', $this->plugin_url('/assets/codemirror/mode/htmlmixed/htmlmixed.js'), array('codemirror-main', 'codemirror-xml'), self::codemirror_version, true);
		wp_enqueue_script('codemirror-autocomplete', $this->plugin_url('/assets/codemirror/addon/edit/closetag.js'), array('codemirror-main'), self::codemirror_version, true);
		wp_enqueue_style('codemirror-for-post-editor', $this->plugin_url('/css/codemirror-for-post-editor.css'), self::plugin_ver, 'screen');
		wp_enqueue_style('codemirror-style', $this->plugin_url('/assets/codemirror/lib/codemirror.css'), array('codemirror-for-post-editor', 'wp-jquery-ui-dialog', 'dashicons'), self::plugin_ver, 'screen');
		wp_enqueue_style('codemirror-theme', $this->plugin_url('/assets/codemirror/theme/monokai.css'), array('codemirror-style'), self::codemirror_version, 'screen');
		wp_enqueue_script('jquery-ui-dialog');

		$options = array(
			'actionurl' => add_query_arg(array(
				'action' => 'get_codemirror_editor',
				), admin_url('admin-ajax.php')),
		);

		wp_localize_script( 'codemirror-main', 'CodeMirrorEditor_Settings', $options );
	}
	private function plugin_url( $path ){
		return plugins_url( $path, __FILE__ );
	}
	public function add_editor_button( $buttons ){
		array_push($buttons, "|", "codemirror_editor");
		return $buttons;
	}
	public function register_button( $plugin_arr ){
		$plugin_arr['codemirror_editor'] = $this->plugin_url('js/editor_plugin.js');
		return $plugin_arr;
	}
	public function get_editor(){
		echo '<textarea name="codemirror_editor" id="codemirror-editor" cols="30" rows="100" style="width:100%"></textarea>';
		exit;
	}
}
// Instantiate the class object
$__code_mirror_for_post_editor = new Code_Mirror_Editor;
$__code_mirror_for_post_editor->init();