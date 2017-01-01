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
## Register Metabox
add_action( 'add_meta_boxes', 'pj_imdb_metabox' );
function pj_imdb_metabox() {
	add_meta_box('pj-imdb',__('Fetch info from IMDB','pj_imdb'),'pjimdb_callback','post','normal','high');
}
function pjimdb_callback($post) {
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
	<div id="result"></div>
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
				$("#sendAjax").html('<img src="<?=plugin_dir_url( __FILE__ )?>assets/loading.svg">');
			},
			success: function (response) {
				$("#sendAjax").html('<img src="<?=plugin_dir_url( __FILE__ )?>assets/search.png">');
				$('#result').slideDown().html(response);
			}
		});
		return false;
	});
});
</script>
<?php
}