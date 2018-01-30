<?php
/*
 * Template Name: home
*/
?>
<?php get_header() ?>

<div>
    <?php
    $args = array(
        'post_type' => 'project'
    );

    $projects = new WP_Query($args);
    ?>

    <?php while ($projects->have_posts()) : $projects->the_post(); ?>
        <div class="page-header">
            <div class="container">
                <h1><?php the_title(); ?></h1>
            </div>
        </div>
        <p>
        <div class="container">
            <?php the_content() ?>
        </div>
        </p>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
