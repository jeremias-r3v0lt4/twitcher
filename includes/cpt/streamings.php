<?php 
// Register Custom Post Type
function register_twchr_stream() {

	$labels = array(
		'name'                  => _x( 'Streamings', 'Post Type General Name', 'twitcher' ),
		'singular_name'         => _x( 'Streaming', 'Post Type Singular Name', 'twitcher' ),
		'menu_name'             => __( 'Twitcher', 'twitcher' ),
		'name_admin_bar'        => __( 'Streamings', 'twitcher' ),
		'archives'              => __( 'Archive streamings', 'twitcher' ),
		'attributes'            => __( 'Atributes', 'twitcher' ),
		'parent_item_colon'     => __( 'Parent', 'twitcher' ),
		'all_items'             => __( 'All streamings', 'twitcher' ),
		'add_new_item'          => __( 'Add new Streaming', 'twitcher' ),
		'add_new'               => __( 'Add new', 'twitcher' ),
		'new_item'              => __( 'New Streaming', 'twitcher' ),
		'edit_item'             => __( 'Edit Streaming', 'twitcher' ),
		'update_item'           => __( 'Update Streaming', 'twitcher' ),
		'view_item'             => __( 'View Streaming', 'twitcher' ),
		'view_items'            => __( 'View streamings', 'twitcher' ),
		'search_items'          => __( 'Search streamings', 'twitcher' ),
		'not_found'             => __( 'Streaming not found', 'twitcher' ),
		'not_found_in_trash'    => __( 'Streaming not found in trash', 'twitcher' ),
		'featured_image'        => __( 'Featured image', 'twitcher' ),
		'set_featured_image'    => __( 'Set featured image', 'twitcher' ),
		'remove_featured_image' => __( 'Remove featured image', 'twitcher' ),
		'use_featured_image'    => __( 'Use featured image', 'twitcher' ),
		'insert_into_item'      => __( 'Insert streaming', 'twitcher' ),
		'uploaded_to_this_item' => __( 'Uploaded to this streaming', 'twitcher' ),
		'items_list'            => __( 'List streamings', 'twitcher' ),
		'items_list_navigation' => __( 'List navigation', 'twitcher' ),
		'filter_items_list'     => __( 'Filter streamings list', 'twitcher' ),
	);

	$args = array(
		'label'                 => __( 'Streaming', 'twitcher' ),
		'description'           => __( 'Streaming de strean', 'twitcher' ),
		'labels'                => $labels,
		'supports'              => array( 
											'title',
											'thumbnail',
											'editor',
											'comments',
											'excerpt' 
										),
		'taxonomies'            => array( 'schedule','category','post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'menu_icon' 			=> plugins_url('twitcher/includes/assets/logo_menu.svg'),
		'show_in_rest'			=> false

	);
	register_post_type( 'twchr_streams', $args );

}
add_action( 'init', 'register_twchr_stream', 0 );




// Creo un endpoint

function streaming_endpoint() {
    register_rest_route( 'twchr/', 'twchr_get_streaming', array(
        'methods'  => 'GET',
        'callback' => 'get_streaming',
    ) );
}

add_action( 'rest_api_init', 'streaming_endpoint' );

function get_streaming( $request ){
	// Solicita a BDD todos los post-type = twchr_streams que esten plubicados
	$posts = get_posts(array(
		'post_type'  => 'twchr_streams',
		'post_status' => "publish"
	));

	// Inicializo un array vacio
	$array_response = array();

	// Itero la List post-type 
	foreach ($posts as $key =>  $value){	
			$id = $value->{'ID'}; // guardo su id
			$title = $value->{'post_title'}; // guardo su title
			$stream_id = get_post_meta( $id, 'twchr-from-api_id', true ); // guardo el custom-field steram_id

			// Guardo los datos anteriores en un array
			$post_for_api = array(
				'wordpress_id' => $id,
				'title' => $title,
				'twchr_id' => (int)$stream_id // Convierto stream_id a numero entero

			);
			
			// guardo $post_for_api en array_response
			array_push($array_response,$post_for_api);
	}
	// retorno array_response
	return $array_response;

}

?>