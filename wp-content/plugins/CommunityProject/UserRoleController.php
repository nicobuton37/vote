<?php

namespace CommunityProject ;

class UserRoleController
{
	const STUDENT_ROLE = 'student_role' ;

	const PROJECT_ACTIVATED = false ;

	public static function isStudent()
	{
		if( ! \is_user_logged_in() )
			return false ;

		$current_user = wp_get_current_user();
		if( in_array( self::STUDENT_ROLE, $current_user->roles ) )
			return true ;

		return false ;
	}

    function set_user_role_permission()
    {
    	// TODO: https://framagit.org/Artefacts/CommunityVoice/issues/61
		$this->grant_all_project_permission( get_role( 'author' ) );
		$this->grant_all_project_permission( get_role( 'editor' ) );
		$this->grant_all_project_permission( get_role( 'administrator' ) );
    }

	function grant_all_project_permission( $role )
	{
		$role->add_cap( 'edit_projects' );
		$role->add_cap( 'delete_projects' );
		$role->add_cap( 'publish_projects' );
		$role->add_cap( 'edit_project' );
		$role->add_cap( 'read_project' );
		$role->add_cap( 'delete_project' );
		$role->add_cap( 'read_projects' );
		$role->add_cap( 'edit_others_projects' );
		$role->add_cap( 'delete_others_projects' );
		$role->add_cap( 'read_private_projects' );
		$role->add_cap( 'edit_published_projects' );
	}

	function create_student_role()
	{
		remove_role( self::STUDENT_ROLE );
		$role = add_role(
			self::STUDENT_ROLE,
			'Etudiant',
			[
				'read_project' => true,
				'edit_projects' => self::PROJECT_ACTIVATED,
				'delete_projects' => self::PROJECT_ACTIVATED,
				'publish_projects' => self::PROJECT_ACTIVATED,
				'edit_published_projects'=>self::PROJECT_ACTIVATED,
				'edit_project' => self::PROJECT_ACTIVATED,
				'delete_project' => self::PROJECT_ACTIVATED,

				'read' => true,
				//'delete_posts' => false,
				'edit_posts' => false,
				'read_private_projects' => false,
				'edit_others_projects' => false,
				'upload_files' => false,
				'level_1' => true
            ]
		);

	}
}
