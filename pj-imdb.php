<?php
/*
Plugin Name: PJ IMDB
Plugin URI: http://pamjad.me/pj-imdb/
Description: fetch the information of the movies from IMDB databse
Version: 1.0
Author: Pouriya Amjadzadeh
Author URI: http://pamjad.me
Text Domain: pj_imdb
Domain Path: /langs/
*/

## Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

## Load TextDomin
add_action( 'init', 'pj_imdb_textdomain' );
function pj_imdb_textdomain() {
	load_plugin_textdomain( 'pj_imdb', false, dirname( plugin_basename( __FILE__ ) ) . '/langs' ); 
}

## Load Style in WordPress
add_action('admin_enqueue_scripts', 'pj_imdb_style');
function pj_imdb_style() {
	wp_enqueue_style('admin-styles', plugin_dir_url( __FILE__ ).'/assets/style.css');
}

## Load Metabox Values
function imdb($value) {
	global $post;
    $custom_field = get_post_meta( $post->ID, 'imdb_'.$value, true );
    if ( !empty( $custom_field ) )
		return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );
    return false;
}

## Register Metabox
add_action( 'add_meta_boxes', 'pj_imdb_metabox' );
function pj_imdb_metabox() {
	add_meta_box('pj-imdb',__('Fetch info from IMDB','pj_imdb'),'pjimdb_callback','post','normal','high');
}
function pjimdb_callback($post) {
	wp_nonce_field( 'pj_imdb_nonce', 'imdb_nonce' );?>
	<div class="imdb-box">
		<div class="head"><?php _e('Fetch info from IMDB','pj_imdb'); ?> <i class="logo"></i></div>
		<div class="search">
			<div>
				<input type="text" name="title" placeholder="<?php _e('Title','pj_imdb'); ?>">
				<input type="number" min="1950" max="<?php echo date('Y'); ?>" name="year" placeholder="<?php _e('Year','pj_imdb'); ?>">
				<input type="text" name="imdb" placeholder="<?php _e('IMDB ID','pj_imdb'); ?>" disabled="disabled">
			</div>
			<a id="sendAjax"><img src="<?php echo plugin_dir_url( __FILE__ )?>assets/search.png"></a>
		</div>
	<?php
	if(imdb('imdbID') != '') : ?>
		<div id="result" style="display:block">
			<img src="<?php echo imdb('poster')?>">
			<div class="content">
				<ul>
					<li><span><?php _e('Title','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('title') )?></li>
					<li><span><?php _e('Year','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('year') )?></li>
					<li><span><?php _e('Runtime','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('time') )?></li>
					<li><span><?php _e('Genre','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('genre') )?></li>
					<li><span><?php _e('Director','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('director') )?></li>
					<li><span><?php _e('Actors','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('actors') )?></li>
					<li><span><?php _e('Country','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('country') )?></li>
					<li><span><?php _e('Rating','pj_imdb'); ?> :</span> <?php echo esc_html( imdb('rating') )?></li>
				</ul>
			</div>
		</div>
	<?php
	else :
		echo '<div id="result"></div>';
	endif;
	?>
	</div>
<?php
}
## Load Ajax Call
add_action( 'admin_footer', 'pj_imdb_get_javascript' );
function pj_imdb_get_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		$("#sendAjax").click(function(){
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				'action': 'pj_imdb_get',
				'title' : $('.search [name=title]').val(),
				'year' : $('.search [name=year]').val(),
				'imdb' : $('.search [name=imdb]').val(),
			},
			beforeSend : function() {
				$("#sendAjax").html('<img src="<?php echo plugin_dir_url( __FILE__ )?>assets/loading.svg" width="20" height="20">');
			},
			success: function (response) {
				$("#sendAjax").html('<img src="<?php echo plugin_dir_url( __FILE__ )?>assets/search.png">');
				$('#result').fadeIn().html(response);
			}
		});
		return false;
		});
	});
	</script>
<?php
}
## Load Ajax Callback
add_action( 'wp_ajax_pj_imdb_get', 'pj_imdb_get_callback' );
function pj_imdb_get_callback() {
	$name = (isset($_POST['title'])) ? $_POST['title'] : '';
	$year = (isset($_POST['year'])) ? $_POST['year'] : '';
	$imdb = (isset($_POST['imdb'])) ? $_POST['imdb'] : '';
	$isTT = substr($imdb, 0, 2);
	
	if($year != '')
		$year = intval($year);
	if ( $imdb != '' && strlen( $imdb ) < 9 || $isTT != 'tt') {
		echo '<p class="error">'.__('Check your IMDB ID!','pj_imdb').'</p>';
		exit;
	}
	
	$imdbjson = file_get_contents("http://www.omdbapi.com/?t=$name&y=$year&i=$imdb");
	$dimdb = json_decode($imdbjson, true);

	if($dimdb['Response'] == 'True') { ?>
		<img src="<?php echo esc_url($dimdb['Poster'])?>">
		<div class="content">
			<ul>
				<li><span><?php _e('Title','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Title'])?></li>
				<li><span><?php _e('Year','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Year'])?></li>
				<li><span><?php _e('Runtime','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Runtime'])?></li>
				<li><span><?php _e('Genre','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Genre'])?></li>
				<li><span><?php _e('Director','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Director'])?></li>
				<li><span><?php _e('Actors','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Actors'])?></li>
				<li><span><?php _e('Country','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['Country'])?></li>
				<li><span><?php _e('Rating','pj_imdb'); ?> :</span> <?php echo esc_html($dimdb['imdbRating'])?></li>
			</ul>
		</div>
		<input type="hidden" name="title_imdb" value="<?php echo esc_attr($dimdb['Title'])?>"/>
		<input type="hidden" name="year_imdb" value="<?php echo esc_attr($dimdb['Year'])?>"/>
		<input type="hidden" name="time_imdb" value="<?php echo esc_attr($dimdb['Runtime'])?>"/>
		<input type="hidden" name="genre_imdb" value="<?php echo esc_attr($dimdb['Genre'])?>"/>
		<input type="hidden" name="director_imdb" value="<?php echo esc_attr($dimdb['Director'])?>"/>
		<input type="hidden" name="actors_imdb" value="<?php echo esc_attr($dimdb['Actors'])?>"/>
		<input type="hidden" name="country_imdb" value="<?php echo esc_attr($dimdb['Country'])?>"/>
		<input type="hidden" name="rating_imdb" value="<?php echo esc_attr($dimdb['imdbRating'])?>"/>
		<input type="hidden" name="poster_imdb" value="<?php echo esc_attr($dimdb['Poster'])?>"/>
		<input type="hidden" name="imdbID_imdb" value="<?php echo esc_attr($dimdb['imdbID'])?>"/>
	<?php
		wp_nonce_field( 'imdb_nonce_action', 'nonce_imdb' );
	}
	else
		echo '<p class="error">'.__('Check inputs! Something is wrong','pj_imdb').'</p>';
	wp_die();
}
## Save IMDB metabox
function imdb_save_metas($post_id) {
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if( !isset( $_POST['imdb_nonce'] ) || !wp_verify_nonce( $_POST['imdb_nonce'], 'pj_imdb_nonce' ) )
		return;
	if( !current_user_can( 'edit_post' ) )
		return;

	update_post_meta( $post_id, 'imdb_title',  sanitize_text_field( $_POST['title_imdb'] ) );
	update_post_meta( $post_id, 'imdb_year',  sanitize_text_field( $_POST['year_imdb'] ) );
	update_post_meta( $post_id, 'imdb_time',  sanitize_text_field( $_POST['time_imdb'] ) );
	update_post_meta( $post_id, 'imdb_genre',  sanitize_text_field( $_POST['genre_imdb'] ) );
	update_post_meta( $post_id, 'imdb_director',  sanitize_text_field( $_POST['director_imdb'] ) );
	update_post_meta( $post_id, 'imdb_actors',  sanitize_text_field( $_POST['actors_imdb'] ) );
	update_post_meta( $post_id, 'imdb_country',  sanitize_text_field( $_POST['country_imdb'] ) );
	update_post_meta( $post_id, 'imdb_rating',  sanitize_text_field( $_POST['rating_imdb'] ) );
	update_post_meta( $post_id, 'imdb_poster',  sanitize_text_field( $_POST['poster_imdb'] ) );
	update_post_meta( $post_id, 'imdb_imdbID',  sanitize_text_field( $_POST['imdbID_imdb'] ) );
}
add_action( 'save_post', 'imdb_save_metas');
