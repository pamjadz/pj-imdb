<?php
require_once('../../../wp-load.php');

$name = (isset($_POST['title'])) ? $_POST['title'] : '';
$year = (isset($_POST['year'])) ? $_POST['year'] : '';
$imdb = (isset($_POST['imdb'])) ? $_POST['imdb'] : '';

$imdbjson = file_get_contents("http://www.omdbapi.com/?t=$name&y=$year&i=$imdb");
$dimdb = json_decode($imdbjson, true);

if($dimdb['Response'] == 'True') { ?>
	<img src="<?=esc_url($dimdb['Poster'])?>">
	<div class="content">
		<ul>
			<li><span><?php _e('Title','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Title'])?></li>
			<li><span><?php _e('Year','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Year'])?></li>
			<li><span><?php _e('Runtime','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Runtime'])?></li>
			<li><span><?php _e('Genre','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Genre'])?></li>
			<li><span><?php _e('Director','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Director'])?></li>
			<li><span><?php _e('Actors','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Actors'])?></li>
			<li><span><?php _e('Country','pj_imdb'); ?> :</span> <?=esc_html($dimdb['Country'])?></li>
			<li><span><?php _e('Rating','pj_imdb'); ?> :</span> <?=esc_html($dimdb['imdbRating'])?></li>
		</ul>
	</div>
	<input type="hidden" name="title_imdb" value="<?=esc_attr($dimdb['Title'])?>"/>
	<input type="hidden" name="year_imdb" value="<?=esc_attr($dimdb['Year'])?>"/>
	<input type="hidden" name="time_imdb" value="<?=esc_attr($dimdb['Runtime'])?>"/>
	<input type="hidden" name="genre_imdb" value="<?=esc_attr($dimdb['Genre'])?>"/>
	<input type="hidden" name="director_imdb" value="<?=esc_attr($dimdb['Director'])?>"/>
	<input type="hidden" name="actors_imdb" value="<?=esc_attr($dimdb['Actors'])?>"/>
	<input type="hidden" name="country_imdb" value="<?=esc_attr($dimdb['Country'])?>"/>
	<input type="hidden" name="rating_imdb" value="<?=esc_attr($dimdb['imdbRating'])?>"/>
	<input type="hidden" name="poster_imdb" value="<?=esc_attr($dimdb['Poster'])?>"/>
	<input type="hidden" name="imdbID_imdb" value="<?=esc_attr($dimdb['imdbID'])?>"/>
<?php
}
else
	echo '<p class="error">'.__('Check inputs! Something is wrong','pj_imdb').'</p>';
?>
