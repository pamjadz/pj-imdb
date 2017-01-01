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

## Check define ABSPATH
if (! defined('ABSPATH') )
	exit;

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
	wp_nonce_field( 'pj_imdb_nonce', 'imdb_nonce' );
?>
<div class="imdb-box">
	<div class="head"><?php _e('Fetch info from IMDB','pj_imdb'); ?> <i class="logo"></i></div>
	<div class="search">
		<div>
			<input type="text" name="title" placeholder="<?php _e('Title','pj_imdb'); ?>">
			<input type="number" min="1950" max="<?php echo date('Y'); ?>" name="year" placeholder="<?php _e('Year','pj_imdb'); ?>">
			<input type="text" name="imdb" placeholder="<?php _e('IMDB ID','pj_imdb'); ?>">
		</div>
		<a id="sendAjax"><img src="<?=plugin_dir_url( __FILE__ )?>assets/search.png"></a>
	</div>
<?php
if(imdb('imdbID') != '') : ?>
	<div id="result" style="display:block">
		<img src="<?=imdb('poster')?>">
		<div class="content">
			<ul>
				<li><span><?php _e('Title','pj_imdb'); ?> :</span> <?=imdb('title')?></li>
				<li><span><?php _e('Year','pj_imdb'); ?> :</span> <?=imdb('year')?></li>
				<li><span><?php _e('Runtime','pj_imdb'); ?> :</span> <?=imdb('time')?></li>
				<li><span><?php _e('Genre','pj_imdb'); ?> :</span> <?=imdb('genre')?></li>
				<li><span><?php _e('Director','pj_imdb'); ?> :</span> <?=imdb('director')?></li>
				<li><span><?php _e('Actors','pj_imdb'); ?> :</span> <?=imdb('actors')?></li>
				<li><span><?php _e('Country','pj_imdb'); ?> :</span> <?=imdb('country')?></li>
				<li><span><?php _e('Rating','pj_imdb'); ?> :</span> <?=imdb('rating')?></li>
			</ul>
		</div>
	</div>
<?php
else :
	echo '<div id="result"></div>';
endif;
?>
</div>
<script>
jQuery(function ($) {
	$("#sendAjax").click(function(){
		$.ajax({
			url: '<?=plugin_dir_url( __FILE__ ).'process.php'?>',
			type: 'POST',
			data: {
				title : $('.search [name=title]').val(),
				year : $('.search [name=year]').val(),
				imdb : $('.search [name=imdb]').val(),
			},
			beforeSend : function() {
				$("#sendAjax").html('<img src="<?=plugin_dir_url( __FILE__ )?>assets/loading.svg" width="20" height="20">');
			},
			success: function (response) {
				$("#sendAjax").html('<img src="<?=plugin_dir_url( __FILE__ )?>assets/search.png">');
				$('#result').fadeIn().html(response);
			}
		});
		return false;
	});
});
</script>
<?php
}

## Save IMDB metabox
function imdb_save_metas($post_id) {
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if( !isset( $_POST['imdb_nonce'] ) || !wp_verify_nonce( $_POST['imdb_nonce'], 'pj_imdb_nonce' ) )
		return;
	if( !current_user_can( 'edit_post' ) )
		return;
	
	update_post_meta( $post_id, 'imdb_title',  esc_attr( $_POST['title_imdb'] ) );
	update_post_meta( $post_id, 'imdb_year',  esc_attr( $_POST['year_imdb'] ) );
	update_post_meta( $post_id, 'imdb_time',  esc_attr( $_POST['time_imdb'] ) );
	update_post_meta( $post_id, 'imdb_genre',  esc_attr( $_POST['genre_imdb'] ) );
	update_post_meta( $post_id, 'imdb_director',  esc_attr( $_POST['director_imdb'] ) );
	update_post_meta( $post_id, 'imdb_actors',  esc_attr( $_POST['actors_imdb'] ) );
	update_post_meta( $post_id, 'imdb_country',  esc_attr( $_POST['country_imdb'] ) );
	update_post_meta( $post_id, 'imdb_rating',  esc_attr( $_POST['rating_imdb'] ) );
	update_post_meta( $post_id, 'imdb_poster',  esc_attr( $_POST['poster_imdb'] ) );
	update_post_meta( $post_id, 'imdb_imdbID',  esc_attr( $_POST['imdbID_imdb'] ) );
}
add_action( 'save_post', 'imdb_save_metas');
