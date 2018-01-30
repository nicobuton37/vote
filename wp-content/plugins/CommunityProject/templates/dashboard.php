<?php
/**
 * Available variables :
 * - $isStudent : is the user has Student role
 * - $projects : the array of WP_Post objects

    WP_Post Object
        (
            [ID] => 1424
            [post_author] => 3
            [post_date] => 2017-09-18 15:18:10
            [post_date_gmt] => 2017-09-18 13:18:10
            [post_content] => 
            [post_title] => Mon Projet
            [post_excerpt] => 
            [post_status] => publish
            [comment_status] => closed
            [ping_status] => closed
            [post_password] => 
            [post_name] => mon-projet
            [to_ping] => 
            [pinged] => 
            [post_modified] => 2017-09-18 15:18:10
            [post_modified_gmt] => 2017-09-18 13:18:10
            [post_content_filtered] => 
            [post_parent] => 0
            [guid] => http://univ-tours-bpe.localhost/?post_type=project&#038;p=1424
            [menu_order] => 0
            [post_type] => project
            [post_mime_type] => 
            [comment_count] => 0
            [filter] => raw
        )

 */

if( count($projects) == 0 )
{
	if( $isStudent )
	{
	?>
	<p>Vous n'avez aucun projet.</p>
	<?php
	}
	else
	{
	?>
	<p>Aucun projet.</p>
	<?php
	}
}
else
{
	?>
	<div id="published-posts" class="activity-block">
	<ul>
	<?php
	foreach( $projects as $project )
	{
	?>
		<li>
			<span><?php echo get_post_time(get_option('time_format'), false, $project, true);?></span>
			<?php edit_post_link( $project->post_title, '', '', $project->ID, '' ); ?>
			- <i><?php echo \CommunityProject\WPUtil::statusToString( $project->post_status );?></i>
		</li>
	<?php
	}
	?>
	</ul>
	</div>
	<?php
}
