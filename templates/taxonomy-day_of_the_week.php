<?php
/*
*** PVS Regular Events 1.0 ***
*/
?>

<?php 
	get_header();
?>

<main id="maincontent" role="main">

	<header id="days_menu">
		<?php pvs_day_menu_hook(); ?>
		<h1 class="entry-title day-of-the-week">
			<?php 
				$tax = get_queried_object();
				echo $tax->name;
   			?> 
		</h1>
	</header>

	<div id="pvs-event__listing">

		<?php
			
			if ( have_posts () ) : while( have_posts() ) : the_post();
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
	<?php ?>
	</div><!--#custom-posts-list-->

</main>

<?php get_footer(); ?>
