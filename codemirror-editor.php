<?php
/*
Plugin Name: CodeMirror for Post Editor
Plugin URI: http://www.yukei.net
Description: Use the CodeMirror code editor component on the HTML editor in posts
Version: 0.4.1
Author: Felipe Lavín
Author URI: http://www.yukei.net
License: GPL3
*/

class CodeMirrorEditor{
	const codemirror_version = '3.2.0';
	const plugin_ver = '4';
	private static $instance;
	private function __construct(){
		add_action('admin_enqueue_scripts', array($this, 'enqueueStuff'));
		add_action('admin_head', array($this, 'printScript'));
		add_action('admin_footer', array($this, 'addContainer'));
		add_filter('mce_buttons', array($this, 'addEditorButton'));
		add_filter('mce_external_plugins', array($this, 'registerButton'));
		add_action('wp_ajax_get_codemirror_editor', array($this, 'getEditor'));
	}
	public static function getInstance(){
		if ( !isset(self::$instance) ){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	public function addContainer(){
		if ( get_current_screen()->base !== 'post' )
			return;
		echo '<div id="codemirror_editor-container"></div>';
	}
	public function printScript(){
		if ( get_current_screen()->base !== 'post' )
			return;
		$options = array(
			'actionurl' => add_query_arg(array(
				'action' => 'get_codemirror_editor',
				), admin_url('admin-ajax.php')),
		);
		echo '<script type="text/javascript">';
			echo 'var CodeMirrorEditor_Settings = '. json_encode( $options );
		echo '</script>';
	}
	public function enqueueStuff(){
		// only enqueue stuff on the post editor page
		if ( get_current_screen()->base !== 'post' )
			return;
		wp_enqueue_script('codemirror-main', $this->plugin_url('/js/lib/codemirror.js'), array(), self::codemirror_version, true);
		wp_enqueue_script('codemirror-xml',  $this->plugin_url('/js/mode/xml/xml.js'), array('codemirror-main'), self::codemirror_version, true);
		// wp_enqueue_script('codemirror-css',  $this->plugin_url('/js/mode/css/css.js'), array('codemirror-main'), self::codemirror_version, true);
		// wp_enqueue_script('codemirror-js',   $this->plugin_url('/js/mode/javascript/javascript.js'), array('codemirror-main'), self::codemirror_version, true);
		wp_enqueue_script('codemirror-html', $this->plugin_url('/js/mode/htmlmixed/htmlmixed.js'), array('codemirror-main', 'codemirror-xml'), self::codemirror_version, true);
		wp_enqueue_script('codemirror-autocomplete', $this->plugin_url('/js/addon/edit/closetag.js'), array('codemirror-main'), self::codemirror_version, true);
		wp_enqueue_style('codemirror-style', $this->plugin_url('/js/lib/codemirror.css'), array('wp-jquery-ui-dialog', 'dashicons'), self::plugin_ver, 'screen');
		wp_enqueue_style('codemirror-theme', $this->plugin_url('/js/theme/monokai.css'), array('codemirror-style'), self::codemirror_version, 'screen');
		wp_enqueue_script('jquery-ui-dialog');
	}
	private function plugin_url( $path ){
		return plugins_url('codemirror-for-post-editor/'. $path);
	}
	public function addEditorButton( $buttons ){
		array_push($buttons, "|", "codemirror_editor");
		return $buttons;
	}
	public function registerButton( $plugin_arr ){
		$plugin_arr['codemirror_editor'] = $this->plugin_url('js/editor_plugin.js');
		return $plugin_arr;
	}
	public function getEditor(){
		echo '<textarea name="codemirror_editor" id="codemirror-editor" cols="30" rows="100" style="width:100%"></textarea>';
		exit;
	}
}
// Instantiate the class object
CodeMirrorEditor::getInstance();
