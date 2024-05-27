function custom_at_biz_dir_setup() {
    // Register custom post type
    $labels = array(
				'menu_name'                => __( 'Directory Listings', 'directorist' ),
				'name_admin_bar'           => __( 'Listing', 'directorist' ),
				'name'                     => _x( 'Listings', 'post type general name', 'directorist' ),
				'singular_name'            => _x( 'Listing', 'post type singular name', 'directorist' ),
				'add_new'                  => _x( 'Add New', 'listing', 'directorist' ),
				'add_new_item'             => __( 'Add New Listing', 'directorist' ),
				'edit_item'                => __( 'Edit Listing', 'directorist' ),
				'update_item'              => __( 'Update Listing', 'directorist' ),
				'new_item'                 => __( 'New Listing', 'directorist' ),
				'view_item'                => __( 'View Listing', 'directorist' ),
				'view_items'               => __( 'View Listings', 'directorist' ),
				'search_items'             => __( 'Search Listings', 'directorist' ),
				'not_found'                => __( 'No listings found.', 'directorist' ),
				'not_found_in_trash'       => __( 'No listings found in Trash.', 'directorist' ),
				'all_items'                => __( 'All Listings', 'directorist' ),
				'archives'                 => __( 'Listing Archives', 'directorist' ),
				'attributes'               => __( 'Listing Attributes', 'directorist' ),
				'insert_into_item'         => __( 'Insert into listing', 'directorist' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this listing', 'directorist' ),
				'featured_image'           => _x( 'Featured image', 'listing', 'directorist' ),
				'set_featured_image'       => _x( 'Set featured image', 'listing', 'directorist' ),
				'remove_featured_image'    => _x( 'Remove featured image', 'listing', 'directorist' ),
				'use_featured_image'       => _x( 'Use as featured image', 'listing', 'directorist' ),
				'filter_items_list'        => __( 'Filter listings list', 'directorist' ),
				'items_list_navigation'    => __( 'Listings list navigation', 'directorist' ),
				'items_list'               => __( 'Listings list', 'directorist' ),
				'item_published'           => __( 'Listing published.', 'directorist' ),
				'item_published_privately' => __( 'Listing published privately.', 'directorist' ),
				'item_reverted_to_draft'   => __( 'Listing reverted to draft.', 'directorist' ),
				'item_trashed'             => __( 'Listing trashed.', 'directorist' ),
				'item_scheduled'           => __( 'Listing scheduled.', 'directorist' ),
				'item_updated'             => __( 'Listing updated.', 'directorist' ),
				'item_link'                => _x( 'Listing Link', 'navigation link block title', 'directorist' ),
				'item_link_description'    => _x( 'A link to a listing.', 'navigation link block description', 'directorist' ),
			);
	//$default_labels = get_post_type_labels(get_post_type_object('post'));


    $args = array(
				'label'               => __( 'Directory Listing', 'directorist' ),
				'description'         => __( 'Directory listings', 'directorist' ),
		         'rewrite' => array(
            'slug' => '%directory-type%/%at_biz_dir-category%/%at_biz_dir-location%',
            'with_front' => false
        ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author' ),
				// 'show_in_rest'         => true,
				'taxonomies'          => array( ATBDP_CATEGORY, ATBDP_LOCATION, ATBDP_TAGS,  ATBDP_DIRECTORY_TYPE ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => current_user_can( 'edit_others_at_biz_dirs' ) ? true : false, // show the menu only to the admin
				'show_in_menu'        => true,
				'menu_position'       => 20,
				'menu_icon'           => DIRECTORIST_ASSETS . 'images/menu_icon.png',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => ATBDP_POST_TYPE,
				'map_meta_cap'        => true, // set this true, otherwise, even admin will not be able to edit this post. WordPress will map cap from edit_post to edit_at_biz_dir etc,
				'menu_position'       => 5,
			);
	 

    register_post_type('at_biz_dir', $args);

    // Register taxonomies
    register_taxonomy('at_biz_dir-category', 'at_biz_dir', array(
        'label' => 'Categories',
        'rewrite' => array('slug' => 'at_biz_dir-category'),
        'hierarchical' => true,
    ));

    register_taxonomy('at_biz_dir-location', 'at_biz_dir', array(
        'label' => 'Locations',
        'rewrite' => array('slug' => 'at_biz_dir-location'),
        'hierarchical' => true,
    ));

    register_taxonomy('directory-type', 'at_biz_dir', array(
        'label' => 'Directory Types',
        'rewrite' => array('slug' => 'directory-type'),
        'hierarchical' => true,
		'show_ui' => false, // Prevents the taxonomy from appearing in the admin menu
        'show_in_menu' => false,
    ));

    // Register rewrite tags and rules
    add_rewrite_tag('%at_biz_dir-category%', '([^&]+)');
    add_rewrite_tag('%at_biz_dir-location%', '([^&]+)');
	add_rewrite_tag('%directory-type%', '([^&]+)');
  //  add_rewrite_tag('%directory-type%', '([^&]+)');
    add_rewrite_rule(
        'at_biz_dir/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?post_type=at_biz_dir&at_biz_dir-category=$matches[1]&at_biz_dir-location=$matches[2]&directory-type=$matches[3]&name=$matches[4]',
        'top'
    );
}

add_action('init', 'custom_at_biz_dir_setup');

function filter_at_biz_dir_post_type_link($post_link, $post) {
    if ($post->post_type == 'at_biz_dir') {
        // Get the category term
        $terms = wp_get_post_terms($post->ID, 'at_biz_dir-category');
        if (!is_wp_error($terms) && !empty($terms)) {
            $category = $terms[0]->slug;
        } else {
            $category = 'uncategorized';
        }

        // Get the location term
        $terms = wp_get_post_terms($post->ID, 'at_biz_dir-location');
        if (!is_wp_error($terms) && !empty($terms)) {
            $location = $terms[0]->slug;
        } else {
            $location = 'no-location';
        }

        // Get the directory type term
        $terms = wp_get_post_terms($post->ID, 'directory-type');
        if (!is_wp_error($terms) && !empty($terms)) {
            $directory_type = $terms[0]->slug;
        } else {
            $directory_type = 'general';
        }

        $post_link = str_replace('%at_biz_dir-category%', $category, $post_link);
        $post_link = str_replace('%at_biz_dir-location%', $location, $post_link);
        $post_link = str_replace('%directory-type%', $directory_type, $post_link);
    }
    return $post_link;
}

add_filter('post_type_link', 'filter_at_biz_dir_post_type_link', 10, 2);