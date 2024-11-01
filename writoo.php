<?php
/*
Plugin Name: Writoo
Plugin URI: http://www.writoo.com/
Description: Make news <strong>automatique</strong>. 
Version: 1.0.0
Author: HP Developpement / Writoo 
Author URI:  http://www.writoo.com
License: GPLv2 or later
*/


define("URL_WRITOO",plugin_dir_url( __FILE__ ));
define("DIR_WRITOO",dirname( __FILE__ ) ."/");



add_action('admin_menu', 'wp_news_mapage' );


function more_schedules() {
 return array(
 '3heures' => array('interval' => 60*60*3, 'display' => '3 Heures'),
 '3minutes' => array('interval' => 60*3, 'display' => '3 Minutes')
 );
 }
 

add_filter('cron_schedules', 'more_schedules');
add_action('writoo_make', 'writoo_this_hourly' ); 

register_activation_hook( __FILE__, 'writoo_init' );
register_deactivation_hook(__FILE__, 'writoo_deactivation');

/*********************** Suppression du programme ****************/
function writoo_deactivation() 
{
	wp_clear_scheduled_hook('writoo_make');
	require_once(DIR_WRITOO . "functions.php");
	$del["del"]=getenv("HTTP_HOST");
	writo_post($del);
}



/*********************** Activation du programme ****************/
function writoo_init() 
{
	if (!wp_next_scheduled('writoo_make')) {wp_schedule_event( time(), '3heures', 'writoo_make' );}
	require_once(DIR_WRITOO . "functions.php");
	$add[]="";
	writo_post($add);
}

function writoo_this_hourly() 
{
	require_once(DIR_WRITOO . "functions.php");
	writo_post(get_post_meta( 2, 'mots',false));
	writto_insert() ;
	return false;
}

// ---------------------------------------------------
function wp_news_mapage() 
{
	add_options_page('Writoo auto', 'Writoo' , 'manage_options', 'Writoo_admin', 'Writoo_options');
}

function writoo_options() {
	require_once(DIR_WRITOO . "fr.php");

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if (isset($_GET))
	{
		if (isset($_GET["mots"]))
		{
			delete_post_meta( 2, "mots");
			foreach($_GET["mots"] as $key=>$value)
			add_post_meta( 2, "mots",  str_replace(";",",", trim($_GET["mots"][$key])) . "|$key|" . trim($_GET["cats"][$key]));	
		}
	}

	$q = get_post_meta( 2, 'mots',false);
	foreach($q as $key=>$value)
	{
		$qq = explode("|",$value);
		$mots[$qq[1]] = $qq[0] ;
		if (trim($qq[0])>"" && isset($_GET["valid"])) $suite=1;
	}
	
	if (!$suite && isset($_GET["valid"]) ) $err="<div><span style='color:#FF0000'>" . PRECISEZ . "</span></div>"; else $err="";
?>
<div class="wrap">
<h1><?php echo GENE ?></h1>
<?php if(!$suite){ ?>
<p>
<?php echo REFERENCEMENT ?><br>
<?php echo EXPLICATION ?><br>
<?php echo EXPLICATION1 ?><br><br>
<?php echo EXEMPLE ?><br>
</p>
<?php echo $err ?>
<form name=wp_news>
<input type=hidden name=page value="<?php echo $_GET["page"]?>">
<input type=hidden name=valid value=1>
<table>


<?php 
$args = array('type'                     	=> 'post',    
		'child_of'                 	=> 0,    
		'parent'                   => 0,    
		'orderby'                  => 'name',    
		'order'                    => 'ASC',    
		'hide_empty'               => 0,    
		'hierarchical'             => 1,    
		'exclude'                  => 0,    
		'include'                  => 0,    
		'number'                   => 0,    
		'taxonomy'                 => 'category',    
		'pad_counts'               => false );

  $categories=  get_categories($args);
  $i=0;
  foreach ($categories as $key=>$category) 
  {
  	echo "<tr>";
	echo "<td><label>" . ENTREZ  . "</label> </td>";
	echo "<td><input type=text name=mots[" . $category->slug ."] value=\"" . $mots[$category->slug] . "\" style='width:300px'></td>";
	echo "<td>" . utf8_encode("dans la catégorie") ."</td>";
	echo "<td><input type=hidden name=cats[" . $category->slug ."] value=\"". $category->cat_name ."\">  <b>" . $category->cat_name . "</b> </td>";
	echo "</tr>";
	$i++;
  }

?>
<tr><td colspan=4 style='text-align:center'><input type=submit value="Suivant >>"></td></tr>
</table>
<?php } else { 

writoo_this_hourly();
?>
</form></div>

<table id=abo2 cellpadding="0" cellspacing="0" style='border:solid 1px #E0E0F0'>
<tr><td style='padding:0px;margin:0px;background-color:#FFFFFF' valign=top><iframe frameborder=0 scrolling=no id=cb name=cb src='https://payment.allopass.com/subscription/subscribe.apu?ids=251556&recall=1&data=<?php echo getenv("HTTP_HOST") ?>&idd=991837&lang=<?php echo LANG ?>' style='border:none;padding:0px;margin:0px;width:580px;height:760px'></iframe></td>
<td valign=top style='margin-top:5px;padding-left:15px;padding-right:15px;background-image : url(<?php echo URL_WRITOO ?>title-bg.png); background-repeat:repeat-x;background-color:#FFFFFF;width:470px'>
<br><br>
<p>
<?php echo J7 ?><br>
<?php echo J28 ?><br><br><br>
<?php echo JOK ?><br><br>
<div style='background-image : url(<?php echo URL_WRITOO ?>formlayout_separator.png); background-repeat:repeat-y;padding-left:10px'>
<h2>Exemple d'article</h2>
<img src='http://www.writoo.com/exemple.png' atl="Exemple d'article">
</p>
</td>
</tr></table>
<?php 
}  
	

}

?>
