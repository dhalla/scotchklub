<?php 

// Internal Cat-ID
define('SK_INTERNAL_CATEGORY', 15);
define('SK_BLOCKED_POST_TITLE', 'Mitglieder-Bereich');

// Enqueue Additional Scripts / CSS
add_action('wp_enqueue_scripts', 'sk_scripts_styles');

// Exclude internal categories from category pages if not logged in
add_action( 'pre_get_posts', function ($query) { if (!is_user_logged_in()) $query->set( 'cat', '-'.SK_INTERNAL_CATEGORY );});

// Block Content if is internal
add_filter('wpmem_block', 'sk_block_internal_post');

// Modify Post/Page-Content if blocked
add_filter( 'the_post', 'sk_modify_internal_post');

// Modify Pagetitle if blocked
add_filter( 'wp_title', function ($title) { return(!wpmem_block()) ? $title : SK_BLOCKED_POST_TITLE; } );

// Register additional nav menues
register_nav_menus( array(
#		'sk_member-menu' => 'SK Navigation Member',
));

// Remove Parent-Theme Features
add_action( 'after_setup_theme', function(){
	global $_wp_theme_features;
	unset( $_wp_theme_features['custom-background'] );
	unset( $_wp_theme_features['custom-header'] );
}, 20);

// Register additional sidebars
register_sidebar( array(
	'name' 			=> 	'SK Sidebar Member',
	'id' 			=>	'sk_member',
	'before_widget'	=>	'<aside id="%1$s" class="widget well %2$s">',
	'after_widget'	=>	'</aside>',
	'before_title'	=>	'<h2 class="widget-title">',
	'after_title'	=>	'</h2>',
));

// Register additional widgets
wp_register_sidebar_widget(
	'sk_public_categories',
	'SK Public Kategorien',
	'sk_public_categories',
	array(
		'description' => 'Anzeige aller öffentlichen Kategorien'
	));

wp_register_sidebar_widget(
	'sk_member_categories',
	'SK Member Kategorien',
	'sk_member_categories',
	array(
		'description' => 'Anzeige aller Kategorien, gruppiert nach externen und internen Kategorien'
	));

wp_register_sidebar_widget(
	'sk_tasting',
	'SK Tasting',
	'test',
	array(
		'description' => 'Anzeige der zuletzt verkosteten Whiskys'
	));


// ==========================================================================================


// Additional Scripts
function sk_scripts_styles() {
	wp_enqueue_script('sk-vegas-js', get_bloginfo('stylesheet_directory') . "/js/jquery.vegas.js", array('jquery'), false, true);
	wp_enqueue_script('sk-js', get_bloginfo('stylesheet_directory') . "/js/scotchklub.js", array('jquery', 'sk-vegas-js'), false, true);
	wp_enqueue_style('sk-vegas', get_bloginfo('stylesheet_directory') . "/css/jquery.vegas.css", array());
	wp_enqueue_style('sk', get_bloginfo('stylesheet_directory') . "/css/scotchklub.css", array('tw-bootstrap', 'the-bootstrap', 'sk-vegas'));
}


// Exclude internal categories from category pages if not logged in
function sk_exclude_internal_category( $query ) 
{

	if (!is_user_logged_in()) {
		$query->set( 'cat', '-'.SK_INTERNAL_CATEGORY );
	}

}


// Overwrite plugin-settings for blocked content and set blocked = true
function sk_block_internal_post( $block )
{

	// Do not block anything if user is logged in
	if (is_user_logged_in()) return false;
	
	// Pages blocked by default
	if (is_page()) return $block;
	
	// Posts are blocked if published in internal category
	if (is_single()) {
	
		$cats = get_the_category();
		$is_internal = $block;
	
		foreach ($cats as $cat) {
	
			$is_internal = (cat_is_ancestor_of(SK_INTERNAL_CATEGORY, $cat->term_id) OR ($cat->term_id == SK_INTERNAL_CATEGORY))
				? true
				: false;
			if ($is_internal) break;
	
		}
	
		return $is_internal;
		
	}

	// Set internal categories to blocked
	if (is_archive()) {
		
		$cat = get_category(get_query_var('cat'));
			$is_internal = (cat_is_ancestor_of(SK_INTERNAL_CATEGORY, $cat->term_id) OR ($cat->term_id == SK_INTERNAL_CATEGORY))
			? true
			: false;
		return $is_internal;	
	
	}
	
	return $block;
	
}


// Modify Post/Page-Content if blocked
function sk_modify_internal_post( $post ){

	if(wpmem_block() AND !is_user_logged_in()) {
		$post->post_title = SK_BLOCKED_POST_TITLE;
		$post->comment_status = 'closed';
		#$post->post_status = 'private';
	}
	
	$post->is_blocked = wpmem_block();
	
	#print_r($post);
	#if(wpmem_block() echo "BLOCKED" else echo "NOT BLOCKED";
	
};


// Public category list 
function sk_public_categories ( $args ) 
{
	
	$list = wp_list_categories( array (
		'echo' 					=> 0,
		'title_li'				=> '',
		#'show_option_all'   	=> 'Blog',
		'orderby'           	=> 'id',
		'style'             	=> 'list',
		'use_desc_for_title'	=> 1,
		#'child_of'          	=> 1,
		'exclude_tree'      	=> SK_INTERNAL_CATEGORY,
		'hierarchical'      	=> false,
	));
	
	echo "<aside id='' class='widget well widget_categories'><h2>Kategorien</h2><ul>";
	echo $list;
	echo "</ul></aside>";
	
}


// Internal category list
function sk_member_categories ( $args )
{
	$list_external = wp_list_categories( array (
		'echo' 					=> 0,
		'title_li'				=> '',
		'orderby'           	=> 'id',
		'style'             	=> 'list',
		'exclude_tree'      	=> SK_INTERNAL_CATEGORY,
		'hierarchical'      	=> false,
	));
	$list_internal = wp_list_categories( array (
		'echo' 				=> 0,
		'title_li'			=> '',
		#'show_option_all'   => 'Home',
		'orderby'           => 'id',
		'style'             => 'list',
		'exclude_tree'		=> 1,
		'hierarchical'      => false,
	));


	echo "<aside id='' class='widget well widget_categories'><h2>Kategorien</h2>";
	echo "<h3>Öffentlich</h3>";	
	echo "<ul>".$list_external."</ul>";	
	echo "<h3>Intern</h3>";
	echo "<ul>".$list_internal."</ul>";	
	echo "</aside>";

}



