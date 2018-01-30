<?php

/**
 * Plugin Name: Community Project
 * Description: Community Project
 * Version: 0.0.1
 * Author: Cyrille GIQUELLO, Romain DUMINIL
 */

namespace CommunityProject ;

require_once __DIR__. '/WPUtil.php';
require_once __DIR__. '/Settings.php';
require_once __DIR__. '/CommunityProject_install.php';
require_once __DIR__. '/UserRoleController.php';
require_once __DIR__. '/ProjectCustomTypeCreator.php';
require_once __DIR__. '/ShortcodeController.php';
require_once __DIR__. '/DashboardWidget.php';
require_once __DIR__. '/Vote/Vote.php';
//require_once ABSPATH . '/wp-content/plugins/CommunityVoice/Settings.php';

class CommunityProject
{
	const CPT_TYPE = 'project';
	const TERMS_STATUS = 'status' ;

    const CP_DEBUG_MODE = false;

    protected $settings ;

    function __construct()
    {
    	$installer = new CommunityProject_install() ;
    	register_activation_hook( __FILE__, array( $installer, 'activate' ) );
    	register_deactivation_hook( __FILE__, array( $installer, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'CommunityProject_install', 'uninstall' ) );

        $this->settings = new Settings();

        $projectCustomTypeCreator = new ProjectCustomTypeCreator() ;
        add_action( 'init', array( $projectCustomTypeCreator, 'create_project_post_type' ) );
        add_action( 'init', array( $projectCustomTypeCreator, 'create_status_taxonomy' ) );
        add_action( 'init', array( $projectCustomTypeCreator, 'create_default_status_terms' ) );

        $userRoleController = new UserRoleController();
        add_action( 'init', array( $userRoleController, 'create_student_role' ) );
        add_action( 'init', array( $userRoleController, 'set_user_role_permission' ) );

        add_action( 'after_setup_theme', array( $this, 'remove_admin_bar') );

        if( is_admin() )
        {
        	// defined( 'DOING_AJAX' ) && DOING_AJAX

        	//add_action( 'admin_menu', array( $this, 'redirect_if_not_author' ) );
        	add_action( 'init', array( $this, 'disable_wpseo' ) );
        	add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );

        	add_action( 'do_meta_boxes', array( $this, 'remove_edit_flow_metabox' ) );
        	//add_action( 'do_meta_boxes', array( $this, 'remove_edit_flow_metabox' ) );
        	//add_action( 'add_meta_boxes', array( $this, 'remove_edit_flow_metabox' ), 100000 );


        	add_filter('screen_options_show_screen', array( $this, 'remove_screen_options_tab' ) );

        	add_action( 'save_post', array( $this, 'set_default_status' ) );
        	add_action( 'save_post', array( $this, 'save_custom_meta_box' ), 10, 3 );

        	add_action( 'wp_dashboard_setup', array( new DashboardWidget(), 'wp_dashboard_setup' ) );

       		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
        }

