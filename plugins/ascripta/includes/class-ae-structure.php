<?php

/**
 * AE Structure
 *
 * @class 		AE_Structure
 * @version		1.1.9
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Structure' ) ) {

	class AE_Structure {

		private static $instance = null;

		/**
		 * Instance
		 *
		 * Ensures only one instance of the plugin is loaded or can be loaded.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Construct the class.
		 */
		public function __construct() {

			// Disable the featured image under certain conditions.
			add_filter( 'post_thumbnail_html', array( $this, 'disable_featured_image' ), 10, 3 );

		}

		/**
		 * Disable the featured image under certain conditions.
		 */
		public function disable_featured_image( $markup, $post_id, $post_image_id ) {

			if (
				AE_Helpers::is_blog_single() && get_theme_mod( 'post_featured', 1 ) == 0 ||
				AE_Helpers::is_page() && get_theme_mod( 'page_featured', 1 ) == 0 ||
				AE_Helpers::is_blog_archive() && get_theme_mod( 'archive_featured', 1 ) == 0 
			){
				return null;
			}

			return $markup;

		}

		/**
		 * Get the current template switch state.
		 *
		 * @return string The customizer section that has to be accessed.
		 */
		public static function get_template() {

			if ( AE_Helpers::is_blog_single() ) {
				$switch = 'post';
			} elseif ( AE_Helpers::is_blog_archive() ) {
				$switch = 'archive';
			} elseif ( AE_Helpers::is_woocommerce() ) {
				$switch = 'woocommerce';
			} elseif ( AE_Helpers::is_page() ) {
				$switch = 'page';
			} else {
				$switch = 'unknown';
			}

			return $switch;

		}

		public static function layout() {

			$switch = self::get_template();

			if ( isset( $switch ) && $switch != 'woocommerce' ) {

				// Breakpoint
				$breakpoint = get_theme_mod( $switch . '_breakpoint', 'md' );

				// Layout Width
				$width = get_theme_mod( $switch . '_layout_width', 8 );
				$layout = 'col-' . $breakpoint . '-' . $width . ' col-' . $breakpoint . '-offset-' . ( 8 - $width );

				// Layout Type
				$layout_type = null;

				if( function_exists( 'get_field' ) ) {
					$layout_type = get_field( 'entry_layout_single' );
				}

				if( !isset( $layout_type ) || $layout_type == 'default' ) {
					$layout_type = get_theme_mod( $switch . '_layout', 'right-sidebar' );
				}
				
				if( $layout_type == 'left-sidebar' ){
					$layout = 'col-' . $breakpoint . '-' . $width . ' col-' . $breakpoint . '-offset-' . ( 8 - $width ) . ' col-' . $breakpoint . '-push-' . ( $width - 4 );
				} elseif( $layout_type == 'full-width' ) {
					$layout = 'col-' . $breakpoint . '-' . ( 2 * $width - 4 ) . ' col-' . $breakpoint . '-offset-' . ( 8 - $width );
				}

			} elseif ( $switch == 'woocommerce' ) {

				if ( is_checkout() || is_cart() || is_account_page() ) {
					$layout = 'col-md-12';
				} else {
					$layout = 'col-md-8';
				}

			}

			echo $layout;

		}

		/**
		 * Pull the sidebar layout from the Customizer based on the current template.
		 */
		public static function sidebar() {

			$sidebar['switch'] = self::get_template();
			$sidebar['type']   = 'sidebar-page';
			$sidebar['hide']   = false;

			if ( AE_Helpers::is_blog_single() ) {
				$sidebar['type']   = 'sidebar-blog';
			} elseif ( AE_Helpers::is_blog_archive() ) {
				$sidebar['type']   = 'sidebar-blog';
			} elseif ( AE_Helpers::is_woocommerce() ) {
				$sidebar['type']   = 'sidebar-woocommerce';
			} elseif ( AE_Helpers::is_page() ) {
				$sidebar['type']   = 'sidebar-page';
			}

			if ( isset( $sidebar['switch'] ) && $sidebar['switch'] != 'woocommerce' ) {

				// Breakpoint
				$breakpoint = get_theme_mod( $sidebar['switch'] . '_breakpoint', 'md' );

				// Layout Width
				$width = get_theme_mod( $sidebar['switch'] . '_layout_width', 8 );
				$sidebar['layout'] = 'col-' . $breakpoint . '-' . ( $width - 4 );

				// Layout Type
				$layout_type = null;

				if( function_exists( 'get_field' ) ) {
					$layout_type = get_field( 'entry_layout_single' );
				}

				if( !isset( $layout_type ) || $layout_type == 'default' ) {
					$layout_type = get_theme_mod( $sidebar['switch'] . '_layout', 'right-sidebar' );						
				}

				if( $layout_type == 'left-sidebar' ){
					$sidebar['layout'] = 'col-' . $breakpoint . '-' . ( $width - 4 ) . ' col-' . $breakpoint . '-pull-' . $width;
				} elseif( $layout_type == 'full-width' ) {
					$sidebar['hide'] = true;
				}

			} elseif ( $sidebar['switch'] == 'woocommerce' ) {

				if ( is_checkout() || is_cart() || is_account_page() ) {
					$sidebar['hide'] = true;
				} else {
					$sidebar['layout'] = 'col-md-4';
				}

			}

			return $sidebar;

		}

		/**
		 * Show an error if the loop cannot find any content.
		 */
		public static function error( $settings ) { ?>

			<p class="entry-error-message">
				<?php 
				if( !is_home() ) {
					_e( 'The content you are looking for cannot be found.', 'ascripta' );
				} else {
					_e( 'No posts have been published to the blog yet.', 'ascripta' );
				}
				?>
			</p>

			<?php

			// Show a search form to help finding content.
			get_search_form();
		
			// Get all available post types and move the "Pages" on the first position.
			$types = get_post_types(); 
			$types = array('page' => $types['page']) + $types; 

			// Output the custom post types.
			echo '<div class="row">';

				foreach( array_keys( $types ) as $index => $type ){ 
					$type = get_post_type_object( $type ); 
					$query = new WP_Query( array( 'post_type' => $type->name, 'posts_per_page' => 10 ) ); 
					if( in_array( $type->name , $settings ) && $query->have_posts() ): ?>

						<div class="<?php echo ( $type->name != 'page' ) ? 'col-sm-6' : 'col-sm-12'; ?>">
							
							<h2><?php echo ucfirst( $type->labels->name ); ?></h2>
							
							<ul class="<?php echo ( $type->name != 'page' ) ? 'list-unstyled' : 'list-inline'; ?>">
								<?php while( $query->have_posts() ) : $query->the_post(); ?>
									<li>
										<a href="<?php the_permalink(); ?>">
											<?php the_title(); ?>
										</a>
									</li>
								<?php endwhile; ?>
							</ul>

							<?php if( $type->has_archive || $type->name == 'post' ): ?>
								<a href="<?php echo !( $type->name == 'post' ) ? $type->has_archive : home_url( 'blog/' ); ?>" class="btn btn-default">View all <?php echo $type->labels->name; ?></a>
							<?php endif; ?>

						</div>

						<?php 
						// Start a new row when necessary.
						echo ( $index % 2 == 0 ) ? '</div><div class="row">' : null ;
						?>
						
					<?php endif; ?>
				<?php }
			echo '</div>';

		}

	}

}

return new AE_Structure();
