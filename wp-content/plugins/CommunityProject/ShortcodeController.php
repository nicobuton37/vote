<?php

namespace CommunityProject ;

class ShortcodeController
{
    function __construct()
    {
        add_shortcode( 'project_nbr', array( $this, 'get_project_nbr' ) );
        add_shortcode( 'add_project_link', array( $this, 'add_project_link' ) );

        add_shortcode( 'display_project_nbr', array( $this, 'display_project_nbr' ) );
        add_shortcode( 'display_add_project', array( $this, 'display_add_project' ) );

        add_shortcode( 'display_proposed_project_text', array( $this, 'display_proposed_project_text' ) );
    }

    function add_project_link()
    {
        return admin_url( 'post-new.php?post_type=project' );
    }

    function get_project_nbr()
    {
        return wp_count_posts( 'project' )->publish;
    }

    function display_add_project( $atts )
    {
        $a = shortcode_atts( array(
            'content' => 'Je propose un projet',
        ), $atts );

        ob_start();
        include 'templates/add_project.php';
        return ob_get_clean();
    }

    /**
     * Return published project number
     * @return [type] [description]
     */
    function display_project_nbr( $atts )
    {
        $projectNbr = $this->get_project_nbr();
        $a = shortcode_atts( array(
            'singular' => "projet déposé",
            'plurial' => "projets déposés",
        ), $atts );
        $content = $projectNbr > 1 ? $a['plurial'] : $a['singular'];

        ob_start();
        include 'templates/project_number.php' ;
        return ob_get_clean();
    }

    function display_proposed_project_text( $atts )
    {
        $projectNbr = $this->get_project_nbr();
        $a = shortcode_atts( array(
            'singular' => 'projet déposé',
            'plurial' => 'projets déposés',
        ), $atts );
        return $projectNbr > 1 ? $a['plurial'] : $a['singular'];
    }

}
