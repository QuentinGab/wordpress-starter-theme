<?php

// unquote this line to register the block
// add_action('acf/init', 'acf_block_example');

function acf_block_example()
{
    // Bail out if function doesn’t exist.
    if (!function_exists('acf_register_block')) {
        return;
    }

    // Register a new block.
    acf_register_block(array(
        'name'            => 'block_example',
        'title'           => __('Block example', 'oseus'),
        'description'     => __('', 'oseus'),
        'render_callback' => 'acf_block_render_block_example',
        'category'        => 'formatting',
        'icon'            => 'admin-comments',
        'keywords'        => array('produit'),
    ));
}

/**
 *  This is the callback that displays the block.
 *
 * @param   array  $block      The block settings and attributes.
 * @param   string $content    The block content (emtpy string).
 * @param   bool   $is_preview True during AJAX preview.
 */
function acf_block_render_block_example($block, $content = '', $is_preview = false)
{
    // Render the block.
    return view("path", [
        'block' => $block
    ]);
}
