<?php

/**
 * AE HiDPI
 *
 * @class 		AE_HiDPI
 * @version		1.1.10
 * @package		AE/Modules/Classes
 * @category	Class
 * @author 		Ascripta
 *
 * @TODO: Clean up and integrate as a class.
 * @TODO: Remove the old retina engine.
 */

if( !in_array( 'wp-retina-2x/wp-retina-2x.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

	/*
	 * Actions and Filters
	 */

	add_filter( 'wp_generate_attachment_metadata', 'wr2x_wp_generate_attachment_metadata' );
	add_action( 'delete_attachment', 'wr2x_delete_attachment' );
	add_filter( 'wr2x_validate_src', 'wr2x_validate_src' );
	add_filter( 'wp_calculate_image_srcset', 'wr2x_wp_calculate_image_srcset', 1000, 2 );

	/*
	 * AJAX
	 */

	if ( is_admin() ) {

		/**
		 * Actions
		 */

		add_action( 'wp_ajax_wr2x_generate', 'wr2x_wp_ajax_wr2x_generate' );
		add_action( 'wp_ajax_wr2x_delete', 'wr2x_wp_ajax_wr2x_delete' );
		add_action( 'wp_ajax_wr2x_delete_full', 'wr2x_wp_ajax_wr2x_delete_full' );
		add_action( 'wp_ajax_wr2x_list_all', 'wr2x_wp_ajax_wr2x_list_all' );
		add_action( 'wp_ajax_wr2x_replace', 'wr2x_wp_ajax_wr2x_replace' );
		add_action( 'wp_ajax_wr2x_upload', 'wr2x_wp_ajax_wr2x_upload' );
		add_action( 'wp_ajax_wr2x_retina_details', 'wr2x_wp_ajax_wr2x_retina_details' );
		add_action( 'admin_head', 'wr2x_admin_head' );

		/**
		 * Client
		 */

		function wr2x_admin_head() {

		?>

		<script type="text/javascript">
			/* GENERATE RETINA IMAGES ACTION */

			var current;
			var maxPhpSize = <?php echo (int)ini_get('upload_max_filesize') * 1000000; ?>;
			var ids = [];
			var errors = 0;
			var ajax_action = "generate"; // generate | delete

			function wr2x_display_please_refresh() {
				wr2x_refresh_progress_status();
				jQuery('#wr2x_progression').html(jQuery('#wr2x_progression').html() + " - <?php echo _e( "<a href = '?page=ae_tools&view=issues&refresh=true'>Refresh</a> this page.", 'wp-retina-2x' ); ?>");
			}

			function wr2x_refresh_progress_status() {
				var errortext = "",
					percent;
				if (errors > 0) {
					errortext = ' - ' + errors + ' error(s)';
				}
				jQuery('#wr2x_progression').text(current + "/" + ids.length + " (" + Math.round(current / ids.length * 100) + "%)" + errortext);
			}

			function wr2x_do_next() {
				var data = {
					action: 'wr2x_' + ajax_action,
					attachmentId: ids[current - 1]
				};
				wr2x_refresh_progress_status();
				jQuery.post(ajaxurl, data, function (response) {
					try {
						reply = jQuery.parseJSON(response);
					} catch (e) {
						reply = null;
					}
					if (!reply || !reply.success)
						errors++;
					else {
						wr2x_refresh_media_sizes(reply.results);
						if (reply.results_full)
							wr2x_refresh_full(reply.results_full);
					}
					if (++current <= ids.length)
						wr2x_do_next();
					else {
						current--;
						wr2x_display_please_refresh();
					}
				}).fail(function () {
					errors++;
					if (++current <= ids.length)
						wr2x_do_next();
					else {
						current--;
						wr2x_display_please_refresh();
					}
				});
			}

			function wr2x_do_all() {
				current = 1;
				ids = [];
				errors = 0;
				var data = {
					action: 'wr2x_list_all',
					issuesOnly: 0
				};
				jQuery('#wr2x_progression').text("<?php _e( "Wait...", 'wp-retina-2x' ); ?>");
				jQuery.post(ajaxurl, data, function (response) {
					reply = jQuery.parseJSON(response);
					if (reply.success = false) {
						alert('Error: ' + reply.message);
						return;
					}
					if (reply.total == 0) {
						jQuery('#wr2x_progression').html("<?php _e( "Nothing to do ", 'wp-retina-2x' ); ?>");
						return;
					}
					ids = reply.ids;
					jQuery('#wr2x_progression').text(current + "/" + ids.length + " (" + Math.round(current / ids.length * 100) + "%)");
					wr2x_do_next();
				});
			}

			function wr2x_delete_all() {
				ajax_action = 'delete';
				wr2x_do_all();
			}

			function wr2x_generate_all() {
				ajax_action = 'generate';
				wr2x_do_all();
			}

			// Refresh the dashboard retina full with the results from the Ajax operation (Upload)
			function wr2x_refresh_full(results) {
				jQuery.each(results, function (id, html) {
					jQuery('#wr2x-fullsize-retina-upload-' + id).find('.dashicons').removeClass().addClass('dashicons dashicons-thumbs-up');
					jQuery('#wr2x-fullsize-retina-upload-' + id + ' img').attr('src', jQuery('#wr2x-info-full-' + id + ' img').attr('src') + '?' + Math.random());
					jQuery('#wr2x-fullsize-retina-upload-' + id + ' img').on('click', function (evt) {
						wr2x_delete_full(jQuery(evt.target).parents('.wr2x-file-row').attr('postid'));
					});
				});
			}

			// Refresh the dashboard media sizes with the results from the Ajax operation (Replace or Generate)
			function wr2x_refresh_media_sizes(results) {
				jQuery.each(results, function (id, html) {
					jQuery('#wr2x-info-' + id).html(html);
				});
			}

			function wr2x_generate(attachmentId, retinaDashboard) {
				var data = {
					action: 'wr2x_generate',
					attachmentId: attachmentId
				};
				jQuery('#wr2x_generate_button_' + attachmentId).text("<?php echo __( "Working...", 'wp-retina-2x' ); ?>");
				jQuery.post(ajaxurl, data, function (response) {
					var reply = jQuery.parseJSON(response);
					if (!reply.success) {
						alert(reply.message);
						return;
					}
					jQuery('#wr2x_generate_button_' + attachmentId).html("<?php echo __( "Generate ", 'wp-retina-2x' ); ?>");
					wr2x_refresh_media_sizes(reply.results);
				});
			}

			/* REPLACE FUNCTION */

			function wr2x_stop_propagation(evt) {
				evt.stopPropagation();
				evt.preventDefault();
			}

			function wr2x_delete_full(attachmentId) {
				var data = {
					action: 'wr2x_delete_full',
					isAjax: true,
					attachmentId: attachmentId
				};

				jQuery.post(ajaxurl, data, function (response) {
					var data = jQuery.parseJSON(response);
					if (data.success === false) {
						alert(data.message);
					} else {
						wr2x_refresh_full(data.results);
						wr2x_display_please_refresh();
					}
				});
			}

			function wr2x_load_details(attachmentId) {
				var data = {
					action: 'wr2x_retina_details',
					isAjax: true,
					attachmentId: attachmentId
				};

				jQuery.post(ajaxurl, data, function (response) {
					var data = jQuery.parseJSON(response);
					if (data.success === false) {
						alert(data.message);
					}
				});
			}

			function wr2x_filedropped(evt) {
				wr2x_stop_propagation(evt);
				var files = evt.dataTransfer.files;
				var count = files.length;
				if (count < 0) {
					return;
				}

				var wr2x_replace = jQuery(evt.target).parent().hasClass('wr2x-fullsize-replace');
				var wr2x_upload = jQuery(evt.target).parent().hasClass('wr2x-fullsize-retina-upload');

				function wr2x_handleReaderLoad(evt) {
					var attachmentId = evt.target.attachmentId;
					var fileData = evt.target.result;
					fileData = fileData.substr(fileData.indexOf('base64') + 7);
					var action = "";
					if (wr2x_replace) {
						action = 'wr2x_replace';
					} else if (wr2x_upload) {
						action = 'wr2x_upload';
					} else {
						alert("Unknown command. Contact the developer.");
					}
					var data = {
						action: action,
						isAjax: true,
						filename: evt.target.filename,
						data: fileData,
						attachmentId: evt.target.attachmentId
					};

					jQuery.post(ajaxurl, data, function (response) {
						var data = jQuery.parseJSON(response);
						jQuery('[postid=' + attachmentId + '] td').removeClass('wr2x-loading-file');
						jQuery('[postid=' + attachmentId + '] .wr2x-dragdrop').removeClass('wr2x-hover-drop');

						if (wr2x_replace) {
							var imgSelector = '[postid=' + attachmentId + '] .wr2x-info-thumbnail img';
							jQuery(imgSelector).attr('src', jQuery(imgSelector).attr('src') + '?' + Math.random());
						}
						if (wr2x_upload) {
							var imgSelector = '[postid=' + attachmentId + '] .wr2x-info-full img';
							jQuery(imgSelector).attr('src', jQuery(imgSelector).attr('src') + '?' + Math.random());
						}

						if (data.success === false) {
							alert(data.message);
						} else {
							if (wr2x_replace) {
								wr2x_refresh_media_sizes(data.results);
							} else if (wr2x_upload) {
								wr2x_refresh_full(data.results);
							}
						}
					});
				}

				var file = files[0];
				if (file.size > maxPhpSize) {
					jQuery(this).removeClass('wr2x-hover-drop');
					alert("Your PHP configuration only allows file upload of a maximum of " + (maxPhpSize / 1000000) + "MB.");
					return;
				}

				jQuery(evt.target).parents('td').addClass('wr2x-loading-file');
				var reader = new FileReader();
				reader.filename = file.name;
				reader.attachmentId = jQuery(evt.target).parents('.wr2x-file-row').attr('postid');
				reader.onload = wr2x_handleReaderLoad;
				reader.readAsDataURL(file);
			}

			jQuery(document).ready(function () {
				jQuery('.wr2x-dragdrop').on('dragenter', function (evt) {
					wr2x_stop_propagation(evt);
					jQuery(this).addClass('wr2x-hover-drop');
				});

				jQuery('.wr2x-dragdrop').on('dragover', function (evt) {
					wr2x_stop_propagation(evt);
					jQuery(this).addClass('wr2x-hover-drop');
				});

				jQuery('.wr2x-dragdrop').on('dragleave', function (evt) {
					wr2x_stop_propagation(evt);
					jQuery(this).removeClass('wr2x-hover-drop');
				});

				jQuery('.wr2x-dragdrop').on('dragexit', wr2x_stop_propagation);

				jQuery('.wr2x-dragdrop').each(function (index, elem) {
					this.addEventListener('drop', wr2x_filedropped);
				});

				jQuery('.wr2x-info-full img').on('click', function (evt) {
					wr2x_delete_full(jQuery(evt.target).parents('.wr2x-file-row').attr('postid'));
				});

			});
		</script>

		<?php

		}

		/**
		 * Server
		 */

		function wr2x_wp_ajax_wr2x_list_all( $issuesOnly ) {
			$issuesOnly = intval( $_POST['issuesOnly'] );
			if ( $issuesOnly == 1 ) {
				$ids = wr2x_get_issues();
				echo json_encode(
					array(
						'success' => true,
						'message' => "List of issues only.",
						'ids' => $ids,
						'total' => count( $ids )
					) );
				die;
			}
			$reply = array();
			try {
				$ids = array();
				$total = 0;
				global $wpdb;
				$postids = $wpdb->get_col( "
				SELECT p.ID
				FROM $wpdb->posts p
				WHERE post_status = 'inherit'
				AND post_type = 'attachment'
				AND ( post_mime_type = 'image/jpeg' OR
					post_mime_type = 'image/png' OR
					post_mime_type = 'image/gif' )
			" );
				foreach ($postids as $id) {
					array_push( $ids, $id );
					$total++;
				}
				echo json_encode(
					array(
						'success' => true,
						'message' => "List of everything.",
						'ids' => $ids,
						'total' => $total
					) );
				die;
			}
			catch (Exception $e) {
				echo json_encode(
					array(
						'success' => false,
						'message' => $e->getMessage()
					) );
				die;
			}
		}
		function wr2x_wp_ajax_wr2x_delete_full( $pleaseReturn = false ) {

			if ( !isset( $_POST['attachmentId'] ) ) {
				echo json_encode(
					array(
						'success' => false,
						'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
					)
				);
				die();
			}
			$attachmentId = intval( $_POST['attachmentId'] );
			$originalfile = get_attached_file( $attachmentId );
			$pathinfo = pathinfo( $originalfile );
			$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
			if ( $retina_file && file_exists( $retina_file ) )
				unlink( $retina_file );

			// RESULTS FOR RETINA DASHBOARD
			$info = wpr2x_html_get_basic_retina_info_full( $attachmentId, wr2x_retina_info( $attachmentId ) );
			$results[$attachmentId] = $info;

			// Return if that's not the final step.
			if ( $pleaseReturn )
				return $info;

			echo json_encode(
				array(
					'results' => $results,
					'success' => true,
					'message' => __( "Full retina file deleted.", 'wp-retina-2x' )
				)
			);
			die();
		}
		function wr2x_wp_ajax_wr2x_delete() {

			if ( !isset( $_POST['attachmentId'] ) ) {
				echo json_encode(
					array(
						'success' => false,
						'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
					)
				);
				die();
			}

			// Information for the retina version of the full-size
			$attachmentId = intval( $_POST['attachmentId'] );
			$results_full[$attachmentId] = wr2x_wp_ajax_wr2x_delete_full( true );

			wr2x_delete_attachment( $attachmentId );
			$meta = wp_get_attachment_metadata( $attachmentId );

			// RESULTS FOR RETINA DASHBOARD
			wr2x_update_issue_status( $attachmentId );
			$info = wpr2x_html_get_basic_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
			$results[$attachmentId] = $info;
			echo json_encode(
				array(
					'results' => $results,
					'results_full' => $results_full,
					'success' => true,
					'message' => __( "Retina files deleted.", 'wp-retina-2x' )
				)
			);
			die();
		}
		function wr2x_wp_ajax_wr2x_retina_details() {
			if ( !isset( $_POST['attachmentId'] ) ) {
				echo json_encode(
					array(
						'success' => false,
						'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
					)
				);
				die();
			}

			$attachmentId = intval( $_POST['attachmentId'] );
			$info = wpr2x_html_get_details_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
			echo json_encode(
				array(
					'result' => $info,
					'success' => true,
					'message' => __( "Details retrieved.", 'wp-retina-2x' )
				)
			);
			die();
		}
		function wr2x_wp_ajax_wr2x_generate() {
			if ( !isset( $_POST['attachmentId'] ) ) {
				echo json_encode(
					array(
						'success' => false,
						'message' => __( "The attachment ID is missing.", 'wp-retina-2x' )
					)
				);
				die();
			}

			$attachmentId = intval( $_POST['attachmentId'] );
			wr2x_delete_attachment( $attachmentId );
			$meta = wp_get_attachment_metadata( $attachmentId );
			wr2x_generate_images( $meta );

			// RESULTS FOR RETINA DASHBOARD
			$info = wpr2x_html_get_basic_retina_info( $attachmentId, wr2x_retina_info( $attachmentId ) );
			$results[$attachmentId] = $info;
			echo json_encode(
				array(
					'results' => $results,
					'success' => true,
					'message' => __( "Retina files generated.", 'wp-retina-2x' )
				)
			);
			die();
		}
		function wr2x_check_get_ajax_uploaded_file() {
			if ( !current_user_can('upload_files') ) {
				echo json_encode( array(
					'success' => false,
					'message' => __( "You do not have permission to upload files.", 'wp-retina-2x' )
				));
				die();
			}

			$data = $_POST['data'];

			// Create the file as a TMP
			if ( is_writable( sys_get_temp_dir() ) ) {
				$tmpfname = tempnam( sys_get_temp_dir(), "wpx_" );
			}
			else if ( is_writable( wr2x_get_upload_root() ) ) {
				if ( !file_exists( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp" ) )
					mkdir( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp" );
				$tmpfname = tempnam( trailingslashit( wr2x_get_upload_root() ) . "wr2x-tmp", "wpx_" );
			}

			if ( $tmpfname == null || $tmpfname == FALSE ) {
				$tmpdir = get_temp_dir();
				error_log( "Retina: The temporary directory could not be created." );
				echo json_encode( array(
					'success' => false,
					'message' => __( "The temporary directory could not be created.", 'wp-retina-2x' )
				));
				die;
			}

			$handle = fopen( $tmpfname, "w" );
			fwrite( $handle, base64_decode( $data ) );
			fclose( $handle );
			chmod( $tmpfname, 0664 );

			// Check if it is an image
			$file_info = getimagesize( $tmpfname );
			if ( empty( $file_info ) ) {
				unlink( $tmpfname );
				echo json_encode( array(
					'success' => false,
					'message' => __( "The file is not an image or the upload went wrong.", 'wp-retina-2x' )
				));
				die();
			}

			$filedata = wp_check_filetype_and_ext( $tmpfname, $_POST['filename'] );
			if ( $filedata["ext"] == "" ) {
				unlink( $current_file );
				echo json_encode( array(
					'success' => false,
					'message' => __( "You cannot use this file (wrong extension? wrong type?).", 'wp-retina-2x' )
				));
				die();
			}

			return $tmpfname;
		}
		function wr2x_wp_ajax_wr2x_upload() {
			$tmpfname = wr2x_check_get_ajax_uploaded_file();
			$attachmentId = (int) $_POST['attachmentId'];
			$meta = wp_get_attachment_metadata( $attachmentId );
			$current_file = get_attached_file( $attachmentId );
			$pathinfo = pathinfo( $current_file );
			$basepath = $pathinfo['dirname'];
			$retinafile = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];

			if ( file_exists( $retinafile ) )
				unlink( $retinafile );

			// Insert the new file and delete the temporary one
			list( $width, $height ) = getimagesize( $tmpfname );

			if ( !wr2x_are_dimensions_ok( $width, $height, $meta['width'] * 2, $meta['height'] * 2 ) ) {
				echo json_encode( array(
					'success' => false,
					'message' => "This image has a resolution of ${width}×${height} but your Full Size image requires a retina image of at least " . ( $meta['width'] * 2 ) . "x" . ( $meta['height'] * 2 ) . "."
				));
				die();
			}

			wr2x_vt_resize( $tmpfname, $meta['width'] * 2, $meta['height'] * 2, null, $retinafile );
			chmod( $retinafile, 0644 );
			unlink( $tmpfname );

			// Get the results
			$info = wr2x_retina_info( $attachmentId );
			wr2x_update_issue_status( $attachmentId );
			$results[$attachmentId] = wpr2x_html_get_basic_retina_info_full( $attachmentId, $info );

			echo json_encode( array(
				'success' => true,
				'results' => $results,
				'message' => __( "Uploaded successfully.", 'wp-retina-2x' )
			));
			die();
		}
		function wr2x_wp_ajax_wr2x_replace() {
			$tmpfname = wr2x_check_get_ajax_uploaded_file();
			$attachmentId = (int) $_POST['attachmentId'];
			$meta = wp_get_attachment_metadata( $attachmentId );
			$current_file = get_attached_file( $attachmentId );
			wr2x_delete_attachment( $attachmentId );
			$pathinfo = pathinfo( $current_file );
			$basepath = $pathinfo['dirname'];

			// Let's clean everything first
			if ( wp_attachment_is_image( $attachmentId ) ) {
				$sizes = wr2x_get_image_sizes();
				foreach ($sizes as $name => $attr) {
					if ( isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) && file_exists( trailingslashit( $basepath ) . $meta['sizes'][$name]['file'] ) ) {
						$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
						$pathinfo = pathinfo( $normal_file );
						$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];

						// Test if the file exists and if it is actually a file (and not a dir)
						// Some old WordPress Media Library are sometimes broken and link to directories
						if ( file_exists( $normal_file ) && is_file( $normal_file ) )
							unlink( $normal_file );
						if ( file_exists( $retina_file ) && is_file( $retina_file ) )
							unlink( $retina_file );
					}
				}
			}
			if ( file_exists( $current_file ) )
				unlink( $current_file );

			// Insert the new file and delete the temporary one
			rename( $tmpfname, $current_file );
			chmod( $current_file, 0644 );

			// Generate the images
			wp_update_attachment_metadata( $attachmentId, wp_generate_attachment_metadata( $attachmentId, $current_file ) );
			$meta = wp_get_attachment_metadata( $attachmentId );
			wr2x_generate_images( $meta );

			// Get the results
			$info = wr2x_retina_info( $attachmentId );
			$results[$attachmentId] = wpr2x_html_get_basic_retina_info( $attachmentId, $info );

			echo json_encode( array(
				'success' => true,
				'results' => $results,
				'message' => __( "Replaced successfully.", 'wp-retina-2x' )
			));
			die();
		}

	}

	/*
	 * Functions
	 */

	function wr2x_wp_calculate_image_srcset( $srcset, $size ) {
		$count            = 0;
		$total            = 0;
		$retinized_srcset = $srcset;
		if ( empty( $srcset ) )
			return $srcset;
		foreach ( $srcset as $s => $cfg ) {
			$total++;
			$retina = wr2x_get_retina_from_url( $cfg['url'] );
			if ( !empty( $retina ) ) {
				$count++;
				$retinized_srcset[(int) $s * 2] = array(
					'url' => $retina,
					'descriptor' => 'w',
					'value' => (int) $s * 2
				);
			}
		}
		return $retinized_srcset;
	}
	function wr2x_vt_resize( $file_path, $width, $height, $crop, $newfile, $customCrop = false ) {
		$crop_params      = $crop == '1' ? true : $crop;
		$orig_size        = getimagesize( $file_path );
		$image_src[0]     = $file_path;
		$image_src[1]     = $orig_size[0];
		$image_src[2]     = $orig_size[1];
		$file_info        = pathinfo( $file_path );
		$newfile_info     = pathinfo( $newfile );
		$extension        = '.' . $newfile_info['extension'];
		$no_ext_path      = $file_info['dirname'] . '/' . $file_info['filename'];
		$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . "-tmp" . $extension;
		$image            = wp_get_image_editor( $file_path );

		if ( is_wp_error( $image ) ) {
			error_log( "Resize failure: " . $image->get_error_message() );
			return null;
		}

		// Resize or use Custom Crop
		if ( !$customCrop )
			$image->resize( $width, $height, $crop_params );
		else
			$image->crop( $customCrop['x'] * $customCrop['scale'], $customCrop['y'] * $customCrop['scale'], $customCrop['w'] * $customCrop['scale'], $customCrop['h'] * $customCrop['scale'], $width, $height, false );

		$quality = 80;
		if ( is_numeric( $quality ) )
			$image->set_quality( intval( $quality ) );
		$saved = $image->save( $cropped_img_path );
		if ( rename( $saved['path'], $newfile ) )
			$cropped_img_path = $newfile;
		else {
			trigger_error( "Retina: Could not move " . $saved['path'] . " to " . $newfile . ".", E_WARNING );
			error_log( "Retina: Could not move " . $saved['path'] . " to " . $newfile . "." );
			return null;
		}
		$new_img_size = getimagesize( $cropped_img_path );
		$new_img      = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
		$vt_image     = array(
			'url' => $new_img,
			'width' => $new_img_size[0],
			'height' => $new_img_size[1]
		);
		return $vt_image;
	}
	function wr2x_are_dimensions_ok( $width, $height, $retina_width, $retina_height ) {
		$w_margin = $width - $retina_width;
		$h_margin = $height - $retina_height;
		return ( $w_margin >= -2 && $h_margin >= -2 );
	}
	function wr2x_update_issue_status( $attachmentId, $issues = null, $info = null ) {
		if ( $issues == null )
			$issues = wr2x_get_issues();
		if ( $info == null )
			$info = wr2x_retina_info( $attachmentId );
		$consideredIssue = in_array( $attachmentId, $issues );
		$realIssue       = wr2x_info_has_issues( $info );
		if ( $consideredIssue && !$realIssue )
			wr2x_remove_issue( $attachmentId );
		else if ( !$consideredIssue && $realIssue )
			wr2x_add_issue( $attachmentId );
		return $realIssue;
	}
	function wr2x_get_issues() {
		$issues = get_transient( 'wr2x_issues' );
		if ( !$issues || !is_array( $issues ) ) {
			$issues = array();
			set_transient( 'wr2x_issues', $issues );
		}
		return $issues;
	}
	function wr2x_info_has_issues( $info ) {
		foreach ( $info as $aindex => $aval ) {
			if ( is_array( $aval ) || $aval == 'PENDING' )
				return true;
		}
		return false;
	}
	function wr2x_calculate_issues() {
		global $wpdb;
		$postids = $wpdb->get_col( "
		SELECT p.ID FROM $wpdb->posts p
		WHERE post_status = 'inherit'
		AND post_type = 'attachment'" . wr2x_create_sql_if_wpml_original() . "
		AND ( post_mime_type = 'image/jpeg' OR
			post_mime_type = 'image/jpg' OR
			post_mime_type = 'image/png' OR
			post_mime_type = 'image/gif' )
	" );
		$issues  = array();
		foreach ( $postids as $id ) {
			$info = wr2x_retina_info( $id );
			if ( wr2x_info_has_issues( $info ) )
				array_push( $issues, $id );

		}
		set_transient( 'wr2x_issues', $issues );
	}
	function wr2x_add_issue( $attachmentId ) {
		$issues = wr2x_get_issues();
		if ( !in_array( $attachmentId, $issues ) ) {
			array_push( $issues, $attachmentId );
			set_transient( 'wr2x_issues', $issues );
		}
		return $issues;
	}
	function wr2x_remove_issue( $attachmentId, $onlyIgnore = false ) {
		$issues = array_diff( wr2x_get_issues(), array(
			$attachmentId
		) );
		set_transient( 'wr2x_issues', $issues );
		return $issues;
	}
	function wpr2x_html_get_basic_retina_info_full( $attachmentId, $retina_info ) {
		if ( ( isset( $retina_info ) && isset( $retina_info['full-size'] ) ) ) {
			$status = $retina_info['full-size'];
		}
		if ( $status == 'EXISTS' ) {
			return '<ul class="retina-info"><li class="retina-exists" title="full-size"><span class="dashicons dashicons-yes"></span></li></ul>';
		} else if ( is_array( $status ) ) {
			return '<ul class="retina-info"><li class="retina-issue" title="full-size"><span class="dashicons dashicons-no-alt"></span></li></ul>';
		}
		return $status;
	}
	function wpr2x_html_get_basic_retina_info_full_sign( $attachmentId, $retina_info ) {
		if ( ( isset( $retina_info ) && isset( $retina_info['full-size'] ) ) ) {
			$status = $retina_info['full-size'];
		}
		if( isset( $status ) ) {
			if ( $status == 'EXISTS' ) {
				return '<span class="dashicons dashicons-thumbs-up"></span>';
			} else if ( is_array( $status ) ) {
				return '<span class="dashicons dashicons-upload"></span>';
			}
		} else {
			return '<span class="dashicons dashicons-no"></span>';
		}
		return $status;
	}
	function wr2x_size_shortname( $name ) {
		$name  = preg_split( '[_-]', $name );
		$short = strtoupper( substr( $name[0], 0, 1 ) );
		if ( count( $name ) > 1 )
			$short .= strtoupper( substr( $name[1], 0, 1 ) );
		return $short;
	}
	function wr2x_format_title( $i, $size ) {
		return $i . ' (' . ( $size['width'] * 2 ) . 'x' . ( $size['height'] * 2 ) . ')';
	}
	function wpr2x_html_get_basic_retina_info( $attachmentId, $retina_info ) {
		$sizes  = wr2x_get_active_image_sizes();
		$result = '<ul class="retina-info">';
		foreach ( $sizes as $i => $size ) {
			$status = ( isset( $retina_info ) && isset( $retina_info[$i] ) ) ? $retina_info[$i] : null;
			if ( is_array( $status ) )
				$result .= '<li class="retina-issue" title="' . wr2x_format_title( $i, $size ) . '">' . wr2x_size_shortname( $i ) . '</li>';
			else if ( $status == 'EXISTS' )
				$result .= '<li class="retina-exists" title="' . wr2x_format_title( $i, $size ) . '">' . wr2x_size_shortname( $i ) . '</li>';
			else if ( $status == 'PENDING' )
				$result .= '<li class="retina-pending" title="' . wr2x_format_title( $i, $size ) . '">' . wr2x_size_shortname( $i ) . '</li>';
			else if ( $status == 'MISSING' )
				$result .= '<li class="retina-missing" title="' . wr2x_format_title( $i, $size ) . '">' . wr2x_size_shortname( $i ) . '</li>';
			else {
				error_log( "Retina: This status is not recognized: " . $status );
			}
		}
		$result .= '</ul>';
		return $result;
	}
	function wpr2x_html_get_details_retina_info( $post, $retina_info ) {

		$sizes    = wr2x_get_image_sizes();
		$total    = 0;
		$possible = 0;
		$issue    = 0;
		$retina   = 0;

		$postinfo        = get_post( $post, OBJECT );
		$meta            = wp_get_attachment_metadata( $post );
		$fullsize_file   = get_attached_file( $post );
		$pathinfo_system = pathinfo( $fullsize_file );
		$pathinfo        = pathinfo( $meta['file'] );
		$uploads         = wp_upload_dir();
		$basepath_url    = trailingslashit( $uploads['baseurl'] ) . $pathinfo['dirname'];

		// Full Size
		$sizes['full-size']['file']           = $pathinfo['basename'];
		$sizes['full-size']['width']          = $meta['width'];
		$sizes['full-size']['height']         = $meta['height'];
		$meta['sizes']['full-size']['file']   = $pathinfo['basename'];
		$meta['sizes']['full-size']['width']  = $meta['width'];
		$meta['sizes']['full-size']['height'] = $meta['height'];

		$result = "<p>This screen displays all the image sizes set-up by your WordPress configuration with the Retina details.</p>";
		$result .= "<br /><a target='_blank' href='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "'><img src='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "' height='100px' style='float: left; margin-right: 10px;' /></a><div class='base-info'>";
		$result .= "Title: <b>" . ( $postinfo->post_title ? $postinfo->post_title : '<i>Untitled</i>' ) . "</b><br />";
		$result .= "Full-size: <b>" . $meta['width'] . "×" . $meta['height'] . "</b><br />";
		$result .= "Image URL: <a target='_blank' href='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "'>" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "</a><br />";
		$result .= "Image Path: " . $fullsize_file . "<br />";
		$result .= "</div><div style='clear: both;'></div><br />";
		$result .= "<div class='scrollable-info'>";

		foreach ( $sizes as $i => $sizemeta ) {
			$total++;
			$normal_file_system = "";
			$retina_file_system = "";
			$normal_file        = "";
			$retina_file        = "";
			$width              = "";
			$height             = "";

			if ( !isset( $meta['sizes'] ) ) {
				$statusText = __( "The metadata is broken! This is not related to the retina plugin. You should probably use a plugin to re-generate the missing metadata and images.", 'wp-retina-2x' );
				$status     = "MISSING";
			} else if ( !isset( $meta['sizes'][$i] ) ) {
				$statusText = sprintf( __( "The image size '%s' could not be found. You probably changed your image sizes but this specific image was not re-build. This is not related to the retina plugin. You should probably use a plugin to re-generate the missing metadata and images.", 'wp-retina-2x' ), $i );
				$status     = "MISSING";
			} else {
				$normal_file_system = trailingslashit( $pathinfo_system['dirname'] ) . $meta['sizes'][$i]['file'];
				$retina_file_system = wr2x_get_retina( $normal_file_system );
				$normal_file        = trailingslashit( $basepath_url ) . $meta['sizes'][$i]['file'];
				$retina_file        = wr2x_get_retina_from_url( $normal_file );
				$status             = ( isset( $retina_info ) && isset( $retina_info[$i] ) ) ? $retina_info[$i] : null;
				$width              = $meta['sizes'][$i]['width'];
				$height             = $meta['sizes'][$i]['height'];
			}

			$result .= "<h3>";

			// Status Icon
			if ( is_array( $status ) && $i == 'full-size' ) {
				$result .= '<div class="retina-status-icon retina-missing"></div>';
				$statusText = sprintf( __( "The retina version of the Full-Size image is missing.<br />Full Size Retina has been checked in the Settings and this image is therefore required.<br />Please drag & drop an image of at least <b>%dx%d</b> in the <b>Full-Size Retina Upload</b> column.", 'wp-retina-2x' ), $status['width'], $status['height'] );
			} else if ( is_array( $status ) ) {
				$result .= '<div class="retina-status-icon retina-issue"></div>';
				$statusText = sprintf( __( "The Full-Size image is too small (<b>%dx%d</b>) and this size cannot be generated.<br />Please upload an image of at least <b>%dx%d</b>.", 'wp-retina-2x' ), $meta['width'], $meta['height'], $status['width'], $status['height'] );
				$issue++;
			} else if ( $status == 'EXISTS' ) {
				$result .= '<div class="retina-status-icon retina-exists"></div>';
				$statusText = "";
				$retina++;
			} else if ( $status == 'PENDING' ) {
				$result .= '<div class="retina-status-icon retina-pending"></div>';
				$statusText = __( "The retina image can be created. Please use the 'GENERATE' button.", 'wp-retina-2x' );
				$possible++;
			} else if ( $status == 'MISSING' ) {
				$result .= '<div class="retina-status-icon retina-missing"></div>';
				$statusText = __( "The standard image normally created by WordPress is missing.", 'wp-retina-2x' );
				$total--;
			}

			$result .= "Size: $i</h3><p>$statusText</p>";

			if ( !is_array( $status ) && $status !== 'MISSING' ) {
				$result .= "<table><tr><th>Normal (" . $width . "×" . $height . ")</th><th>Retina 2x (" . $width * 2 . "×" . $height * 2 . ")</th></tr><tr><td><a target='_blank' href='$normal_file'><img src='$normal_file' width='100'></a></td><td><a target='_blank' href='$retina_file'><img src='$retina_file' width='100'></a></td></tr></table>";
				$result .= "<p><small>";
				$result .= "Image URL: <a target='_blank' href='$normal_file'>$normal_file</a><br />";
				$result .= "Retina URL: <a target='_blank' href='$retina_file'>$retina_file</a><br />";
				$result .= "Image Path: $normal_file_system<br />";
				$result .= "Retina Path: $retina_file_system<br />";
				$result .= "</small></p>";
			}
		}
		$result .= "</table>";
		$result .= "</div>";
		return $result;
	}
	function wr2x_get_upload_root() {
		$uploads = wp_upload_dir();
		return $uploads['basedir'];
	}
	function wr2x_get_upload_root_url() {
		$uploads = wp_upload_dir();
		return $uploads['baseurl'];
	}
	function wr2x_get_wordpress_root() {
		return ABSPATH;
	}
	function wr2x_get_retina( $file ) {
		$pathinfo = pathinfo( $file );
		if ( empty( $pathinfo ) || !isset( $pathinfo['dirname'] ) ) {
			if ( empty( $file ) ) {
				error_log( "An empty filename was given to wr2x_get_retina()." );
			} else {
				error_log( "Pathinfo is null for " . $file . "." );
			}
			return null;
		}
		$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . ( isset( $pathinfo['extension'] ) ? $pathinfo['extension'] : "" );
		if ( file_exists( $retina_file ) )
			return $retina_file;
		return null;
	}
	function wr2x_get_retina_from_url( $url ) {
		$filepath = wr2x_from_url_to_system( $url );
		if ( empty( $filepath ) ) {
			return null;
		}
		$system_retina = wr2x_get_retina( $filepath );
		if ( empty( $system_retina ) ) {
			return null;
		}
		$retina_url = wr2x_rewrite_url_to_retina( $url );
		return $retina_url;
	}
	function wr2x_from_url_to_system( $url ) {
		$img_pathinfo = wr2x_get_pathinfo_from_image_src( $url );
		$filepath     = trailingslashit( wr2x_get_wordpress_root() ) . $img_pathinfo;
		if ( file_exists( $filepath ) )
			return $filepath;
		$filepath = trailingslashit( wr2x_get_upload_root() ) . $img_pathinfo;
		if ( file_exists( $filepath ) )
			return $filepath;
		return null;
	}
	function wr2x_rewrite_url_to_retina( $url ) {
		$whereisdot = strrpos( $url, '.' );
		$url        = substr( $url, 0, $whereisdot ) . wr2x_retina_extension() . substr( $url, $whereisdot + 1 );
		return $url;
	}
	function wr2x_get_pathinfo_from_image_src( $image_src ) {
		$uploads_url = trailingslashit( wr2x_get_upload_root_url() );
		if ( strpos( $image_src, $uploads_url ) === 0 )
			return ltrim( substr( $image_src, strlen( $uploads_url ) ), '/' );
		else if ( strpos( $image_src, wp_make_link_relative( $uploads_url ) ) === 0 )
			return ltrim( substr( $image_src, strlen( wp_make_link_relative( $uploads_url ) ) ), '/' );
		$img_info = parse_url( $image_src );
		return ltrim( $img_info['path'], '/' );
	}
	function wr2x_get_image_sizes() {
		$sizes = array();
		global $_wp_additional_image_sizes;
		foreach ( get_intermediate_image_sizes() as $s ) {
			$crop = false;
			if ( isset( $_wp_additional_image_sizes[$s] ) ) {
				$width  = intval( $_wp_additional_image_sizes[$s]['width'] );
				$height = intval( $_wp_additional_image_sizes[$s]['height'] );
				$crop   = $_wp_additional_image_sizes[$s]['crop'];
			} else {
				$width  = get_option( $s . '_size_w' );
				$height = get_option( $s . '_size_h' );
				$crop   = get_option( $s . '_crop' );
			}
			$sizes[$s] = array(
				'width' => $width,
				'height' => $height,
				'crop' => $crop
			);
		}
		return $sizes;
	}
	function wr2x_get_active_image_sizes() {
		$sizes        = wr2x_get_image_sizes();
		$active_sizes = array();
		foreach ( $sizes as $name => $attr ) {
			$validSize = !empty( $attr['width'] ) || !empty( $attr['height'] );
			if ( $validSize ) {
				$active_sizes[$name] = $attr;
			}
		}
		return $active_sizes;
	}
	function wr2x_is_wpml_installed() {
		return function_exists( 'icl_object_id' ) && !class_exists( 'Polylang' );
	}
	function wr2x_create_sql_if_wpml_original() {
		$whereIsOriginal = "";
		if ( wr2x_is_wpml_installed() ) {
			global $wpdb;
			global $sitepress;
			$tbl_wpml        = $wpdb->prefix . "icl_translations";
			$language        = $sitepress->get_default_language();
			$whereIsOriginal = " AND p.ID IN (SELECT element_id FROM $tbl_wpml WHERE element_type = 'post_attachment' AND language_code = '$language') ";
		}
		return $whereIsOriginal;
	}
	function wr2x_get_attachment_id( $file ) {
		$query = array(
			'post_type' => 'attachment',
			'meta_query' => array(
				array(
					'key' => '_wp_attached_file',
					'value' => ltrim( $file, '/' )
				)
			)
		);
		$posts = get_posts( $query );
		foreach ( $posts as $post )
			return $post->ID;
		return false;
	}
	function wr2x_retina_extension() {
		return '@2x.';
	}
	function wr2x_is_image_meta( $meta ) {
		if ( !isset( $meta ) )
			return false;
		if ( !isset( $meta['sizes'] ) )
			return false;
		if ( !isset( $meta['width'], $meta['height'] ) ) {
			return false;
		}
		return true;
	}
	function wr2x_retina_info( $id ) {
		$result = array();
		$meta   = wp_get_attachment_metadata( $id );
		if ( !wr2x_is_image_meta( $meta ) )
			return $result;
		$original_width  = $meta['width'];
		$original_height = $meta['height'];
		$sizes           = wr2x_get_image_sizes();
		$required_files  = true;
		$originalfile    = get_attached_file( $id );
		$pathinfo        = pathinfo( $originalfile );
		$basepath        = $pathinfo['dirname'];

		// Full-Size (if required in the settings)
		$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
		if ( $retina_file && file_exists( $retina_file ) )
			$result['full-size'] = 'EXISTS';
		else if ( $retina_file )
			$result['full-size'] = array(
			'width' => $original_width * 2,
			'height' => $original_height * 2
		);

		if ( $sizes ) {
			foreach ( $sizes as $name => $attr ) {
				$validSize = !empty( $attr['width'] ) || !empty( $attr['height'] );
				if ( !$validSize ) {
					continue;
				}
				// Check if the file related to this size is present
				$pathinfo    = null;
				$retina_file = null;

				if ( isset( $meta['sizes'][$name]['width'] ) && isset( $meta['sizes'][$name]['height'] ) && isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) && file_exists( trailingslashit( $basepath ) . $meta['sizes'][$name]['file'] ) ) {
					$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
					$pathinfo    = pathinfo( $normal_file );
					$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
				}
				// None of the file exist
				else {
					$result[$name]  = 'MISSING';
					$required_files = false;
					continue;
				}

				// The retina file exists
				if ( $retina_file && file_exists( $retina_file ) ) {
					$result[$name] = 'EXISTS';
					continue;
				}
				// The size file exists
				else if ( $retina_file )
					$result[$name] = 'PENDING';

				// The retina file exists
				$required_width  = $meta['sizes'][$name]['width'] * 2;
				$required_height = $meta['sizes'][$name]['height'] * 2;
				if ( !wr2x_are_dimensions_ok( $original_width, $original_height, $required_width, $required_height ) ) {
					$result[$name] = array(
						'width' => $required_width,
						'height' => $required_height
					);
				}
			}
		}
		return $result;
	}
	function wr2x_delete_attachment( $attach_id ) {
		$meta = wp_get_attachment_metadata( $attach_id );
		wr2x_delete_images( $meta );
		wr2x_remove_issue( $attach_id );
	}
	function wr2x_wp_generate_attachment_metadata( $meta ) {
		if ( wr2x_is_image_meta( $meta ) )
			wr2x_generate_images( $meta );
		return $meta;
	}
	function wr2x_generate_images( $meta ) {

		global $_wp_additional_image_sizes;
		$sizes = wr2x_get_image_sizes();
		if ( !isset( $meta['file'] ) )
			return;
		$originalfile      = $meta['file'];
		$uploads           = wp_upload_dir();
		$pathinfo          = pathinfo( $originalfile );
		$original_basename = $pathinfo['basename'];
		$basepath          = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];
		$issue             = false;
		$id                = wr2x_get_attachment_id( $meta['file'] );

		foreach ( $sizes as $name => $attr ) {
			$normal_file = "";

			// Is the file related to this size there?
			$pathinfo    = null;
			$retina_file = null;

			if ( isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) ) {
				$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
				$pathinfo    = pathinfo( $normal_file );
				$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
			}

			if ( $retina_file && file_exists( $retina_file ) ) {
				continue;
			}
			if ( $retina_file ) {
				$originalfile = trailingslashit( $pathinfo['dirname'] ) . $original_basename;

				if ( !file_exists( $originalfile ) ) {
					return $meta;
				}

				// Maybe that new image is exactly the size of the original image.
				// In that case, let's make a copy of it.
				if ( $meta['sizes'][$name]['width'] * 2 == $meta['width'] && $meta['sizes'][$name]['height'] * 2 == $meta['height'] ) {
					copy( $originalfile, $retina_file );
				}
				// Otherwise let's resize (if the original size is big enough).
				else if ( wr2x_are_dimensions_ok( $meta['width'], $meta['height'], $meta['sizes'][$name]['width'] * 2, $meta['sizes'][$name]['height'] * 2 ) ) {
					// Change proposed by Nicscott01, slighlty modified by Jordy (+isset)
					// (https://wordpress.org/support/topic/issue-with-crop-position?replies=4#post-6200271)
					$crop       = isset( $_wp_additional_image_sizes[$name] ) ? $_wp_additional_image_sizes[$name]['crop'] : true;
					$customCrop = null;

					// Support for Manual Image Crop
					// If the size of the image was manually cropped, let's keep it.
					if ( class_exists( 'ManualImageCrop' ) && isset( $meta['micSelectedArea'] ) && isset( $meta['micSelectedArea'][$name] ) && isset( $meta['micSelectedArea'][$name]['scale'] ) ) {
						$customCrop = $meta['micSelectedArea'][$name];
					}
					$image = wr2x_vt_resize( $originalfile, $meta['sizes'][$name]['width'] * 2, $meta['sizes'][$name]['height'] * 2, $crop, $retina_file, $customCrop );
				}
				if ( !file_exists( $retina_file ) ) {
					$issue = true;
				} else {
					do_action( 'wr2x_retina_file_added', $id, $retina_file, $name );
				}
			}
		}

		// Checks attachment ID + issues
		if ( !$id )
			return $meta;
		if ( $issue )
			wr2x_add_issue( $id );
		else
			wr2x_remove_issue( $id );
		return $meta;
	}
	function wr2x_delete_images( $meta ) {
		if ( !wr2x_is_image_meta( $meta ) )
			return $meta;
		$sizes = $meta['sizes'];
		if ( !$sizes || !is_array( $sizes ) )
			return $meta;
		$originalfile = $meta['file'];
		$id           = wr2x_get_attachment_id( $originalfile );
		$pathinfo     = pathinfo( $originalfile );
		$uploads      = wp_upload_dir();
		$basepath     = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];
		foreach ( $sizes as $name => $attr ) {
			$pathinfo    = pathinfo( $attr['file'] );
			$retina_file = $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
			if ( file_exists( trailingslashit( $basepath ) . $retina_file ) ) {
				$fullpath = trailingslashit( $basepath ) . $retina_file;
				unlink( $fullpath );
				do_action( 'wr2x_retina_file_removed', $id, $retina_file );
			}
		}
		// Remove full-size if there is any
		$pathinfo    = pathinfo( $originalfile );
		$retina_file = $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
		if ( file_exists( trailingslashit( $basepath ) . $retina_file ) ) {
			$fullpath = trailingslashit( $basepath ) . $retina_file;
			unlink( $fullpath );
			do_action( 'wr2x_retina_file_removed', $id, $retina_file );
		}
		return $meta;
	}
	function wr2x_validate_src( $src ) {
		if ( preg_match( "/^data:/i", $src ) )
			return null;
		return $src;
	}

}
