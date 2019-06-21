<?php
/*
*** PVS Regular Events 1.0 ***
Template Name: PVS Regular Events
*/
?>

<?php 
	get_header();
?>

<main id="maincontent" role="main">

	<header>
		<?php pvs_day_menu_hook(); ?>
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
	</header>

	<div id="pvs-event__listing">


		<?php $custom_terms = get_terms('day-of-the-week');

			$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
			foreach( $days as $day ) {
				wp_reset_query();
				$args = array(
					'order' => 'ASC',
					'orderby' => 'term_order',
					'tax_query' => array(
						array(
							'taxonomy' => 'day_of_the_week',
							'field' => 'slug',
							'terms' => $day,
						),
					),
				);

			$pvs_events = new WP_Query($args);
			if( $pvs_events->have_posts() ) :
				echo '<p class="day-of-the-week">'.ucfirst($day).'</p>';
				while( $pvs_events->have_posts() ) : $pvs_events->the_post();
		?>

	<div class="pvs-event">

		<h2 class="pvs-event__title"><?php the_title(); ?></h2>
		
		<div class="pvs-event__fields">
			<h3>Time</h3>
			<p><?php the_field('time'); ?></p>
			
			<h3>Day</h3>
			<p><?php the_field('day'); ?></p>
			
			<?php if( get_field('where') ): ?>
				<h3>Location</h3>
				<p><?php the_field('where'); ?></p>
			<?php endif; ?>
			
			<?php if( get_field('contact-name') ): ?>
				<h3>Contact name</h3>
				<p><?php the_field('contact-name'); ?></p>
			<?php endif; ?>

			<?php if( get_field('contact-number') ): ?>
				<h3>Contact number</h3>
				<p><?php the_field('contact-number'); ?></p>
			<?php endif; ?>
		</div><!--.pvs-event__fields-->
		
		<div class="pvs-event__info">
			<?php if( get_field('description') ): ?>
				<h3>Description</h3>
				<?php the_field('description'); ?>
			<?php endif; ?>
		</div><!--.pvs-event__info-->

	</div><!--.pvs-event-->

	<?php endwhile;  //else :?>
	<?php endif; ?>
	<?php } ?>
	</div><!--#custom-posts-list-->

</main>

<?php get_footer(); ?>
