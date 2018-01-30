<?php

namespace CommunityProject ;

class ProjectCustomTypeCreator
{
	const CPT = 'project' ;

	public function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'custom_field_meta_box' ) );
		
		add_filter( 'wp_insert_post_data', array( $this, 'default_comments_on') );

		// https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns
		add_filter('manage_project_posts_columns', array( $this, 'manage_project_posts_columns'));

		// https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
		add_action('manage_project_posts_custom_column', array( $this, 'manage_project_posts_custom_column'), 1, 2);

	}

	/**
	 * Force permits comments on CPT 'project' post. 
	 * https://framagit.org/Artefacts/bpe-univ-tours/issues/42
	 * 
	 * @param unknown $data
	 * @return string
	 */
	function default_comments_on( $data )
	{
		if( $data['post_type'] == self::CPT )
		{
			if( $data['post_status'] == 'publish' )
				$data['comment_status'] = 'open';
		}
		return $data;
	}

	/**
	 * Add custom field to project custom type
	 */
	function custom_field_meta_box()
	{
		add_meta_box(
			'budget',
			'Budget',
			array( $this, 'custom_field_display' ),
			'project',
			'normal',
			'default',
			[
				'id'  => 'budget',
				'label' => 'Montant €',
				'post_id' => get_post()->ID
			]
		);
	}

	/**
	 * Display customs fields in "add project" page
	 * @param   $post
	 * @param   $metabox
	 */
	function custom_field_display( $post, $metabox )
	{
		ob_start();
		include __DIR__.'/templates/custom_field.php';
		echo ob_get_clean();
	}

	function manage_project_posts_columns($defaults)
	{
		//$defaults['categories'] = 'Categorie';
		//error_log( __METHOD__.' defaults: '. print_r($defaults,true) );
		
		return array_merge( $defaults,
			array(
				'comproj_categories' => __( 'Categories' ),
				'comproj_budget' => __( 'Budget' ),
			)
		);

	}
	
	function manage_project_posts_custom_column($column_name, $post_ID)
	{
		//error_log( __METHOD__.' column_name: '. $column_name);
		
		switch($column_name )
		{
			case 'comproj_categories' :
				echo the_terms($post_ID, 'category_projects');
				break;
			case 'comproj_budget' :
				echo get_post_meta( $post_ID, 'budget', true );
				break;
		}
	}

	function create_project_post_type()
	{
        register_post_type( self::CPT,
			[
				'labels' => [
					'name' => __('Projets'),
					'singular_name' => __('Projet'),
                    'all_items' => 'Tous les projets',
                    'add_new_item' => 'Ajouter un projet',
                    'edit_item' => 'Éditer le projet',
                    'new_item' => 'Nouveau projet',
                    'view_item' => 'Voir le projet',
                    'search_items' => 'Rechercher parmi les projets',
                    'not_found' => 'Pas de projet trouvé',
                    'not_found_in_trash'=> 'Pas de projet dans la corbeille'
				],
                'capability_type' => 'post',
				'map_meta_cap'=>true,
                'capabilities' => [
    				'publish_posts' => 'publish_projects',
    				'edit_posts' => 'edit_projects',
    				'edit_others_posts' => 'edit_others_projects',
    				'delete_posts' => 'delete_projects',
    				'delete_others_posts' => 'delete_others_projects',
    				'read_private_posts' => 'read_private_projects',
    				'edit_post' => 'edit_project',
    				'delete_post' => 'delete_project',
    				'read_post' => 'read_project',
              'edit_published_posts' => 'edit_published_projects',
    			],
                'supports' => [
                    'title',
                    'editor',
                    'author',
                    'excerpt',
                    'comments',
                    'thumbnail'
                ],
                'public' => true,
                'has_archive' => true
		  	] );
    }

    function create_status_taxonomy()
    {
    	register_taxonomy( CommunityProject::TERMS_STATUS, CommunityProject::CPT_TYPE, [
			'hierarchical' => true,
			'labels' => [
                'name' => 'Statuts',
                'singular_name' => 'Statut',
                'all_items' => 'Tous les statuts',
                'edit_item' => 'Éditer le statut',
                'view_item' => 'Voir le statut',
                'update_item' => 'Mettre à jour le statut',
                'add_new_item' => 'Ajouter un statut',
                'new_item_name' => 'Nouveau statut',
                'search_items' => 'Rechercher parmi les statuts',
                'popular_items' => 'Statuts les plus utilisés'
            ],
			'query_var' => true,
			'rewrite' => true]
		);

        register_taxonomy_for_object_type( CommunityProject::TERMS_STATUS, CommunityProject::CPT_TYPE );

        register_taxonomy( 'category_projects', CommunityProject::CPT_TYPE, array(
			'labels' => [
                'name' => 'Catégories des projets',
                'singular_name' => 'Catégorie du projet',
                'all_items' => 'Toutes',
                'edit_item' => 'Éditer la catégorie',
                'view_item' => 'Voir la catégorie',
                'update_item' => 'Mettre à jour la catégorie',
                'add_new_item' => 'Ajouter une catégorie',
                'new_item_name' => 'Nouvelle catégorie',
                'search_items' => 'Rechercher parmi les catégories',
                'popular_items' => 'Catégories les plus utilisées'
            ],
			'query_var' => true,
			'rewrite' => true,
            'hierarchical' => true,
            'public' => true,
            'capabilities' => array(
                'manage_terms'=> 'manage_categories',
                'edit_terms'=> 'manage_categories',
                'delete_terms'=> 'manage_categories',
                'assign_terms' => 'read'
            ),
        ));

        register_taxonomy_for_object_type( 'category_projects', CommunityProject::CPT_TYPE );
    }

    function create_default_status_terms()
    {
        $terms = [
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'Déposé', 'description' => 'Projet déposé', 'slug' => 'depose' ],
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'Étude de la faisabilité', 'description' => 'Étude de la faisabilité', 'slug' => 'etude' ],
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'En cours de vote', 'description' => 'Projet en cours de vote', 'slug' => 'vote' ],
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'Validé', 'description' => 'Mise en œuvre du projet', 'slug' => 'valide' ],
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'Annulé', 'description' => 'Projet annulé', 'slug' => 'annule' ],
            ['taxonomy' => CommunityProject::TERMS_STATUS, 'name' => 'Refusé', 'description' => 'Projet refusé', 'slug' => 'refuse' ],

/*
            ["taxonomy" => "category_projects", "name" => "Sport", "description" => "Sport", "slug" => "sport" ],
            ["taxonomy" => "category_projects", "name" => "Musique", "description" => "Musique", "slug" => "musique" ],
            ["taxonomy" => "category_projects", "name" => "Nourriture", "description" => "Nourriture", "slug" => "nourriture" ],
            ["taxonomy" => "category_projects", "name" => "Santé", "description" => "Santé", "slug" => "sante" ],
            ["taxonomy" => "category_projects", "name" => "Loisir", "description" => "Loisir", "slug" => "loisir" ],
            ["taxonomy" => "category_projects", "name" => "Divers", "description" => "Divers", "slug" => "divers" ],
*/
        ];

        foreach( $terms as $term )
        {
            wp_insert_term(
                $term['name'],
                $term['taxonomy'],
                [ 'description'=> $term['description'], 'slug' => $term['slug']]
            );
        }
    }

}
