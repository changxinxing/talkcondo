<?php

/**
 * AE Admin HiDPI
 *
 * @version		1.1.3
 * @author 		Ascripta
 */

add_settings_section( 'retina', 'High DPI', 'register_custom_section_tools_hidpi', 'ae_tools_retina' );

function register_custom_section_tools_hidpi() {

	$view           = isset($_GET['view']) ? $_GET['view'] : 'all';
	$paged          = isset($_GET['paged']) ? $_GET['paged'] : 1;
	$s              = isset($_GET['s']) && !empty($_GET['s']) ? sanitize_text_field($_GET['s']) : null;
	$issues         = $count = 0;
	$posts_per_page = 15; // TODO: HOW TO GET THE NUMBER OF MEDIA PER PAGES? IT IS NOT get_option('posts_per_page');
	$issues         = wr2x_get_issues();

	if ($view == 'issues') {
		global $wpdb;
		$totalcount = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM $wpdb->posts p
			WHERE post_status = 'inherit'
			AND post_type = 'attachment'" . wr2x_create_sql_if_wpml_original() . "
			AND post_title LIKE %s
			AND ( post_mime_type = 'image/jpeg' OR
			post_mime_type = 'image/png' OR
			post_mime_type = 'image/gif' )
		", '%' . $s . '%'));
		$postin     = count($issues) < 1 ? array(
			-1
		) : $issues;
		$query      = new WP_Query(array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post__in' => $postin,
			'paged' => $paged,
			'posts_per_page' => $posts_per_page,
			's' => $s
		));
	} else {
		$query = new WP_Query(array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
			'paged' => $paged,
			'posts_per_page' => $posts_per_page,
			's' => $s
		));

		//$s
		$totalcount = $query->found_posts;
	}

	$issues_count = count($issues);

	// If 'search', then we need to clean-up the issues count
	if ($s && $issues_count > 0) {
		global $wpdb;
		$issues_count = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM $wpdb->posts p
			WHERE id IN ( " . implode(',', $issues) . " )" . wr2x_create_sql_if_wpml_original() . "
			AND post_title LIKE %s
		", '%' . $s . '%'));
	}

	$results    = array();
	$count      = $query->found_posts;
	$pagescount = $query->max_num_pages;
	foreach ($query->posts as $post) {
		$info = wr2x_retina_info($post->ID);
		array_push($results, array(
			'post' => $post,
			'info' => $info
		));
	}
?>

<?php

	$active_sizes = wr2x_get_active_image_sizes();

	$max_width  = 0;
	$max_height = 0;
	foreach ($active_sizes as $name => $active_size) {
		if ($active_size['height'] != 9999 && $active_size['height'] > $max_height) {
			$max_height = $active_size['height'];
		}
		if ($active_size['width'] != 9999 && $active_size['width'] > $max_width) {
			$max_width = $active_size['width'];
		}
	}

?>

