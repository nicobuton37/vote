<?php

namespace CommunityProject\Vote;

use CommunityProject\UserRoleController;


class Settings
{

    public function __construct()
    {
        // add menu
        add_action('admin_menu', array($this, 'submenu_budget'), 20);
        add_action('admin_menu', array($this, 'submenu_vote'), 20);
        $budget = $this->add_custom_user_meta_budget();
    }


    /**
     * Create submenu budget
     */
    public function submenu_budget()
    {
        add_submenu_page('communityproject_settings',
            'Budget',
            'budget',
            'manage_options',
            'setting_budget',
            array($this, 'admin_budget_tpl')
        );
    }

    /**
     *Create submenu vote
     */
    public function submenu_vote()
    {
        add_submenu_page('communityproject_settings',
            'Vote',
            'vote',
            'manage_options',
            'setting_vote',
            array($this, 'admin_vote_tpl')
        );
    }


    /**
     *Include Template budget
     */
    public function admin_budget_tpl()
    {
        include 'templates/admin_budget_tpl.php';
    }

    /**
     *Include Template vote
     */
    public function admin_vote_tpl()
    {
        include 'templates/admin_budget_tpl.php';
    }


    /**
     *Attribution budget for all users and create usermeta student_budget in wp_usermeta table
     */
    public function add_custom_user_meta_budget()
    {

        $args = array(
            'student_role' => UserRoleController::STUDENT_ROLE,
        );

        $students = get_users($args);
        if (isset($_POST['student_budget'])) {
            foreach ($students as $student) {
                $student_id = $student->ID;
                add_user_meta($student_id, 'student_budget', isset($_POST['student_budget']));
            }
        }
    }
}
