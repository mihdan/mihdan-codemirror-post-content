<?php
/**
 * Plugin Name: Mihdan: CodeMirror For Post Content
 * Description: WordPress-плагин, добавляющий редактор из ядра с подсветкой кода на экран создания/редактирования поста
 * Version: 1.0.2
 * Plugin URI: https://www.kobzarev.com
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-codemirror-post-content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_notices', function () {
	?>
	<?php if ( get_user_option( 'rich_editing' ) === 'true' ) : ?>
		<div class="notice notice-warning is-dismissible">
			<p><b>CodeMirror For Post Content:</b> для правильной работы плагина необходимо отключить визуальный редактор для записей в вашем <a href="<?php echo get_edit_profile_url(); ?>">профиле</a>.</p>
		</div>
	<?php endif; ?>
	<?php
} );

add_action( 'admin_enqueue_scripts', function () {
	if ( ! in_array( get_current_screen()->id, array( 'post', 'page' ) ) ) {
		return;
	}

	if ( get_user_option( 'rich_editing' ) === 'true' ) {
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
	$settings['height'] = '900px';
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
//	wp_add_inline_script(
//		'wp-codemirror__',
//		'window.CodeMirror = wp.CodeMirror;'
//	);

	//print_r( $settings );die;

	wp_enqueue_script( 'emmet', plugins_url( 'assets/js/emmet-codemirror-plugin.js', __FILE__ ), array( 'code-editor' ) );

	// инициализация
	wp_add_inline_script(
		'emmet',
		sprintf( 'jQuery( function() { var mcpc = wp.codeEditor.initialize( "content", %s ); /**mcpc.setSize( null, 900 );**/ } );', wp_json_encode( $settings, JSON_PRETTY_PRINT ) )
	);

	wp_add_inline_style(
		'wp-codemirror',
		'.CodeMirror { height: 800px; margin-top: 37px; }'
	);
} );

add_action( 'admin_print_footer_scripts', function () {
	if ( ! wp_script_is( 'quicktags' ) ) {
		return;
	}
	?>
	<script type="text/javascript">
		QTags.addButton( 'mcpc_css', 'CSS', '[css]', '[/css]', 'p', 'Подсветка CSS синтаксиса', 100 );
		QTags.addButton( 'mcpc_html', 'HTML', '[html]', '[/html]', 'p', 'Подсветка HTML синтаксиса', 101 );
		QTags.addButton( 'mcpc_php', 'PHP', '[php]', '[/php]', 'p', 'Подсветка PHP синтаксиса', 102 );
	</script>
	<?php
} );

// eof;
