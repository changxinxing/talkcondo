<?php

/**
 * AE Components
 *
 * @class 		AE_Components
 * @version		1.2.0
 * @package		AE/Classes
 * @category	Class
 * @author 		Ascripta
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'AE_Components' ) ){

	class AE_Components {

		/**
		 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
		 */
		public function __construct() {

			// Load the required libraries, classes and option fields.
			$this->load_libraries();

		}

		/**
		 * Load the required libraries to make this class work.
		 */
		private function load_libraries() {

			require_once ASCRIPTA_ENGINE_FUNCTIONS_PATH . 'func-ae-components.php';

		}

		/**
		 * Generate the author box component.
		 */
		public static function author_box() {

			if ( get_theme_mod( 'post_author_box', 1 ) == 1 ) { ?>

				<div class="media well entry-author-box">

					<?php if( $avatar = get_avatar( get_the_author_meta( 'ID' ), 75 ) ): ?>
						<div class="media-left">
							<div class="media-object entry-author-box-avatar">
								<?php echo $avatar; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="media-body" itemscope itemtype="http://schema.org/Person" itemprop="author">

						<h4 class="media-heading entry-author-box-title">
							<?php _e( 'Written by', 'ascripta' ); ?>
							<span class="entry-author-box-name" itemprop="name">
								<?php echo get_the_author_link( get_the_author_meta( 'ID' ) ); ?>
							</span>
						</h4>

						<?php
						if ( get_the_author_meta( 'description' ) != NULL ) {
							echo '<p class="entry-author-box-content" itemprop="description">' . get_the_author_meta( 'description' ) . '</p>';
						}
						?>

						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" itemprop="url">
							<?php _e( 'View more posts written by this author', 'ascripta' ); ?> &raquo;
						</a>

					</div>

				</div>

			<?php
			}

		}


		/**
		 * Generate the pager component.
		 * 
		 * @since 1.4.0
		 */

		public static function pager() {

			if ( get_theme_mod( 'post_pager', 1 ) == 1 ) { ?>

				<nav class="entry-pager">
					<ul class="pager">
						<?php previous_post_link( '<li class="previous">%link</li>', '&larr; %title' ); ?>
						<?php next_post_link( '<li class="next">%link</li>', '%title &rarr;' ); ?>
					</ul>
				</nav>

			<?php
			}

		}


		/**
		 * Generate the related posts component.
		 * 
		 * @since 1.4.0
		 */

		public static function related_posts() {

			if ( get_theme_mod( 'post_related', 1 ) == 1 ) { ?>

				<?php $args = array(
					'post_type'           => 'post',
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 3,
					'ignore_sticky_posts' => 1,
					'tag__in'             => array(),
					'category__in'        => array(),
					'meta_query'          => array(
						array( 'key' => '_thumbnail_id' )
					)
				); ?>

				<?php if( $tags = get_the_tags() ) {
					foreach( $tags as $tag ) {
						$args['tag__in'][] = $tag->term_id;
					}
				} elseif( $categories = get_the_category() ) {
					foreach( $categories as $category ) {
						$args['category__in'][] = $category->term_id;
					}
				} ?>

				<?php $query = new WP_Query( $args ); ?>

				<?php if( $query->have_posts() && $query->found_posts >= 3 ): ?>

					<div class="entry-related">

						<h3 class="entry-related__heading">
							<?php esc_html_e( 'Related Posts', 'ascripta' ); ?>
						</h3>

						<div class="row">
								
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>

								<div class="col-sm-4">

									<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-related__item' ); ?> itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost">
									
										<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url">
											<?php the_post_thumbnail( 'medium', array( 'class' => 'entry-related__item__thumb', 'itemprop' => 'image' ) ); ?>

											<h3 class="entry-related__item__title" itemprop="headline">
												<?php the_title(); ?>
											</h3>
										</a>

									</article>

								</div>

							<?php endwhile; ?>

						</div>

					</div>

					<?php wp_reset_query(); ?>

				<?php endif; ?>

			<?php
			}

		}


		/**
		 * Generate the pagination component.
		 *
		 * @param integer [$pages = ''] The total number of pages.
		 * @param integer [$range = 10] The number of pages to show in the menu.
		 */

		public static function pagination( $pages = '', $range = 10 ) {

			$showitems = ( $range * 2 ) + 1;

			global $paged;
			if ( empty( $paged ) )
				$paged = 1;

			if ( $pages == '' ) {
				global $wp_query;
				$pages = $wp_query->max_num_pages;
				if ( !$pages )
					$pages = 1;
			}

			if ( 1 != $pages ) {

				echo '<ul class="pagination">';

				if ( $paged > 1 ) {
					echo '<li>' . get_previous_posts_link( '&laquo;' ) . '</li>';
				} else {
					echo '<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
				}

				for ( $i = 1; $i <= $pages; $i++ ) {
					if ( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) {
						if ( $paged == $i ) {
							echo '<li class="active"><a href="#">' . $i . ' <span class="sr-only">(current)</span></a></li>';
						} else {
							echo '<li><a href="' . get_pagenum_link( $i ) . '" class="inactive">' . $i . '</a></li>';
						}
					} else {
						$range_ex = true;
					}
				}

				if ( $paged < $pages ) {
					echo '<li>' . get_next_posts_link( '&raquo;' ) . '</li>';
				} else {
					echo '<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
				}

				echo '</ul>';

			}

		}


		/**
		 * Generate the breadcrumb component.
		 */
		public static function breadcrumb() {

			if ( !( ( AE_Helpers::is_page() && get_theme_mod( 'page_breadcrumb', 1 ) == 0 ) || ( AE_Helpers::is_blog_single() && get_theme_mod( 'post_breadcrumb', 1 ) == 0 ) || ( AE_Helpers::is_blog_archive() && get_theme_mod( 'archive_breadcrumb', 1 ) == 0 ) ) ) {

				$index = 1;

				echo '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

					/**
					 * Front Page
					 *
					 * Always insert the link to the front page.
					 */
					if( is_home() && is_front_page() ){
						self::breadcrumb_item( 'You Are Home', '', $index );
					} else {
						self::breadcrumb_item( 'Home', home_url(), $index );
					}

					/**
					 * Blog
					 *
					 * Check if the current template is part of the blog.
					 */
					if ( AE_Helpers::is_blog() ) {

						/*
						 * Blog: General
						 *
						 * Insert the link if on a general blog page or the title if on the main page.
						 */
						if ( get_option( 'show_on_front' ) == 'page' ) {

							if ( is_home() ) {
								self::breadcrumb_item( get_page( get_option( 'page_for_posts' ) )->post_title, '', $index );
							} else {
								self::breadcrumb_item( get_page( get_option( 'page_for_posts' ) )->post_title, get_permalink( get_option( 'page_for_posts' ) ), $index );
							}

						}
						
						if ( is_single() ) {

							/*
							 * Blog: Single Post
							 *
							 * If the current template is a blog post.
							 */
							
							self::breadcrumb_item( get_the_title(), '', $index );

						} elseif ( is_category() ) {

							/*
							 * Archive: Category
							 *
							 * If the current template is a category archive.
							 */
							
							self::breadcrumb_item( single_cat_title( 'Category: ', false ), '', $index );
						
						} elseif ( is_tag() ) {

							/*
							 * Archive: Tag
							 *
							 * If the current template is a tag archive.
							 */
							
							self::breadcrumb_item( single_tag_title( 'Tag: ', false ), '', $index );
						
						} elseif ( is_day() ) {

							/*
							 * Archive: Day
							 *
							 * If the current template is a day archive.
							 */
							
							self::breadcrumb_item( 'Archive for ' . get_the_time( 'F jS, Y' ), '', $index );
						
						} elseif ( is_month() ) {

							/*
							 * Archive: Month
							 *
							 * If the current template is a month archive.
							 */
							
							self::breadcrumb_item( 'Archive for ' . get_the_time( 'F, Y' ), '', $index );
						
						} elseif ( is_year() ) {

							/*
							 * Archive: Year
							 *
							 * If the current template is a year archive.
							 */
							
							self::breadcrumb_item( 'Archive for ' . get_the_time( 'Y' ), '', $index );

						} elseif ( is_author() ) {

							/*
							 * Archive: Author
							 *
							 * If the current template is an author archive.
							 */
							
							self::breadcrumb_item( 'Author: ' . get_the_author(), '', $index );
						
						} elseif ( isset( $_GET['paged'] ) && !empty( $_GET['paged'] ) ) {

							/*
							 * Blog: Paged
							 *
							 * If the current template is a paginated blog loop.
							 */
							
							self::breadcrumb_item( 'Blog Archives', '', $index );
						
						} elseif ( is_search() ) {

							/*
							 * Search Results
							 *
							 * If the current template shows search results.
							 */
							
							self::breadcrumb_item( 'Search Results', '', $index );

						}

					} elseif ( AE_Helpers::is_woocommerce() ) {

						/*
						 * WooCommerce
						 *
						 * If the current template is part of WooCommerce.
						 */

						$trail = AE_Compatibility_WooCommerce::breadcrumb();

						foreach ( $trail as $item ) {
							self::breadcrumb_item( $item, '', $index );
						}

					} elseif ( is_page() ) {

						/*
						 * Ancestors
						 *
						 * If the current page has ancestors.
						 */

						if( $ancestors = get_post_ancestors( get_the_ID() ) ){
							foreach( array_reverse( $ancestors ) as $ancestor ) {
								self::breadcrumb_item( get_the_title( $ancestor ), get_permalink( $ancestor ), $index );
							}
						}

						/*
						 * Page
						 *
						 * If the current template is a page.
						 */
						
						self::breadcrumb_item( get_the_title(), '', $index );

					} elseif( is_home() && get_queried_object()->post_count == 0 ) {

						/*
						 * 404 Blog Error
						 *
						 * If there are no posts on the blog.
						 */

						self::breadcrumb_item( 'Blog', '', $index );

					} elseif( is_post_type_archive() || is_tax() || ( is_singular() && !in_array( get_post_type(), array( 'post', 'page' ) ) ) ) {
						
						/*
						 * Custom Post Types
						 *
						 * Check if the current template is a custom post type.
						 */

						$post_type = get_post_type_object( get_post_type() )->labels;

						if( is_post_type_archive() ) {

							// Archive
							self::breadcrumb_item( $post_type->name, '', $index );

						} elseif( is_tax() ) {

							// Archive
							self::breadcrumb_item( $post_type->name, get_post_type_archive_link( get_post_type() ), $index );

							// Taxonomy
							self::breadcrumb_item( get_the_archive_title(), '', $index );

						} else {

							// Archive
							self::breadcrumb_item( $post_type->name, get_post_type_archive_link( get_post_type() ), $index );

							// Singular
							self::breadcrumb_item( get_the_title(), '', $index );

						}

					}

				echo '</ol>';

			}

		}

		/**
		 * Generate the breadcrumb item.
		 *
		 * @param string   $title      The title visible in the breadcrumb item.
		 * @param string   [$url = ''] The url where the item would link to.
		 * @param integer  &$index     The id for the current itme
		 */
		public static function breadcrumb_item( $title, $url = '', &$index ) {

			$html = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';

				if ( $url != null ) {
					$html .= '<a href="' . $url . '" itemprop="item"><span itemprop="name">' . $title . '</span></a>';
				} else {
					$html .= '<span itemprop="name">' . $title . '</span>';
				}

				$html .= '<meta itemprop="position" content="' . $index . '">';

			$html .= '</li>';

			$index++;

			echo $html;

		}


	}

}

return new AE_Components();
