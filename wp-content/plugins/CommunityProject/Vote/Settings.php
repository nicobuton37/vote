<?php

namespace CommunityProject\Vote;

use CommunityProject\UserRoleController;


class Settings
{
    const STUDENT_BUDGET = 'student_budget';
    public function __construct()
    {
        // add menu
        add_action('admin_menu', array($this, 'submenu_budget'), 20);
        add_action('admin_menu', array($this, 'submenu_vote'), 20);
        add_action('user_register', array($this, 'attribute_budget_for_new_user'));
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
                update_user_meta($student_id, 'student_budget', $_POST['student_budget']);
            }
        }

    }

    public function attribute_budget_for_new_user($user_id)
    {
        $args = array(
            'role' => 'administrator'
        );

        $admins = get_users($args);
        foreach ($admins as $admin) {
            $budget_admin = get_user_meta($admin->ID, self::STUDENT_BUDGET);
            for ($i = 0; $i <= count($budget_admin); $i++) {
                $the_budget = $budget_admin[$i];
            }

        }
        var_dump($the_budget);
        die();
//        update_user_meta($user_id, self::STUDENT_BUDGET, $the_budget);

    }

}
