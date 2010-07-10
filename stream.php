<?
$module='show_songs';
session_start();

if(count($_GET))$input=&$_GET;
if(count($_POST)){
    foreach($_POST as $key => $value){
    	$input[$key]=&$_POST[$key];
    }
}

if(isset($input['ss']) and is_numeric($input['ss'])){
	include_once('classes.php');
    $connect= new connector();

	$song=new song($connect->get_connection());
	$location=$song->get_location($input['ss']);
	$file=$song->get_name($input['ss']);
	$file=$location.$file;
	if(!$file){
		exit($song->get_status());
	}

	$player = new player($file);

	if($player->get_status()!=''){
		exit($player->get_status());
	}

	$player->stream();
	//exit('END_SOUND');
}else{
	exit('0');
}


?>