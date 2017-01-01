<?php
require_once('../../../wp-load.php');

$name = (isset($_POST['title'])) ? $_POST['title'] : '';
$year = (isset($_POST['year'])) ? $_POST['year'] : '';
$imdb = (isset($_POST['imdb'])) ? $_POST['imdb'] : '';

$json = file_get_contents("http://www.omdbapi.com/?t=$name&y=$year&i=$imdb");
$data = json_decode($json, true);

if($data['Response'] == 'True') : ?>
<img src="<?=$data['Poster']?>">
<div class="content">
	<ul>
		<li><span><?php _e('Title','pj_imdb'); ?> :</span> <?=$data['Title']?></li>
		<li><span><?php _e('Year','pj_imdb'); ?> :</span> <?=$data['Year']?></li>
		<li><span><?php _e('Runtime','pj_imdb'); ?> :</span> <?=$data['Runtime']?></li>
		<li><span><?php _e('Genre','pj_imdb'); ?> :</span> <?=$data['Genre']?></li>
		<li><span><?php _e('Director','pj_imdb'); ?> :</span> <?=$data['Director']?></li>
		<li><span><?php _e('Actors','pj_imdb'); ?> :</span> <?=$data['Actors']?></li>
		<li><span><?php _e('Country','pj_imdb'); ?> :</span> <?=$data['Country']?></li>
		<li><span><?php _e('Rating','pj_imdb'); ?> :</span> <?=$data['imdbRating']?></li>
	</ul>
</div>
<?php
else :
	echo 'SOMTING IS WRONG';
endif;
?>