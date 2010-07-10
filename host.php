<?
include_once('classes.php');

$connect= new connector();
$new_host = new host($connect->get_connection());

if($new_host->setup_new_server())exit('1');
else exit ('0');

?>