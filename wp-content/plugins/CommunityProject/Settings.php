<?php

namespace CommunityProject ;
use CommunityProject\Vote\Vote;


/**
 * Class define CommunitVoice settings.
 * Settings are save in wp_options table, with key : "CommunityVoice_settings"
 * Settings format : setting1:value1;setting2:value2;
 */
class Settings
{
    const SETTING_NAME = 'CommunityProject_settings';

    private $settings;
    private $settingsList;

    function __construct()
    {
        $this->settingsList = [
            //['id' => self::CPT, 'label' => 'Custom post type only : ', 'type' => 'text', 'description'=>'Vote actif seulement pour les articles de ce type.'],
        ];

        if( get_option( self::SETTING_NAME, '' ) !== '' )
        {
            $this->settings = get_option( self::SETTING_NAME, '' );
            $this->settings = explode( ";", $this->settings );
        }
        else
        {
            $this->settings = [];
            add_option( self::SETTING_NAME, '' );
        }
        add_action( 'admin_menu', array( $this, 'wp_admin_menu_setting_page' ) );

        add_action( 'admin_post_save_settings', array( $this, 'wp_admin_post_save_settings' ) );
    }

    function wp_admin_menu_setting_page()
    {
    	if ( current_user_can( 'administrator' ) )
    	{
            new Vote();
    		add_menu_page('CommunityProject', 'CommunityProject', 'administrator', 'communityproject_settings', array( $this, 'menu_page_settings' ) );
    	}
    }

    function menu_page_settings()
    {
    	ob_start();
    	include 'templates/settings.php';
    	echo ob_get_clean();
    }

    function wp_admin_post_save_settings()
    {
        if( isset( $_POST ) )
        {
            foreach( $this->settingsList as $oldSetting )
            {
                if( ! isset( $_POST[$oldSetting['id']] ) )
                {
                    $this->saveSetting( $oldSetting['id'], '' );
                    continue;
                }

                foreach( $_POST as $id => $setting )
                {
                    if( $id === $oldSetting['id'] )
                    {
                        $this->saveSetting( $id, $setting );
                    }
                }
            }
        }
        wp_redirect( admin_url( 'admin.php?page=cv_settings' ) );
    }

    /**
     * Return value of setting
     * @param  String $name : setting key value
     * @return mixed
     */
    function getSetting( $name )
    {
        return isset( $this->getArraySettings()[$name] ) ? $this->getArraySettings()[$name] : false;
    }

    /**
     * Return array of all Community Voice setting
     * @return array
     */
    function getArraySettings()
    {
        $arraySettings = [];

        foreach( $this->settings as $setting )
        {
            //list( $key, $value ) = explode( ":", $setting );
                $parts = explode( ':', $setting );
                $key = $parts[0] ;
                $value = isset( $parts[1] ) ? $parts[1] : null;

            if( $value === 'true' )
            {
                $value = true;
            }
            else if( $value === 'false' )
            {
                $value = false;
            }
            else if( is_numeric( $value ) )
            {
                settype( $value, 'float' );
            }

            $arraySettings[$key] = $value;
        }

        return $arraySettings;
    }

    /**
     * Save a setting in database
     * @param  String $key   : Setting key
     * @param  mixed  $value : Setting value
     */
    function saveSetting( $key, $value )
    {
        $settingsToSave = '';
        $arraySettings = $this->getArraySettings();
        $arraySettings[$key] = $value;

        foreach( $arraySettings as $key => $value )
        {
            $settingsToSave .= $key . ":" . $value . ";";
        }

        $this->settings = $this->settings = explode( ";", $settingsToSave );

        update_option( self::SETTING_NAME, $settingsToSave );
    }
}
