<?php
/**
 * Welcome Notice Class.
 *
 * @package   disable-remove-google-fonts
 * @copyright Copyright (c) 2020, Fonts Plugin
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'DRGF_Notice' ) ) :
	/**
	 * The welcome.
	 */
	class DRGF_Notice {

		/**
		 * Slug.
		 *
		 * @var string $slug
		 */
		private $slug;

		/**
		 * Message.
		 *
		 * @var string $message
		 */
		private $message;

		/**
		 * Type.
		 *
		 * @var string $type
		 */
		private $type;

		/**
		 * Class constructor.
		 *
		 * @param string $slug Slug.
		 * @param string $message Message.
		 * @param string $type Type.
		 */
		public function __construct( $slug, $message, $type = 'success' ) {
			$this->slug    = $slug;
			$this->message = $message;
			$this->type    = $type;

			// Add actions.
			add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			add_action( 'wp_ajax_drgf_dismiss_notice', array( $this, 'dismiss_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Enqeue the styles and scripts.
		 */
		public function enqueue() {
			wp_enqueue_script( 'drgf-scripts', esc_url( DRGF_DIR_URL . 'admin/scripts.js' ), array( 'jquery' ), DRGF_VERSION, false );
		}

		/**
		 * AJAX handler to store the state of dismissible notices.
		 */
		public function dismiss_notice() {
			if ( isset( $_POST['type'] ) ) {
				// Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice).
				$type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
				// Store it in the options table.
				update_option( 'dismissed-' . $type, true );
			}
		}

		/**
		 * Display the admin notice.
		 */
		public function display_admin_notice() {
			if ( get_option( 'dismissed-' . $this->slug, false ) ) {
				return;
			}
			?>

			<div class="notice notice-<?php echo esc_attr( $this->type ); ?> is-dismissible notice-dismiss-drgf" data-notice="<?php echo esc_attr( $this->slug ); ?>">
				<p>
					<?php
						echo $this->message; // WPCS: XSS ok.
					?>
				</p>
			</div>
			<?php
		}
	}
endif;

$message = sprintf(
	// translators: %s Link to DRGF welcome page.
	__( 'Thank you for installing <strong>Disable & Remove Google Fonts</strong>! <a href="%s">Finish the setup process</a>.', 'olympus-google-fonts' ),
	esc_url( admin_url( '/themes.php?page=drgf' ) )
);

/*
* Instantiate the DRGF_Notice class.
*/
new DRGF_Notice( 'drgf-welcome', $message, 'success' );
