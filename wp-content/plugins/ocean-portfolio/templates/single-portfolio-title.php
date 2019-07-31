<?php
/**
 * Displays the title
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Heading tag
$heading = get_theme_mod( 'op_portfolio_single_title_tag', 'h2' );
$heading = $heading ? $heading : 'h2';
$heading = apply_filters( 'op_portfolio_single_title_tag', $heading ); ?>

<header class="entry-header clr">
	<<?php echo esc_attr( $heading ); ?> class="single-portfolio-title entry-title"<?php oceanwp_schema_markup( 'headline' ); ?>><?php the_title(); ?></<?php echo esc_attr( $heading ); ?>>
</header><!-- .entry-header -->