<?php

namespace CommunityProject ;

class WPUtil
{
	public static function statusToString( $status )
	{
		switch ( $status )
		{
		case 'draft': return __( 'Draft' );
		case 'pending': return __( 'Pending Review' );
		case 'private': return __( 'Private' );
		case 'publish': return __( 'Published' );
		}

		return __( $status );
	}
}
