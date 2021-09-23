<?php

/**
 * AE Autocomplete
 *
 * @class 		AE_Autocomplete
 * @version		1.2.0
 * @package		AE/Modules/Classes
 * @category	Class
 * @author 		Ascripta
 */

class AE_Autocomplete {

	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the plugin is loaded or can be loaded.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor
	 *
	 * Initialize the framework using the class functions.
	 */
	public function __construct() {

		if( AE_Admin_Settings::get_option( 'autocomplete', 'ae_engine', 'on' ) == 'on' ){

			// Load scripts and stylesheets.
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

			// Replace the default Wordpress search.
			add_action( 'wp_footer', array( $this, 'autocomplete' ) );

		}

	}

	/**
	 * Enqueue the JavaScript.
	 */
	public function scripts_and_styles() {

		wp_enqueue_script( 'autocomplete', ASCRIPTA_ENGINE_INC_URL . 'autocomplete/autocomplete.min.js' );

	}

	/**
	 * Replace the default Wordpress search.
	 * 
	 * @return string The generated HTML content.
	 */
	public function autocomplete() {

		// Support for post types.
		$post_types = array();
		foreach ( get_post_types( '', 'names' ) as $type ) {
			 if ( in_array( $type, apply_filters( 'ascripta_autocomplete_post_types', array( 'post', 'page' ) ) ) ) {
				$post_types[] = $type;
			 }
		}
		
		// Create a new Wordpress query. 
		$query = new WP_Query( array(
		   'post_type'      => $post_types,
		   'post_status'    => 'publish',
		   'posts_per_page' => -1,
		) );
		
		// Go through the query and add the titles to the list.
		$post_ids = array();
		while ( $query->have_posts()) {
			$query->the_post();
			$post_ids[] = get_the_title();
		}

		// Convert the array into a comma sepparated list.
		$post_ids = "'" . implode("', '", $post_ids) . "'";
		
		// Generate the script and place it in the footer. ?>
		<script>
	        var autocomplete = new autoComplete({
	            selector: '#s',
	            minChars: 1,
	            source  : function(term, suggest){
	                term = term.toLowerCase();
	                var choices = [<?php echo $post_ids; ?>];
	                var suggestions = [];
	                for ( i = 0 ; i < choices.length ; i++ ) {
	                    if ( ~choices[i].toLowerCase().indexOf(term) ){
							suggestions.push( choices[i] );
	                    }
	                }
	                suggest(suggestions);
	            }
	        });
	    </script>

	<?php }

}

return new AE_Autocomplete;
