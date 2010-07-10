<?

include_once('classes.php');
//include('config.php');
//if(count($_POST)){
//    foreach($_POST as $key => $value){
//    	$input[$key]=&$_POST[$key];
//    }
//}
//$_in&=$_REQUEST;
//var_dump($_REQUEST);
//var_dump($_REQUEST);
if(isset($_REQUEST['id']) and is_numeric($_REQUEST['id'])) $id=$_REQUEST['id'];
else exit('{result: ID}');
if(isset($_REQUEST['type']) and ($_REQUEST['type']=='h' OR $_REQUEST['type']=='H' OR $_REQUEST['type']=='f') OR $_REQUEST['type']=='F') $type=$_REQUEST['type'];
else exit('{result: TYPE}');
	$connect= new connector();
	$max_file_size=16;//in Mb

	if((count($_FILES)>0 and $_REQUEST['type']=='h') OR (count($_FILES)==1 and $_REQUEST['type']=='f')){
		$file_obj=new files();
        $answer="";
		foreach($_FILES as $key => $value){
			if($value['name']!=''){
				$file_obj->load_file($value);
				//-tut ukazat razshqrenije faila
				$extension=$file_obj->get_file_extension();
				if($extension=='mp3'){
					if ($value["error"] > 0){
						$answer.=$value["error"];
						//$answer.='{result: FAILE}';
					}else{
						if($value["size"]<$max_file_size*1024*1024){
							//echo $value["tmp_name"];
							$song=new mp3($value["tmp_name"]);
							$technical_info=$song->get_info();
/*
  ["version_id"]=>int(1)
  ["version"]=>string(14) "MPEG Version 1"
  ["layer_id"]=>int(1)
  ["layer"]=>string(7) "Layer I"
  ["protection"]=>string(3) "CRC"
  ["sampling_rate"]=>bool(false)
  ["padding"]=>string(3) "off"
  ["private"]=>string(3) "off"
  ["channel_mode"]=>string(6) "stereo"
  ["copyright"]=>string(3) "off"
  ["original"]=>string(2) "on"
  ["filesize"]=>int(2969728)
  ["length"]=>string(5) "12:22"
*/
							$song_info=$song->get_id3();
/*
  ["tag"]=>string(3) "TAG"
  ["title"]=>string(19) "Небо Плачущей Осени"
  ["author"]=>string(5) "Марки"
  ["album"]=>string(15) "Письма в никуда"
  ["year"]=>string(4) "2009"
  ["comment"]=>string(30) " ????????????????????????????"
  ["genre_id"]=>int(121)
  ["genre"]=>string(9) "Punk Rock"
  ["bitrate"]=>int(32)
*/

							do{
								$new_name=gen_unick_name();
								$new_name.='.'.$extension;
								$host=new host($connect->get_connection());
								$this_host=$host->get_this_id();
								$location=$host->get_this_location();
							}while(file_exists($location.$new_name));
                            if(!$song_id3->error){                            	$return=$file_obj->save($location.$new_name);
                            	$host->set_server_space();
                            }else{
								$answer.='{result: DATA}';
							}
							if($return){
								$song_db=new song($connect->get_connection());
								foreach($song_info as $key => $value){
									if(($temp = check_text($value)))$value=$temp;
								}
								$ans=$song_db->add_new($id,$song_info,$technical_info,$new_name,$this_host);
								if($ans) $answer.='{result: OK}';
								else  $answer.='{result: '.$song_db->get_status().'}';
							}else $answer.='{result: UPLOAD}';
						}
					}
				}else{ $answer.='{result: EXT}';}
			}
		}
    	//if(count($_FILES)>1)
    	preg_match('|([^?&]*)?|',$_SERVER['HTTP_REFERER'],$referer);
	}else{ $answer.='{result: empty';}
    if($type=='h' OR $type=='H')header('Location: '.$referer[1].'?ans='.$answer);
    if($type=='f' OR $type=='F')echo $answer;
?>