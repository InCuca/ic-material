<?php
/**
 * Plugin Name:     IC Material
 * Plugin URI:      https://incuca.net
 * Description:     Custom post type de materials gratuitos
 * Author:          INCUCA
 * Author URI:      https://incuca.net
 * Text Domain:     ic-material
 * Version:         0.1.0
 *
 * @package         Ic_Enfold
 */

// Register Custom Post Type
function ic_mat_register_material() {
	$args = array(
    'label' => 'Materiais Gratuitos',
    'labels' => array(
      'menu_name' => 'Materiais',
    ),
    'public' => true,
    'menu_position' => 5,
    'has_archive' => true,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'supports' => array(
      'title', 'editor', 'comments', 'revisions', 'trackbacks',
      'author', 'excerpt', 'thumbnail', 'post-formats'
    ),
    'taxonomies' => array(
      'category',
      'ic_material_type',
    )
  );
  register_post_type( 'ic_material', $args );
  $taxArgs = array(
    'label' => 'Tipos de conteúdo',
    'labels' => array(
      'singular_name' => 'Tipo de conteúdo',
      'search_items'      => 'Buscar tipos',
      'all_items'         => 'Todos os tipos',
      'parent_item'       => 'Tipo pai',
      'parent_item_colon' => 'Tipo pai',
      'edit_item'         => 'Editar tipo',
      'update_item'       => 'Atualizar tipo',
      'add_new_item'      => 'Adicionar tipo',
      'new_item_name'     => 'Novo tipo',
      'menu_name'         => 'Tipos de conteúdo',
    ),
    'public' => true,
    'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
  );
  register_taxonomy('ic_material_type', 'ic_material', $taxArgs);
}
add_action( 'init', 'ic_mat_register_material', 0 );

function ic_mat_featured_box($post) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'ic_mat_featured_nonce' );
	$value = get_post_meta( $post->ID, '_ic_mat_featured', true );
	$selected = function ($for) use ($value) {
		if ($value === $for || (empty($value) && $for === 'no')) {
			return ' selected="selected"';
		}
		return '';
	};
	echo '<label for="ic_mat_featured">Destaque</label> ';

	echo '<select name="ic_mat_featured" id="ic_mat_featured">';
	echo '<option value="yes"'.$selected('yes').'>Sim</option>';
	echo '<option value="no"'.$selected('no').'>Não</option>';
	echo '</select>';
}
function ic_mat_boxes() {
    add_meta_box(
		'ic_mat_featured',
		'Destaque',
		'ic_mat_featured_box',
		'ic_material',
		'side'
	);
}
add_action( 'add_meta_boxes', 'ic_mat_boxes' );

function ic_mat_save_ic_material($post_id) {
	$post_type = get_post_type($post_id);
	if ("ic_material" != $post_type) return;

	if ( ! isset( $_POST['ic_mat_featured_nonce'] ) || ! wp_verify_nonce( $_POST['ic_mat_featured_nonce'], plugin_basename( __FILE__ ) ) )
	  return;
	$featured = sanitize_text_field( $_POST['ic_mat_featured'] );
	update_post_meta($post_id, '_ic_mat_featured', $featured);
}
add_action( 'save_post', 'ic_mat_save_ic_material' );