<?php
/**
 * Popup template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Return if logged in
if ( is_user_logged_in() ) {
	return;
}

// Classes
$classes = array( 'opl-popup-block' );

// Background image
if ( '' != get_theme_mod( 'opl_popup_login_style_bg' ) ) {
	$classes[] = 'has-background-image';
}

// Turn classes into space seperated string
$classes = implode( ' ', $classes );

// If registration is enabled
$can_register = get_option( 'users_can_register' ); 

// Vars
$login_title   			= oceanwp_tm_translation( 'opl_popup_login_text_title', get_theme_mod( 'opl_popup_login_text_title' ) );
$login_title 			= $login_title ? $login_title : esc_html__( 'Log in', 'ocean-popup-login' );
$login_content 			= oceanwp_tm_translation( 'opl_popup_login_text_content', get_theme_mod( 'opl_popup_login_text_content' ) );
$login_content 			= $login_content ? $login_content : esc_html__( 'Become a part of our community!', 'ocean-popup-login' );
$register_title   		= oceanwp_tm_translation( 'opl_popup_register_text_title', get_theme_mod( 'opl_popup_register_text_title' ) );
$register_title 		= $register_title ? $register_title : esc_html__( 'Create an account', 'ocean-popup-login' );
$register_content 		= oceanwp_tm_translation( 'opl_popup_register_text_content', get_theme_mod( 'opl_popup_register_text_content' ) );
$register_content 		= $register_content ? $register_content : esc_html__( 'Welcome! Register for an account', 'ocean-popup-login' );
$lost_password_title   	= oceanwp_tm_translation( 'opl_popup_lost_password_text_title', get_theme_mod( 'opl_popup_lost_password_text_title' ) );
$lost_password_title 	= $lost_password_title ? $lost_password_title : esc_html__( 'Reset password', 'ocean-popup-login' );
$lost_password_content 	= oceanwp_tm_translation( 'opl_popup_lost_password_text_content', get_theme_mod( 'opl_popup_lost_password_text_content' ) );
$lost_password_content 	= $lost_password_content ? $lost_password_content : esc_html__( 'Recover your password', 'ocean-popup-login' );

// Redirections
$login_redirect 		= get_theme_mod( 'opl_popup_login_redirect' );
$register_redirect 		= get_theme_mod( 'opl_popup_register_redirect' ); ?>

<div id="opl-login-form">

	<div class="opl-login-container">

		<div class="opl-login-content">

			<div class="<?php echo esc_attr( $classes ); ?>">

				<div class="opl-login-wrap">

					<div class="opl-login opl-wrap opl-show">

						<?php do_action( 'opl_before_login' ); ?>
						
						<header class="content-header">
							<h3 class="opl-title"><?php esc_html_e( $login_title ); ?></h3>
							<div class="opl-intro"><?php esc_html_e( $login_content ); ?></div>
						</header>

						<div class="opl-errors"></div>

						<?php do_action( 'opl_after_login_form' ); ?>

						<form id="opl_login_form" action="<?php echo home_url( '/' ); ?>" method="post">

							<?php do_action( 'opl_before_login_form_inner' ); ?>

							<div class="form-field input-wrap">
								<input type="text" class="form-control input-lg required" name="opl_user_login" id="opl_user_login" value="" required />
								<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Username', 'ocean-popup-login' ); ?></label>
								<div class="opl-line"></div>
								<div class="opl-focus-line"></div>
							</div>

							<div class="form-field input-wrap">
								<input type="password" class="form-control input-lg required" name="opl_user_pass" id="opl_user_pass" value="" required />
								<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Password', 'ocean-popup-login' ); ?></label>
								<div class="opl-line"></div>
								<div class="opl-focus-line"></div>
							</div>

							<div class="form-field input-wrap opl-remember">
								<label><input name="opl_user_remember" type="checkbox" id="opl_user_remember" value="forever" /> <?php esc_html_e( 'Remember Me', 'ocean-popup-login' ); ?></label>
							</div>

							<div class="form-field">
								<input type="hidden" name="action" value="opl_login_member"/>
								<?php
								if ( ! empty( $login_redirect ) ) { ?>
									<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $login_redirect ); ?>">
								<?php
								} ?>
								<button type="submit" name="login_button" id="login_button" class="opl-button" data-loading-text="<?php esc_html_e( 'Loading...', 'ocean-popup-login' ); ?>"><?php esc_html_e( 'Login', 'ocean-popup-login' ); ?></button>
							</div>

							<?php wp_nonce_field( 'opl-login-nonce', 'login-security' ); ?>

							<div class="opl-text"><a href="#" class="forgot-pass-link"><?php esc_html_e( 'Forgot your password? Get help', 'ocean-popup-login' ); ?></a></div>

							<?php do_action( 'opl_after_login_form_inner' ); ?>

						</form>

						<?php do_action( 'opl_after_login_form' ); ?>

						<?php
						if ( function_exists( 'the_privacy_policy_link' ) ) {
							the_privacy_policy_link( '<div class="opl-privacy">', '</div>' );
						} ?>

						<?php
						// If WordPress Social Login is enabled
						if ( function_exists( 'wsl_load_plugin_textdomain' ) ) { ?>
							<div class="opl-social-login-wrap">
								<div class="opl-social-title"><span><?php esc_html_e( 'Or log in with', 'ocean-popup-login' ); ?></span></div>
								<?php do_action( 'opl_wp_social_login' ); ?>
							</div>
						<?php
						}

						// If Nextend Social Login is enabled
						if ( class_exists( 'NextendSocialLogin', false ) ) { ?>
							<div class="opl-social-login-wrap">
								<div class="opl-social-title"><span><?php esc_html_e( 'Or log in with', 'ocean-popup-login' ); ?></span></div>
								<?php echo NextendSocialLogin::renderButtonsWithContainer(); ?>
							</div>
						<?php
						} ?>

						<?php
						// If registraion is enabled
						if ( $can_register ) { ?>

							<div class="opl-bottom">
								<div class="text"><?php esc_html_e( 'Not a member?', 'ocean-popup-login' ); ?></div>
								<a href="#" class="register-link opl-btn"><?php esc_html_e( 'Sign up', 'ocean-popup-login' ); ?></a>
							</div>

						<?php
						} ?>

						<?php do_action( 'opl_after_login' ); ?>

					</div>

					<?php
					// If registraion is enabled
					if ( $can_register ) { ?>

						<div class="opl-register opl-wrap opl-hide">

							<?php do_action( 'opl_before_register' ); ?>
						
							<header class="content-header">
								<h3 class="opl-title"><?php esc_html_e( $register_title ); ?></h3>
								<div class="opl-intro"><?php esc_html_e( $register_content ); ?></div>
							</header>

							<div class="opl-errors"></div>

							<?php do_action( 'opl_before_register_form' ); ?>

							<form id="opl_registration_form" action="<?php echo home_url( '/' ); ?>" method="post">

								<?php do_action( 'opl_before_register_form_inner' ); ?>

								<div class="form-field input-wrap">
									<input type="text" class="form-control input-lg required" name="opl_register_login" id="opl_register_login" value="" required />
									<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Your username', 'ocean-popup-login' ); ?></label>
									<div class="opl-line"></div>
									<div class="opl-focus-line"></div>
								</div>

								<div class="form-field input-wrap">
									<input type="email" class="form-control input-lg required" name="opl_register_email" id="opl_register_email" value="" required />
									<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Your email', 'ocean-popup-login' ); ?></label>
									<div class="opl-line"></div>
									<div class="opl-focus-line"></div>
								</div>

								<div class="form-field input-wrap">
									<input type="password" class="form-control input-lg required" name="opl_register_pass" id="opl_register_pass" value="" required />
									<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Password', 'ocean-popup-login' ); ?></label>
									<div class="opl-line"></div>
									<div class="opl-focus-line"></div>
								</div>

								<div class="form-field input-wrap">
									<input type="password" class="form-control input-lg required" name="opl_register_pass2" id="opl_register_pass2" value="" required />
									<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Confirm Password', 'ocean-popup-login' ); ?></label>
									<div class="opl-line"></div>
									<div class="opl-focus-line"></div>
								</div>

								<input type="hidden" name="action" value="opl_register_member"/>
								<?php
								if ( ! empty( $register_redirect ) ) { ?>
									<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $register_redirect ); ?>">
								<?php
								} ?>
								<button type="submit" name="register_button" id="register_button" class="opl-button" data-loading-text="<?php esc_html_e( 'Loading...', 'ocean-popup-login' ); ?>"><?php esc_html_e( 'Register', 'ocean-popup-login' ); ?></button>

								<?php wp_nonce_field( 'opl-login-nonce', 'register-security' ); ?>

								<?php do_action( 'opl_after_register_form_inner' ); ?>

							</form>

							<?php do_action( 'opl_after_register_form' ); ?>

							<?php
							// If WordPress Social Login is enabled
							if ( function_exists( 'wsl_load_plugin_textdomain' ) ) { ?>
								<div class="opl-social-login-wrap">
									<div class="opl-social-title"><span><?php esc_html_e( 'Or register with', 'ocean-popup-login' ); ?></span></div>
									<?php do_action( 'opl_wp_social_register' ); ?>
								</div>
							<?php
							}

							// If Nextend Social Login is enabled
							if ( class_exists( 'NextendSocialLogin', false ) ) { ?>
								<div class="opl-social-login-wrap">
									<div class="opl-social-title"><span><?php esc_html_e( 'Or register with', 'ocean-popup-login' ); ?></span></div>
									<?php echo NextendSocialLogin::renderButtonsWithContainer(); ?>
								</div>
							<?php
							} ?>

							<?php
							if ( function_exists( 'the_privacy_policy_link' ) ) {
								the_privacy_policy_link( '<div class="opl-privacy">', '</div>');
							} ?>

							<div class="opl-bottom">
								<div class="text"><?php esc_html_e( 'Already a member?', 'ocean-popup-login' ); ?></div>
								<a href="#" class="login-link opl-btn"><?php esc_html_e( 'Sign in', 'ocean-popup-login' ); ?></a>
							</div>

							<?php do_action( 'opl_after_register' ); ?>

						</div>

					<?php
					} ?>

					<div class="opl-reset-password opl-wrap opl-hide">

						<?php do_action( 'opl_before_reset_password' ); ?>
						
						<header class="content-header">
							<h3 class="opl-title"><?php esc_html_e( $lost_password_title ); ?></h3>
							<div class="opl-intro"><?php esc_html_e( $lost_password_content ); ?></div>
						</header>
						
						<div class="opl-errors"></div>

						<?php do_action( 'opl_before_reset_password_form' ); ?>

						<form id="opl_reset_password_form" action="<?php echo home_url( '/' ); ?>" method="post">

							<?php do_action( 'opl_before_reset_password_form_inner' ); ?>

							<div class="form-field input-wrap">
								<input type="text" class="form-control input-lg required" name="opl_user_or_email" id="opl_user_or_email" value="" required />
								<label class="opl-label" aria-hidden="true"><?php esc_html_e( 'Username or E-mail', 'ocean-popup-login' ); ?></label>
								<div class="opl-line"></div>
								<div class="opl-focus-line"></div>
							</div>

							<div class="form-field">
								<input type="hidden" name="action" value="opl_reset_password" />
								<button type="submit" name="forgot_button" id="forgot_button" class="opl-button" data-loading-text="<?php esc_html_e( 'Loading...', 'ocean-popup-login' ); ?>"><?php esc_html_e( 'Send My Password', 'ocean-popup-login' ); ?></button>
							</div>

							<?php wp_nonce_field( 'opl-login-nonce', 'password-security' ); ?>

							<div class="opl-text"><?php esc_html_e( 'A password will be e-mailed to you.', 'ocean-popup-login' ); ?></div>

							<?php do_action( 'opl_after_reset_password_form_inner' ); ?>

						</form>

						<?php do_action( 'opl_after_reset_password_form' ); ?>

						<?php
						if ( function_exists( 'the_privacy_policy_link' ) ) {
							the_privacy_policy_link( '<div class="opl-privacy">', '</div>');
						} ?>

						<div class="opl-bottom">
							<div class="text"><?php esc_html_e( 'Back to', 'ocean-popup-login' ); ?></div>
							<a href="#" class="login-link opl-btn"><?php esc_html_e( 'Login', 'ocean-popup-login' ); ?></a>
						</div>

						<?php do_action( 'opl_after_reset_password' ); ?>

					</div>

				</div>

				<a href="#" class="opl-close-button">×</a>

			</div>

		</div>

	</div>

	<div class="opl-overlay"></div>

</div>