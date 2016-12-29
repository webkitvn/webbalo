<?php
/**
 *
 * Template name: My Account
 * The template for displaying all of the product of Teas.
 *
 * @package maxstore
 */
?>

<?php get_header();?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <?php if(is_user_logged_in()) : ?>
                <div class="personal">
                    <?php the_content(); ?>
                </div>
            <?php else : ?>
                <?php the_content(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>