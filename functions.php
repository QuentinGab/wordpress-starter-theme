<?php

if (!defined('WP_DEBUG')) {
	die('Direct access forbidden.');
}
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = new \App\Config(__DIR__ . "/config");

$user = QuentinGab\WordpressOrm\User::current();

/**
 * Include php file with scoped variables
 */
function view($path, $variables = [])
{
	$filepath = __DIR__ . $path . ".php";

	if (!file_exists($filepath)) {
		throw new Exception("The file \"$filepath\" does not exists");
	}

	$output = NULL;
	// Extract the variables to a local namespace
	extract($variables);

	// Start output buffering
	ob_start();

	// Include the template file
	include $filepath;

	// End buffering and return its contents
	$output = ob_get_clean();

	return $output;
}

function config($key, $fallback = null)
{
	global $config;

	return $config->get($key, $fallback);
}

function dd(...$variables)
{
	var_dump($variables);
	die();
}

function user()
{
	global $user;
	return $user;
}

add_action('wp_enqueue_scripts', function () {
	// wp_enqueue_script('child-script', get_stylesheet_directory_uri() . '/public/js/app.js', [], null, true);

	//if you work on a child theme
	// wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
	// wp_enqueue_style('child-style', get_stylesheet_uri());
	// wp_enqueue_style('child-css', get_stylesheet_directory_uri() . '/public/css/style.css');
});

add_action('admin_enqueue_scripts', function () {

	//enqueue script in admin

}, 100);

add_action('admin_init', function () {

	// add_editor_style(get_stylesheet_directory_uri() . '/public/css/style.css');

});


/**
 * Include all custom post type
 */

/**
 * Include all migrations
 */
foreach (glob(__DIR__ . "/database/migrations/*.php") as $filename) {
	require_once $filename;
}

foreach (glob(__DIR__ . "/database/cpt/*.php") as $filename) {
	require_once $filename;
}
/**
 * Include all api routes
 */
foreach (glob(__DIR__ . "/routes/api/*.php") as $filename) {
	require_once $filename;
}

/**
 * Include all blocks
 */
foreach (glob(__DIR__ . "/blocks/*.php") as $filename) {
	require_once $filename;
}


/**
 * Setup your own settings
 */
function my_textbox_callback($args)
{  // Textbox Callback
	$option = get_option($args[0]);
	echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" />';
}
function register_my_setting()
{
	register_setting('general', '[option-id]', [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => NULL,
	]);

	add_settings_field(
		'[option-id]',
		'[Option Label]',
		'my_textbox_callback',
		'general',
		'default',
		array( // The $args
			'[option-id]' // Should match Option ID
		)
	);
}
add_action('admin_init', 'register_my_setting');