        new ShortcodeController();
    }
    
    function disable_wpseo()
    {
    	if( ! UserRoleController::isStudent() )
    		return ;

    	add_action( 'wp_print_scripts', array( $this, 'disable_wpseo_dequeue_script'), 10000 );
    	add_filter( 'manage_edit-project_columns', array( $this, 'disable_wpseo_remove_columns') );
    	add_action( 'do_meta_boxes', array( $this, 'disable_wpseo_remove_metabox' ) );
    /*
    	if( UserRoleController::isStudent() )
    	{
    		// Remove page analysis columns from post lists, also SEO status on post editor
	    	add_filter('wpseo_use_page_analysis', '__return_false');
    	}*/
    }

    function disable_wpseo_dequeue_script()
    {
    	// To remove javascript error that happen if metabox removed.
    	// wp-seo-post-scraper-542.min.js?ver=5.4.2:38 Uncaught Error: The snippet preview requires a valid target element

    	wp_dequeue_script( 'yoast-seo-post-scraper' );
    	wp_dequeue_script( 'yoast-seo-metabox' );
    	
    }

    function disable_wpseo_remove_columns( $columns )
    {
		// remove the Yoast SEO columns
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-links'] );
		unset( $columns['wpseo-linked'] );
		unset( $columns['wpseo-score-readability'] );
			
    	return $columns;
    }

    function disable_wpseo_remove_metabox()
    {
    	remove_meta_box( 'wpseo_meta','project','normal' );
    }

    function wp_admin_enqueue_scripts()
    {
    	// remove wp logo submenu
    	wp_register_script( 'hide_wp_submenu', plugin_dir_url( __FILE__ ) . '/js/hide_wp_submenu.js', array( 'jquery' ) );
    	wp_enqueue_script( 'hide_wp_submenu' );
    }

    /**
     * A student cannot edit other's projects.
     */
    function redirect_if_not_author()
    {
        if ( isset( $_GET['post'] ) && $_GET['action'] === 'edit' && get_post( $_GET['post'] )->post_type === self::CPT_TYPE )
        {
            if( ! current_user_can( 'publish_posts' ) && get_post($_GET['post'])->post_author != get_current_user_id() )
            {
                wp_redirect( admin_url() );
            }
        }
    }

    function remove_admin_bar()
    {
        if ( current_user_can( 'student_role' ) && ! is_admin() )
        {
            show_admin_bar( false );
        }
    }

    function remove_screen_options_tab()
    {
        return current_user_can( 'publish_posts' ); // editor
    }

    /**
     * Set default status to project post type
     * @param $post_id
     */
    function set_default_status( $post_id )
    {
        $post = get_post( $post_id );
        if ( $post->post_type === self::CPT_TYPE )
        {
        	$terms = wp_get_post_terms( $post_id, self::TERMS_STATUS );
            if ( empty( $terms ) )
            {
                wp_set_object_terms( $post_id, 'depose', self::TERMS_STATUS );
            }
        }
    }

    /**
     * Remove some default boxes in "add project" page
     */
    function remove_meta_boxes()
    {

    	if( UserRoleController::isStudent() || ! current_user_can( 'publish_posts' ) )
    	{
    		//remove_meta_box( 'wpseo-dashboard-overview','project','normal' );
    		remove_meta_box( 'commentstatusdiv','project', 'normal' );
    		remove_meta_box( 'commentsdiv','project','normal' );
    		remove_meta_box( 'statusdiv','project','normal' );
    		
    		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
    		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
    		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8

    		remove_meta_box( 'themeisle', 'dashboard', 'normal');//since 3.8    		
    		remove_meta_box( 'logincust_subscribe_widget', 'dashboard', 'normal');//since 3.8

    	}
    }

    function remove_edit_flow_metabox()
    {
    	if( UserRoleController::isStudent() || ! current_user_can( 'publish_posts' ) )
    	{
    		
    		remove_meta_box( 'commentstatusdiv','project', 'normal' );
    		remove_meta_box( 'commentsdiv','project','normal' );
    		remove_meta_box( 'statusdiv','project','normal' );
    		remove_meta_box( 'authordiv','project','normal' );
    		remove_meta_box( 'slugdiv','project','normal' );
    		remove_meta_box( 'tagsdiv-post_tag','project','normal' );
    	}

    	if( ! current_user_can( 'publish_posts' ) && get_post()->post_author != get_current_user_id() )
        {
            remove_meta_box( 'edit-flow-editorial-comments','project','normal' );
        }
    }

    function save_custom_meta_box( $post_id, $post, $update )
    {
        if( $post->post_type !== self::CPT_TYPE )
            return;

        if( $update === true )
        {
            if( isset( $_POST['budget'] ) )
            {
                add_post_meta( $post_id, 'budget', $_POST['budget'] );
            }
        }
        else
        {
            if( isset( $_POST['budget'] ) )
            {
                update_post_meta( $post_id, 'budget', $_POST['budget'] );
            }
        }
    }

    /**
     * Return all projects for no student user.
     * Return only his/her projects for student user.
     * 
     * @return WP_Post[]
     */
    public static function get_projects()
    {
    	if( UserRoleController::isStudent() )
    	{
    		$current_user = wp_get_current_user();
    		$author = $current_user->ID ;
    	}
    	else
    	{
			$author = null ;
    	}

    	return get_posts( array(
    		'author' =>  $author,
    		'post_type' => ProjectCustomTypeCreator::CPT,
    		'numberposts' => 100,
    		'orderby' => 'date',
    		'order' => 'DESC',
    		'post_status' => 'any',
    		//'category' => 0,
    		//'include' => array(),
    		//'exclude' => array(),
    		//'meta_key' => '',
    		//'meta_value' =>'',
    		//'suppress_filters' => true
    	));
    }

}

new CommunityProject();
