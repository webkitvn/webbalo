<?php 
	/*
		Template name: Simple Page
	*/
?>
<?php get_header() ?>
	<?php while(have_posts()) : the_post(); ?>
		<div class="page-content simple-page">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12">
						<div>
						    <h1 class="big-title"><?php the_title()?></h1>
							<div class="content">
							    <?php the_content() ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
<?php get_footer() ?>