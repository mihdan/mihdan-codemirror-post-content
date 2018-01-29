<?php
/**
 * Plugin Name: Mihdan: CodeMirror For Post Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPOSA_VERSION' ) ) {
	define( 'WPOSA_VERSION', '1.0.0' );
}
if ( ! defined( 'WPOSA_NAME' ) ) {
	define( 'WPOSA_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}
if ( ! defined('WPOSA_DIR' ) ) {
	define( 'WPOSA_DIR', WP_PLUGIN_DIR . '/' . WPOSA_NAME );
}
if ( ! defined('WPOSA_URL' ) ) {
	define( 'WPOSA_URL', WP_PLUGIN_URL . '/' . WPOSA_NAME );
}

if ( file_exists( WPOSA_DIR . '/includes/class-wposa.php' ) ) {
	require_once( WPOSA_DIR . '/includes/class-wposa.php' );
}

add_action( 'admin_enqueue_scripts', function () {
	if ( 'post' !== get_current_screen()->id ) {
		return;
	}

	// подключаем редактор кода для HTML.
	$args = array(
		'type' => 'text/html',
	);
	$settings = wp_enqueue_code_editor( $args );

	// ничего не делаем если CodeMirror отключен.
	if ( false === $settings ) {
		return;
	}

//	$settings['codemirror']['matchTags'] = array(
//		'bothTags' => true,
//	);

	//$settings['codemirror']['inputStyle'] = "textarea";
	$settings['codemirror']['mode'] = "text/html";
	$settings['codemirror']['markTagPairs'] = true;
	$settings['codemirror']['autoRenameTags'] = true;
	$settings['codemirror']['tabSize'] = 4;
	$settings['codemirror']['lineSeparator'] = "\n";

	$settings['codemirror']['extraKeys']['Tab'] = 'emmetExpandAbbreviation';
	$settings['codemirror']['extraKeys']['Enter'] = 'emmetInsertLineBreak';
	$settings['codemirror']['extraKeys']['Ctrl-Alt-A'] = 'emmetW';

	/**
	 * As noted above, CodeMirror and its bundled modes and add-ons are registered in a wp-codemirror script handle.
	 * Also important to note here that this script does not define a global CodeMirror object
	 * but rather a wp.CodeMirror one. This ensures that other plugins that may be including other CodeMirror
	 * bundles won’t have conflicts. This also means that if you do want to include fortran.js from CodeMirror,
	 * that you’ll need to bundle it to call wp.CodeMirror.defineMode() instead of CodeMirror.defineMode().
	 * A workaround for having to do this would be the following, but be aware of potential conflicts:
	 */
	wp_add_inline_script(
		'wp-codemirror',
		'window.CodeMirror = wp.CodeMirror;'
	);

	//print_r( $settings );die;

	wp_enqueue_script( 'emmet', plugins_url( 'assets/js/emmet-codemirror-plugin.js', __FILE__ ), array( 'code-editor' ) );

	// инициализация
	wp_add_inline_script(
		'emmet',
		sprintf( 'jQuery( function() { wp.codeEditor.initialize( "content", %s ); } );', wp_json_encode( $settings, JSON_PRETTY_PRINT ) )
	);
} );

if ( class_exists( 'WP_OSA' ) ) {
	/**
	 * Object Instantiation.
	 *
	 * Object for the class `WP_OSA`.
	 */
	$wposa_obj = new WP_OSA();
	// Section: Basic Settings.
	$wposa_obj->add_section(
		array(
			'id'    => 'wposa_basic',
			'title' => __( 'Basic Settings', 'WPOSA' ),
		)
	);
	// Section: Other Settings.
	$wposa_obj->add_section(
		array(
			'id'    => 'wposa_other',
			'title' => __( 'Other Settings', 'WPOSA' ),
		)
	);
	// Field: Text.
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'      => 'text',
			'type'    => 'text',
			'name'    => __( 'Text Input', 'WPOSA' ),
			'desc'    => __( 'Text input description', 'WPOSA' ),
			'default' => 'Default Text',
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'                => 'text_no',
			'type'              => 'number',
			'name'              => __( 'Number Input', 'WPOSA' ),
			'desc'              => __( 'Number field with validation callback `intval`', 'WPOSA' ),
			'default'           => 1,
			'sanitize_callback' => 'intval'
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'      => 'password',
			'type'    => 'password',
			'name'    => __( 'Password Input', 'WPOSA' ),
			'desc'    => __( 'Password field description', 'WPOSA' ),
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'textarea',
			'type' => 'textarea',
			'name' => __( 'Textarea Input', 'WPOSA' ),
			'desc' => __( 'Textarea description', 'WPOSA' ),
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'checkbox',
			'type' => 'checkbox',
			'name' => __( 'Checkbox', 'WPOSA' ),
			'desc' => __( 'Checkbox Label', 'WPOSA' ),
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'radio',
			'type' => 'radio',
			'name' => __( 'Radio', 'WPOSA' ),
			'desc' => __( 'Radio Button', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'multicheck',
			'type' => 'multicheck',
			'name' => __( 'Multile checkbox', 'WPOSA' ),
			'desc' => __( 'Multile checkbox description', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);
	$wposa_obj->add_field(
		'wposa_basic',
		array(
			'id'   => 'select',
			'type' => 'select',
			'name' => __( 'A Dropdown', 'WPOSA' ),
			'desc' => __( 'A Dropdown description', 'WPOSA' ),
			'options' => array(
				'yes' => 'Yes',
				'no'  => 'No'
			)
		)
	);
	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'image',
			'type' => 'image',
			'name' => __( 'Image', 'WPOSA' ),
			'desc' => __( 'Image description', 'WPOSA' ),
			'options' => array(
				'button_label' => 'Choose Image'
			)
		)
	);
	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'file',
			'type' => 'file',
			'name' => __( 'File', 'WPOSA' ),
			'desc' => __( 'File description', 'WPOSA' ),
			'options' => array(
				'button_label' => 'Choose file'
			)
		)
	);
	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'color',
			'type' => 'color',
			'name' => __( 'Color', 'WPOSA' ),
			'desc' => __( 'Color description', 'WPOSA' ),
		)
	);
	$wposa_obj->add_field(
		'wposa_other',
		array(
			'id'   => 'wysiwyg',
			'type' => 'wysiwyg',
			'name' => __( 'WP_Editor', 'WPOSA' ),
			'desc' => __( 'WP_Editor description', 'WPOSA' ),
		)
	);
}