<?php if( $totalcount > 0 ): ?>

	<div class="wp-filter">
		<div class="filter-items">
			<input type="hidden" name="mode" value="list">
			<div class="view-switch">
				<a href="?page=ae_tools&view=issues&refresh=true" class="dashicons dashicons-update">
					<span class="screen-reader-text">Refresh</span>
				</a>
			</div>

			<div class="actions">
				<a onclick='wr2x_generate_all()' class='button button-primary action'>
					<?php _e("Generate", 'wp-retina-2x'); ?>
				</a>
				<a onclick='wr2x_delete_all()' class='button action'>
					<?php _e("Remove All", 'wp-retina-2x'); ?>
				</a>
				<span id='wr2x_progression'></span>
			</div>
		</div>

		<form id="posts-filter" action="admin.php" method="get">
			<div class="search-form">
				<label for="media-search-input" class="screen-reader-text">
					<?php _e('Search', 'ascripta'); ?>
				</label>
				<input type="search" placeholder="Search" id="media-search-input" class="search" name="s" value="<?php echo $s ? $s : ''; ?>">
				<input type="hidden" name="page" value="ae_tools">
				<input type="hidden" name="view" value="<?php echo $view; ?>">
				<input type="hidden" name="paged" value="<?php echo $paged; ?>">
			</div>
		</form>

	</div>

	<p>
		<?php esc_html_e( 'Upload high resolution assets for items in your media library to increase quality on high resolution displays.', 'ascripta' ); ?>
	</p>

	<div class="tablenav top">
		<ul class="subsubsub">
			<li class="all">
				<a <?php if ($view=='all' ) echo "class='current'"; ?> href='?page=ae_tools&s=<?php echo $s; ?>&view=all'>
					<?php _e("All", 'ascripta'); ?>
				</a>
				<span class="count">(<?php echo $totalcount; ?>)</span>
			</li> |
			<li class="all">
				<a <?php if ($view=='issues' ) echo "class='current'"; ?> href='?page=ae_tools&s=<?php echo $s; ?>&view=issues'>
					<?php _e("Issues", 'ascripta'); ?>
				</a>
				<span class="count">(<?php echo $issues_count; ?>)</span>
			</li>
		</ul>
		<div class="wr2x_pagination">
			<?php
				echo paginate_links( array(
					'base' => '?page=ae_tools&s=' . urlencode($s) . '&view=' . $view . '%_%',
					'current' => $paged,
					'format' => '&paged=%#%',
					'total' => $pagescount,
					'prev_text' => __( '&laquo;', 'text-domain' ),
					'next_text' => __( '&raquo;', 'text-domain' ),
				));
			?>
		</div>
	</div>

	<table class='wp-list-table widefat fixed striped media'>

		<thead>
			<tr>
				<th>
					<?php _e("File", 'ascripta'); ?>
				</th>
				<th>
					<?php _e("Image Sizes", 'ascripta'); ?>
				</th>
				<th style="width:125px">
					<?php _e("Replace", 'ascripta'); ?>
				</th>
				<th style="width:125px">
					<?php _e("High DPI", 'ascripta'); ?>
				</th>
			</tr>
		</thead>

		<tbody>

			<?php

		foreach ($results as $index => $attr) {

			$post = $attr['post'];
			$info = $attr['info'];
			$meta = wp_get_attachment_metadata($post->ID);

			// Let's clean the issues status
			if ($view != 'issues') {
				wr2x_update_issue_status($post->ID, $issues, $info);
			}

			if (isset($meta) && isset($meta['width'])) {
				$original_width  = $meta['width'];
				$original_height = $meta['height'];
			}

			?>

			<tr class='wr2x-file-row' postId="<?php echo $post->ID; ?>">

				<td class="title column-title has-row-actions column-primary" data-colname="File">
					<?php $title = ($post->post_title ? $post->post_title : '<i>Untitled</i>'); ?>
					<strong class="has-media-icon">
						<a href="<?php echo get_edit_post_link( $post->ID ); ?>">
							<span class="media-icon image-icon">
								<img width="100" height="100" src="<?php echo wp_get_attachment_image_src($post->ID, 'thumbnail')[0]; ?>" alt="">
							</span>
							<span aria-hidden="true"><?php echo $title; ?></span>
							<span class="screen-reader-text">Edit “<?php echo $title; ?>”</span>
						</a>
					</strong>
					<p class="filename">
						<span class="screen-reader-text">Size: </span>
						<span class='resolution'>Size:
							<span class='<?php echo ( $original_width < $max_width ? "red" : "" ); ?>'> <?php echo $original_width; ?></span> ×
							<span class='<?php echo ( $original_height < $max_height ? "red" : "" ); ?>'> <?php echo $original_height; ?></span>
						</span>
					</p>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php _e('Edit', 'ascripta'); ?></a> | </span>
						<span class="view"><a onclick="wr2x_generate( <?php echo $post->ID; ?>, true)" id="wr2x_generate_button_<?php echo $post->ID; ?>"><?php _e('Generate', 'ascripta'); ?></a></span>
					</div>
					<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
				</td>

				<td id="wr2x-info-'<?php echo $post->ID; ?>" style="padding-top: 10px;" class="wr2x-info">
					<?php echo wpr2x_html_get_basic_retina_info($post, $info); ?>
				</td>

				<td class='wr2x-fullsize-replace'>
					<div class='wr2x-dragdrop'></div>
				</td>

				<td class="wr2x-fullsize-retina-upload" id="wr2x-fullsize-retina-upload-<?php echo $post->ID; ?>">
					<div class='wr2x-dragdrop'>
						<?php echo wpr2x_html_get_basic_retina_info_full_sign($post->ID, $info); ?>
					</div>
				</td>

			</tr>

			<?php } ?>

		</tbody>

	</table>

<?php else: ?>

	<p>
		<?php esc_html_e( 'Congratulations, there are no issues with your media library.', 'ascripta' ); ?>
	</p>

<?php endif; 

}
