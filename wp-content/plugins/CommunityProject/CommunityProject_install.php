<?php

namespace CommunityProject ;

class CommunityProject_install
{
    function install()
    {
        add_option( "community_project_installed", "1" );
    }

    function activate()
    {
        if ( ! $this->isInstall() )
		{
			$this->install();
		}
    }

    function deactivate()
    {

    }

    function uninstall()
    {
        // remove project post type
        // remove status taxonomy
        // remove default status terms
        // remove role
    }

    function isInstall()
	{
		return strlen( get_option( "community_project_installed" ) ) > 0;
	}
}
