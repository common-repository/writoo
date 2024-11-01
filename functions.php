<?php
function writo_post($datas)
{
	$datas["host"] = getenv("HTTP_HOST");

	$options = array(
   	'http'		=> array(
	'timeout' 	=> 5, 
     	'method'	=> "POST",
     	'header'	=>	"Accept-language: fr\r\n".
       		"Content-type: application/x-www-form-urlencoded\r\n",
     	'content'	=>http_build_query($datas)
 	));
 
	$context = stream_context_create($options);
 
	$fh = fopen( 'http://www.writoo.com/script.php', 'r', false, $context);
 	// Récupération des meta informations du flux
 	$meta = stream_get_meta_data( $fh );
 	// Récupération des headers sous forme de tableau
 	$headers= $meta['wrapper_data'];
 	// Récupération de la réponse du serveur
 	$retour= '';
 	
	while( !feof( $fh ) ) $retour  .= fread( $fh, 1024 );

 	fclose( $fh );
 
	echo  $retour;

}
/***************************/

function writto_insert()
{
	$datas["host"] = getenv("HTTP_HOST");

	$options = array(
   	'http'		=> array(
	'timeout' 	=> 5, 
     	'method'	=> "POST",
     	'header'	=>	"Accept-language: fr\r\n".
       		"Content-type: application/x-www-form-urlencoded\r\n",
     	'content'	=>http_build_query($datas)
 	));
 
	$context = stream_context_create($options);
 
	$fh = fopen( 'http://www.writoo.com/recupe.php', 'r', false, $context);
	// Récupération des meta informations du flux
 	$meta = stream_get_meta_data( $fh );
 	// Récupération des headers sous forme de tableau
 	$headers= $meta['wrapper_data'];
 	// Récupération de la réponse du serveur
 	$retour= '';
 	
	while( !feof( $fh ) ) $retour  .= fread( $fh, 1024 );

 	fclose( $fh );

	$a= $retour;
	$q = explode("|",$a);

	$_cat = $q[0];
	$_titre = $q[1];
	$_img = $q[2];
	$_libelle = $q[3];
	$_tags = $q[4];
	
	if ($_cat =="")return false;
	if ($_titre =="")return false;
	if ($_libelle =="")return false;
	//return true;

	$content = "<img class='alignleft size-full' src='" . $_img . "' alt=\"" . $title . "\" style='max-width:250px'>" . $_libelle;
	$publishedDate = date("Y-m-d H:i:s");


	$my_post = array(
		'post_title' => $_titre	,
    		'post_content' => $content,
    		'post_status' => 'publish',
     		'post_author' => 1,
     		'post_date' => $publishedDate,
     		'post_date_gmt' => $publishedDate,
		'tags_input'	=> $_tags,
     		'post_category' => array($_cat)
	);

	$wp_id = wp_insert_post($my_post);

	$filename = $_img;

	$wp_filetype = wp_check_filetype(basename($filename), null );
	$attachment = array(
     		'post_mime_type' => $wp_filetype['type'],
     		'post_title' => preg_replace('/\.[^.]+$/', '', $_titre),
     		'post_content' => $_libelle,
     		'post_status' => 'inherit',
     		'post_parent' => $wp_id 
  	);

	$attach_id = wp_insert_attachment( $attachment, $filename, $wp_id );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

	wp_update_attachment_metadata( $attach_id,  $attach_data );

  	$_img_up = get_the_post_thumbnail($wp_id, 'full');

  	$my_up_post = array();
  	$my_up_post['ID'] = $wp_id;
  	$my_up_post['post_content'] =  $_img_up . $_libelle;

	// Update the post into the database
  	wp_update_post( $my_up_post );
}
?>