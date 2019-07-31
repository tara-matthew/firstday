<?php
/**
 * Single tags
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<div class="portfolio-tags post-tags clr">
	<span class="owp-tag-text"><?php echo esc_attr( 'Tags: ', 'oceanwp' ); ?></span><?php echo get_the_term_list( get_the_ID(), 'ocean_portfolio_tag', '', '<span class="owp-sep">,</span> ' ); ?>
</div>