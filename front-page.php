<?php
/**
 * The template for displaying the Code Reference landing page.
 *
 * Template Name: Reference
 *
 * @package WPCodeReference
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content-area">
			<?php breadcrumb_trail(); ?>
		</div>

		<main id="main" class="site-main" role="main">
			<?php do_action( 'wp_code_reference_front_page_before' ); ?>
			<div class="reference-landing">
				<div class="search-guide section clear">
					<?php
					// Fake that this is 404 page to fool search form template.
					global $wp_query;
					$before_faking    = $wp_query->is_404;
					$wp_query->is_404 = true;

					get_search_form();

					$wp_query->is_404 = $before_faking;
					?>
				</div><!-- /search-guide -->

				<div class="topic-guide section">
					<ul class="unordered-list horizontal-list no-bullets">
						<?php
						foreach ( \DevHub\get_parsed_post_types( 'labels' ) as $type => $label ) {
							$nums = wp_count_posts( $type );
							if ( isset( $nums->publish ) && 0 < $nums->publish ) :
								?>
								<li><a href="<?php echo esc_url( get_post_type_archive_link( $type ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
								<?php
							endif;
						}
						?>
					</ul>
				</div><!-- /topic-guide -->
			</div><!-- /reference-landing -->
			<?php do_action( 'wp_code_reference_front_page_after' ); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
