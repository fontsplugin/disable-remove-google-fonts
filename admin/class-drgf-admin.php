<?php
/**
 * DRGF Admin Page.
 *
 * @package disable-remove-google-fonts
 */

/**
 * Create the admin pages.
 */
class DRGF_Admin {

	/**
	 * Start up
	 */
	public function __construct() {
		register_activation_hook( DRGF_PLUGIN_FILE, array( $this, 'activate' ) );

		add_action( 'admin_menu', array( $this, 'add_submenu' ), 10 );
		add_action( 'admin_init', array( $this, 'admin_redirect' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	function activate() {
		add_option( 'drgf_do_activation_redirect', true );
	}

	/**
 * Redirect to the Google Fonts Welcome page.
 */
function admin_redirect() {
	if ( get_option( 'drgf_do_activation_redirect', false ) ) {
		delete_option( 'drgf_do_activation_redirect' );
		if ( ! isset( $_GET['activate-multi'] ) && ! is_network_admin() ) {
			wp_redirect( admin_url('themes.php?page=drgf') );
			exit;
		}
	}
}


	/**
	 * Add options page
	 */
	public function add_submenu() {
		add_submenu_page(
			'themes.php',
			__( 'Google Fonts', 'disable-remove-google-fonts' ),
			__( 'Google Fonts', 'disable-remove-google-fonts' ),
			'manage_options',
			'drgf',
			array( $this, 'render_welcome_page' ),
			50
		);
	}

	/**
	 * Add options page
	 */
	public function enqueue() {
		wp_enqueue_style( 'drgf-admin', esc_url( DRGF_DIR_URL . 'admin/style.css' ), false, DRGF_VERSION );
		wp_enqueue_script( 'drgf-admin', esc_url( DRGF_DIR_URL . 'admin/scripts.js' ), 'jquery', DRGF_VERSION, false );
	}

	/**
	 * Options page callback
	 */
	public function render_welcome_page() {
		update_option( 'dismissed-drgf-welcome', true );
		$site_url = site_url( '', 'https' );
		$url      = preg_replace( '(^https?://)', '', $site_url );
		?>
		<style>
		.notice {
			display: none;
		}
		</style>
			<div class="drgf-admin__wrap">
				<div class="drgf-admin__content">
					<div class="drgf-admin__content__header">
						<h1>Your Quickstart Guide</h1>
					</div>
					<div class="drgf-admin__content__inner">
						<p>Thank you for installing the <em>Remove Google Fonts</em> plugin!</p>
						<p><strong>Now the plugin is active, it will begin working right away.</strong></p>
						<p>To confirm it's working as expected, you can test your website here: <a target="_blank" href="https://fontsplugin.com/google-fonts-checker/">Google Fonts Checker</a>.</p>
						<p>If there are any font requests still present, please <a href="https://wordpress.org/support/plugin/disable-remove-google-fonts/#new-post">create a support ticket</a> and our team will happily look into it for you</a>.</p>
						<h3>How This Plugin Works</h3>
						<p>This plugin completely removes all references to Google Fonts from your website. That means that your website will no longer render  Google Fonts and will instead revert to a <a target="_blank" href="https://fontsplugin.com/web-safe-system-fonts/">fallback font</a>.</p>
						<p>However, some services load Google Fonts within an embedded iFrame. These include YouTube, Google Maps and ReCaptcha. It's not possible for this plugin to remove those services for the reasons <a target="_blank" href="https://fontsplugin.com/remove-disable-google-fonts/#youtube">outlined here</a>.</p>
						<?php if ( function_exists( 'ogf_initiate' ) ) : ?>
							<h3>⭐️ Fonts Plugin Pro</h3>
							<p>Instead of removing the fonts completely, <a target="_blank" href="https://fontsplugin.com/drgf-upgrade">Fonts Plugin Pro</a> enables you to host the fonts from your <strong>own domain</strong> (<?php echo $url; ?>)  with the click of a button. Locally hosted fonts are more efficient, quicker to load and don't connect to any third-parties (GDPR & DSGVO-friendly).</p>
							<a class="drgf-admin__button button" href="https://fontsplugin.com/drgf-upgrade" target="_blank">Learn More</a>
						<?php else : ?>
							<h3>⭐️ Host Google Fonts Locally</h3>
							<p>Instead of removing the fonts completely, our <a href="https://fontsplugin.com/drgf-upgrade" target="_blank">Pro upgrade</a> enables you to host the fonts from your <strong>own domain</strong> (<?php echo $url; ?>)  with the click of a button. Locally hosted fonts are more efficient, quicker to load and don't connect to any third-parties (GDPR & DSGVO-friendly).</p>
							<a class="drgf-admin__button button" href="https://fontsplugin.com/drgf-upgrade" target="_blank">Get Started</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
	}
}

if ( is_admin() ) {
	$drgf_admin = new DRGF_Admin();
}
