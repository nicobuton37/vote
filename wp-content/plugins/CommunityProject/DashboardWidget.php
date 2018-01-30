<?php
/**
 * DashboardWidget.php
 */

namespace CommunityProject ;

class DashboardWidget
{
	const SLUG = 'communityproject-widget' ;

	public function __construct()
	{
	}

	public function wp_dashboard_setup()
	{
		if( UserRoleController::isStudent() )
		{
			$title = 'Mes projets' ;
		}
		else
		{
			$title = 'Les projets' ;
		}

		wp_add_dashboard_widget(
			self::SLUG, // Widget slug.
			$title, // Title.
			array( $this, 'display_widget' ) // Display function.
		);
	}

	public function display_widget()
	{
		/**
		 * @var WP_Post[] $projects Liste des projets, variable disponible dans le template.
		 */
		$projects = CommunityProject::get_projects();

		$isStudent = UserRoleController::isStudent();

		ob_start();
		include __DIR__.'/templates/dashboard.php';
		echo ob_get_clean();
	}

}
