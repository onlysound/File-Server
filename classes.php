<?php
class connector{
  private $connect;
  private $status;
    //-----------------
    public function __construct(){//<<ready

    $ret='OK';
     include_once 'config.php';
    $this->set_connection(mysql_connect( $connect[$what]['host'], $connect[$what]['user'], $connect[$what]['pass']));

    if($this->get_connection()){
      if (!mysql_select_db($connect[$what]['db'], $this->get_connection())) {
            $ret='DB_ERR';
      }else{
        mysql_query("SET NAMES  cp1251;");
      }
    }else $ret='CONNECT_ERR';

    $this->set_status($ret);
  }
  public function __destruct(){//<<ready
    mysql_close($this->get_connection());
  }
  private function set_connection( $connect ){
    $this->connect = $connect;
  }
  public function get_connection(){
    return $this->connect;
  }
  private function set_status( $status ){//<<ready
    $this->status = $status;
  }
  public function get_status(){//<<ready
    return $this->status;
  }
}

class song{
  private $connect;
  private $status;
    //-----------------
    public function __construct($connect = NULL){//<<ready

      if($connect!=NULL){
            $this->set_connection($connect);
      }else{

        $ret='OK';
       include_once 'config.php';
      $this->set_connection(mysql_connect( $connect[$what]['host'], $connect[$what]['user'], $connect[$what]['pass']));

      if($this->get_connection()){
        if (!mysql_select_db($connect[$what]['db'], $this->get_connection())) {
              $ret='DB_ERR';
        }
      }else $ret='CONNECT_ERR';

      $this->set_status($ret);

      }
  }
    private function set_connection( $connect ){
    $this->connect = $connect;
  }
  public function get_connection(){
    return $this->connect;
  }
  private function set_status( $status ){//<<ready
    $this->status = $status;
  }
  public function get_status(){//<<ready
    return $this->status;
  }
  //-----------------
   public function add_new($user_id,$sond_info,$tecnical_info,$file_name,$location){
    //SELECT song_add(suser INTEGER(11), sauthor CHAR(64), sname CHAR(64), slen TIME, sbit INTEGER(11), sloc CHAR(255));
      //0-���������� �������
    //1-������ ����� ����� ��� ����������

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
*/
/*
["bitrate"]=>int(32)
  ["tag"]=>string(3) "TAG"
  ["title"]=>string(19) "���� �������� �����"
  ["author"]=>string(5) "�����"
  ["album"]=>string(15) "������ � ������"
  ["year"]=>string(4) "2009"
  ["comment"]=>string(30) " ????????????????????????????"
  ["genre_id"]=>int(121)
  ["genre"]=>string(9) "Punk Rock"
  ["filesize"]=>int(2969728)
  ["length"]=>string(5) "12:22"
*/

    //['bitrate']
    //var_dump($sond_info,$tecnical_info);
    if($sond_info['author']=='')$sond_info['author']='unknown';
    if($sond_info["title"]=='')$sond_info["title"]='unknown';
    if($sond_info["album"]=='')$sond_info["album"]='unknown';
    if($sond_info["genre"]=='')$sond_info["genre"]='unknown';
    if($sond_info["year"]=='')$sond_info["year"]='0';
    //FUNCTION         `song_add`(  suser INTEGER(11), sauthor CHAR(64),     sname CHAR(64),       sgenre CHAR(32),       slen INTEGER,       sbit INTEGER(11),     syear INTEGER(11),     ssize INTEGER(11),       sfile_name cHAR(32), sloc CHAR(64))
      $ans=mysql_query("SELECT song_add(".$user_id.",\"".$sond_info['author']."\",\"".$sond_info['title']."\",\"".$sond_info['genre']."\",".$sond_info['length'].",".$sond_info['bitrate'].",".$sond_info["year"].",".$sond_info["filesize"].",\"".$file_name."\",".$location.");",$this->get_connection());
      if(mysql_errno()){
          
          return false;
      }
      $ans=mysql_fetch_row($ans);
         if($ans[0]!=1){
           $song_num=$ans[0];
      $ans=mysql_query("SELECT song_tech_insert(".$song_num.",".$tecnical_info['second'].",".$tecnical_info['third'].",".$tecnical_info['fourth'].");",$this->get_connection());
            $ans=mysql_fetch_row($ans);
      //echo mysql_error();

      $ans=mysql_query("SELECT album_new('".$sond_info['album']."','".$sond_info['author']."');",$this->get_connection());
      $ans=mysql_fetch_row($ans);

           $album_num=$ans[0];
      $ans=mysql_query("SELECT performer_check('".$sond_info['author']."');",$this->get_connection());

      //echo "SELECT performer_check('".$sond_info['author']."');";
      $ans=mysql_fetch_row($ans);
      //var_dump($ans);

      $perf_num=$ans[0];

      $ans=mysql_query("SELECT album_performer_add(".$album_num.",".$perf_num.",".$song_num.");",$this->get_connection());
      //echo mysql_error();
      $ans=mysql_fetch_row($ans);

            return TRUE;
    }elseif($ans[0]==1){
      $this->set_status('SONG_EXIST');
        return FALSE;
    }
   }
  public function change_author($user_id,$song_id,$new_author){
    //SELECT song_change_author(suserid INTEGER(11), ssongid INTEGER(11), sauthor INTEGER(11));
    //0-��� ������ �������
    //1-����� �� ����������
    //2-������������ �� ���
    //3-����� �� ����������

      $ans=mysql_query("SELECT song_change_author(".$user_id.",".$song_id.",".$new_author.");",$this->get_connection());
    $ans=mysql_fetch_row($ans);

    if($ans[0]==0){
      return TRUE;
         }elseif($ans[0]==1){
           $this->set_status('SONG_EXIST');
           return FALSE;
         }
   }
    public function change_rule($user_id,$song_id,$new_rule){
    //SELECT song_change_rule(suserid INTEGER(11), ssongid INTEGER(11), srule INTEGER(11));
    //0-��� ������ �������
    //1-����� �� ����������
    //2-������������ �� ���

      $ans=mysql_query("SELECT song_change_rule(".$user_id.",".$song_id.",".$new_rule.");",$this->get_connection());
    $ans=mysql_fetch_row($ans);

    if($ans[0]==0){
      return TRUE;
         }elseif($ans[0]==1){
           $this->set_status('SONG_NOT_EXIST');
           return FALSE;
         }elseif($ans[0]==1){
           $this->set_status('WRONG_USER');
           return FALSE;
         }
   }
    public function delete_from_list($user_id,$song_id){
    //SELECT song_delete(sid INTEGER(11), suser INTEGER(11));
    //0-������� �������
    //1-����� �� ����������
    //2-���� �� ���(������������ ��� �������� �� ���� ������)

      $ans=mysql_query("SELECT song_delete(".$song_id.",".$user_id.");",$this->get_connection());
    $ans=mysql_fetch_row($ans);

    if($ans[0]==0){
      return TRUE;
         }elseif($ans[0]==1){
           $this->set_status('SONG_NOT_EXIST');
           return FALSE;
         }elseif($ans[0]==1){
           $this->set_status('WRONG_USER');
           return FALSE;
         }
   }
    public function get_name($song_id){
    //SELECT `song_get_location`(sid INTEGER(11));
    //����� �����
    //1-����� �� ����������

      $ans=mysql_query("SELECT song_get_name(".$song_id.");",$this->get_connection());
      echo mysql_error();
    $ans=mysql_fetch_row($ans);

    if($ans[0]==1){
      $this->set_status('SONG_NOT_EXIST');
           return FALSE;
         }elseif($ans[0]==-1){
           $this->set_status('NOT_AVAILABLE');
           return FALSE;
         }else{
           return $ans[0];
         }
   }
  public function get_location($song_id){
    //SELECT `song_get_location`(sid INTEGER(11));
    //����� �����
    //1-����� �� ����������
    //2-����� ����� ����� �� ��������

    $ans=mysql_query("SELECT song_get_location(".$song_id.");",$this->get_connection());
    echo mysql_error();
    $ans=mysql_fetch_row($ans);

    if($ans[0]==1){
      $this->set_status('SONG_NOT_EXIST');
      return FALSE;
    }else{
      return $ans[0];
    }
   }

  public function select_user_songs($user_id,$from = 0,$type = 'LESS'){
    $help=new playlist($this->get_connection());
    $return=$help->select_songs_general($user_id,$from,$type);
    unset($help);
    return $return;
  }
  public function count_user_songs($user_id){
    $help=new playlist($this->get_connection());
    $return=$help->count_songs_general($user_id);
    unset($help);
    return $return;
  }
    public function select_clones($song_id,$from = 0,$type = 'LESS'){
    if($type=='LESS'){
          $ans=mysql_query("
      SELECT
      `performer`.`id` AS pid,
      `performer`.`name` AS pname,
      `songs_info`.`id` AS sid,
      `songs_info`.`name` AS sname,
      `users_songs_info`.`id` user_sid,
      `users_songs_info`.`id` as song_id,
      `users_songs_info`.`length` as len,
      `users_songs_info`.`bitrate` as bitr
      FROM `songs_info`
      JOIN (
      `users_songs_info` , `users_songs_tech_info` , `performer`
      ) ON (
      `songs_info`.`id`=`users_songs_info`.`song_num`
            AND `songs_info`.`author` = `performer`.`id`
      AND `users_songs_info`.`id` = `users_songs_tech_info`.`id`
      )WHERE `songs_info`.`id`=".$song_id." LIMIT ".$from.",".($from+50).";
      ",$this->get_connection());
        }elseif($type=='MORE'){
          $ans=mysql_query("
      SELECT
      `performer`.`id` AS pid,
      `performer`.`name` AS pname,
      `songs_info`.`id` AS sid,
      `songs_info`.`name` AS sname,
      `users_songs_info`.`id` user_sid,
      `users_songs_info`.`id` as song_id,
      `users_songs_info`.`length` as len,
      `users_songs_info`.`bitrate` as bitr,
            `users_songs_tech_info`.`second` as second,
            `users_songs_tech_info`.`third` as third,
            `users_songs_tech_info`.`fourth` as fourth
      FROM `songs_info`
      JOIN (
      `users_songs_info` , `users_songs_tech_info` , `performer`
      ) ON (
      `songs_info`.`id`=`users_songs_info`.`song_num`
            AND `songs_info`.`author` = `performer`.`id`
      AND `users_songs_info`.`id` = `users_songs_tech_info`.`id`
      )WHERE `songs_info`.`id`=".$song_id." LIMIT ".$from.",".($from+50).";
      ",$this->get_connection());
        }


    if(mysql_num_rows($ans)!=0){
      $ans=mysql_array($ans);
      return $ans;
         }else{
           $this->set_status('EMPTY_LIST');
           return FALSE;
         }
  }
  public function count_clones($song_id){
          $ans=mysql_query("
      SELECT song_clone_count(".$song_id.");",$this->get_connection());
        $ans=mysql_fetch_row($ans);

    if($ans[0]!=0){
      return $ans[0];
    }elseif($ans[0]==0){
      $this->set_status('FAKE_LIST');
           return FALSE;
    }
  }
}

class files{
  private $file_name;
    private $file_temp;
  private $file_extension;

    public function load_file(&$file_path){
      preg_match("/^([0-9A-Za-z�-��-�\-_\s\{\}\[\]\(\)']+).([0-9A-Za-z�-��-�\-_]+)$/",$file_path['name'],$out);
    $this->file_name=$out[1];;
    $this->file_extension=$out[2];
    $this->file_temp=$file_path['tmp_name'];
    }
    function save($filename, $permissions=744) {
      //echo $filename;
      $filecontents= file_get_contents($this->file_temp);
      $ans=file_put_contents($filename,$filecontents);

         //chmod($filename,$permissions);
         //chown($filename,'root');
         //chgrp($filename,'root');

        return $ans;
   }
    public function get_file_name(){
    return $this->file_name;
  }
  public function get_file_extension(){//<<ready
    return $this->file_extension;
  }
}

class host{
  private $connect;
  private $status;
//----------------------------------
  function host($connect = NULL){
    if($connect!=NULL){
            $this->set_connection($connect);
      }else{

        $ret='OK';
       include_once 'config.php';
      $this->set_connection(mysql_connect( $connect[$what]['host'], $connect[$what]['user'], $connect[$what]['pass']));

      if($this->get_connection()){
        if (!mysql_select_db($connect[$what]['db'], $this->get_connection())) {
              $ret='DB_ERR';
        }
      }else $ret='CONNECT_ERR';

      $this->set_status($ret);
      }
      $this->set_server_space();
  }
    private function set_connection( $connect ){
    $this->connect = $connect;
  }
  public function get_connection(){
    return $this->connect;
  }
  private function set_status( $status ){//<<ready
    $this->status = $status;
  }
  public function get_status(){//<<ready
    return $this->status;
  }
//----------------------------------
  public function check_server_response($host){
      $starttime = microtime(true);
      $file      = fsockopen($host, 80, $errno, $errstr, 5);
      $stoptime  = microtime(true);
      $status    = 0;

      if (!$file) $status = -1;  // Site is down
      else{
          fclose($file);
          $status = ($stoptime - $starttime) * 1000;
          $status = floor($status);
      }
      return $status;
  }
  public function is_server_online($adress){
      $file      = fsockopen($adress, 80, $errno, $errstr, 10);
      if (!$file) $status = FALSE;  // Site is down
      else{
          $status=TRUE;
      }
      fclose($file);
        return $status;
  }
  public function check_server($host_id){

      $ans=mysql_query("SELECT
      `host`.`server_address`
      FROM `host`
    WHERE `host`.`id`=".$host_id."
    LIMIT 1;",$this->get_connection());

    if(mysql_num_rows($ans)==1){
      $ans=mysql_fetch_row($ans);
      return $this->is_server_online($ans[0]);
         }else{
           return FALSE;
         }
  }
  public function get_available_server(){
    $min_memory_condition=1024*1024*1024;//1 gig min
      $ans=mysql_query("SELECT
      `host`.`id`,
      `host`.`server_name` as adress,
      `host`.`port`
      FROM `host`
    WHERE `host`.`memory_left`>".$min_memory_condition."
    LIMIT 10;",$this->get_connection());

      if(mysql_num_rows($ans)!=0){
      $ans=mysql_array($ans);
      foreach($ans as $key => $value){
         if($this->is_server_online($value['adress'])) return $value;
      }
    }

    $this->set_status('NO_SERVER_AVAILABLE');
    return FALSE;
  }
  public function get_this_id(){
    return $this->get_server_id($_SERVER['SERVER_NAME']);
  }
  public function get_this_name(){
    return $this->get_server_name($_SERVER['SERVER_ADDR']);
  }
  public function get_this_location(){
    return $this->get_server_location($_SERVER['SERVER_NAME']);
  }
  public function get_server_id($host){
    $query="SELECT
      `host`.`id`
      FROM `host`
    WHERE ";
    $query.="`host`.`server_name` like '".$host."' OR `host`.`server_address`='".$host."'";
    $query.=" LIMIT 1;";

    $ans=mysql_query($query,$this->get_connection());
    //echo mysql_error();
    if(mysql_num_rows($ans)!=0){
      $ans=mysql_fetch_row($ans);
      return $ans[0];
    }else{
           $this->set_status('EMPTY_LIST');
           return FALSE;
        }
  }
  public function get_server_name($host){
    $query="SELECT
      `host`.`server_name`
      FROM `host`
    WHERE ";
    if(is_numeric($host)) $query.="`host`.`id`=".$host;
    else $query.="`host`.`server_address`='".$host."'";
    $query.=" LIMIT 1;";

    $ans=mysql_query($query,$this->get_connection());

    if(mysql_num_rows($ans)!=0){
      $ans=mysql_fetch_row($ans[0]);
      return $ans;
    }else{
           $this->set_status('EMPTY_LIST');
           return FALSE;
        }
  }
  public function get_server_location($host){
    $query="SELECT
      `host`.`store_location`
      FROM `host`
    WHERE ";
    if(is_numeric($host)) $query.="`host`.`id`=".$host;
    else $query.="`host`.`server_name` like '".$host."' OR `host`.`server_address`='".$host."'";
    $query.=" LIMIT 1;";

    $ans=mysql_query($query,$this->get_connection());

    if(mysql_num_rows($ans)!=0){
      $ans=mysql_fetch_row($ans);
      return $ans[0];
    }else{
           $this->set_status('EMPTY_LIST');
           return FALSE;
        }
  }
  public function curent_server_space(){
    return disk_free_space($_SERVER['DOCUMENT_ROOT']);
  }
  public function set_server_space(){
         $ans=mysql_query("SELECT server_space(".$this->get_this_id().",".$this->curent_server_space().");",$this->get_connection());
         //echo mysql_error().'<br>'."SELECT server_space(".$this->get_this_id().",'".$this->curent_server_space()."');";
        $ans=mysql_fetch_row($ans);
    if($ans[0]==1){
      return TRUE;
    }elseif($ans[0]==0){
           $this->set_status('EMPTY_LIST');
           return FALSE;
        }
  }
  public function setup_new_server($location = 'songs/'){
       $free_space=disk_free_space('/');
    //$free_space=3000000000;

       $server_name=$_SERVER['SERVER_NAME'];
       $server_addres=$_SERVER['SERVER_ADDR'];
       $port=$_SERVER['SERVER_PORT'];

       $ans=mysql_query("SELECT server_add('".$server_name."','".$server_addres."','".$port."','".$location."','".$free_space."');",$this->get_connection());
    //echo mysql_error();
    $ans=mysql_fetch_row($ans);

    if($ans[0]!=0){
        return TRUE;
    }elseif($ans[0]==0){
      $this->set_status('SERVER_EXIST');
        return FALSE;
    }

  }
}

class image {

   var $image;
   var $image_type;

   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         $ans=imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         $ans=imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         $ans=imagepng($this->image,$filename);
      }
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
      return $ans;
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      $ans=imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
      return $ans;
   }
}

class mp3{
  var $id3_genres_array = array(
            'Blues', 'Classic Rock', 'Country', 'Dance', 'Disco', 'Funk', 'Grunge', 'Hip-Hop', 'Jazz', 'Metal', 'New Age', 'Oldies', 'Other', 'Pop', 'R&B', 'Rap', 'Reggae', 'Rock', 'Techno', 'Industrial',
            'Alternative', 'Ska', 'Death Metal', 'Pranks', 'Soundtrack', 'Euro-Techno', 'Ambient', 'Trip-Hop', 'Vocal', 'Jazz+Funk', 'Fusion', 'Trance', 'Classical', 'Instrumental', 'Acid', 'House',
            'Game', 'Sound Clip', 'Gospel', 'Noise', 'AlternRock', 'Bass', 'Soul', 'Punk', 'Space', 'Meditative', 'Instrumental Pop', 'Instrumental Rock', 'Ethnic', 'Gothic', 'Darkwave',
            'Techno-Industrial', 'Electronic', 'Pop-Folk', 'Eurodance', 'Dream', 'Southern Rock', 'Comedy', 'Cult', 'Gangsta', 'Top 40', 'Christian Rap', 'Pop/Funk', 'Jungle', 'Native American', 'Cabaret',
            'New Wave', 'Psychadelic', 'Rave', 'Showtunes', 'Trailer', 'Lo-Fi', 'Tribal', 'Acid Punk', 'Acid Jazz', 'Polka', 'Retro', 'Musical', 'Rock & Roll', 'Hard Rock', 'Folk', 'Folk/Rock', 'National Folk',
            'Swing', 'Fast Fusion', 'Bebob', 'Latin', 'Revival', 'Celtic', 'Bluegrass', 'Avantgarde', 'Gothic Rock', 'Progressive Rock', 'Psychedelic Rock', 'Symphonic Rock', 'Slow Rock', 'Big Band',
            'Chorus', 'Easy Listening', 'Acoustic', 'Humour', 'Speech', 'Chanson', 'Opera', 'Chamber Music', 'Sonata', 'Symphony', 'Booty Bass', 'Primus', 'Porn Groove', 'Satire', 'Slow Jam', 'Club', 'Tango', 'Samba',
            'Folklore', 'Ballad', 'Power Ballad', 'Rhythmic Soul', 'Freestyle', 'Duet', 'Punk Rock', 'Drum Solo', 'Acapella', 'Euro-house', 'Dance Hall'
        );
  var $info_bitrates = array(
            1    =>    array(
                1    =>    array( 0 => 0, 16 => 32, 32 => 64, 48 => 96, 64 => 128, 80 => 160, 96 => 192, 112 => 224, 128 => 256, 144 => 288, 160 => 320, 176 => 352, 192 => 384, 208 => 416, 224 => 448, 240 => false),
                2    =>    array( 0 => 0, 16 => 32, 32 => 48, 48 => 56, 64 =>  64, 80 =>  80, 96 =>  96, 112 => 112, 128 => 128, 144 => 160, 160 => 192, 176 => 224, 192 => 256, 208 => 320, 224 => 384, 240 => false),
                3    =>    array( 0 => 0, 16 => 32, 32 => 40, 48 => 48, 64 =>  56, 80 =>  64, 96 =>  80, 112 =>  96, 128 => 112, 144 => 128, 160 => 160, 176 => 192, 192 => 224, 208 => 256, 224 => 320, 240 => false)
            ),
            2    =>    array(
                1    =>    array( 0 => 0, 16 => 32, 32 => 48, 48 => 56, 64 =>  64, 80 => 80, 96 => 96, 112 => 112, 128 => 128, 144 => 144, 160 => 160, 176 => 176, 192 => 192, 208 => 224, 224 => 256, 240 => false),
                2    =>    array( 0 => 0, 16 =>  8, 32 => 16, 48 => 24, 64 =>  32, 80 => 40, 96 => 48, 112 =>  56, 128 =>  64, 144 =>  80, 160 =>  96, 176 => 112, 192 => 128, 208 => 144, 224 => 160, 240 => false),
                3    =>    array( 0 => 0, 16 =>  8, 32 => 16, 48 => 24, 64 =>  32, 80 => 40, 96 => 48, 112 =>  56, 128 =>  64, 144 =>  80, 160 =>  96, 176 => 112, 192 => 128, 208 => 144, 224 => 160, 240 => false)
            )
        );
  var $info_versions = array(0 => "reserved", 1 => "MPEG Version 1", 2 => "MPEG Version 2", 2.5 => "MPEG Version 2.5");
  var $info_layers = array("reserved", "Layer I", "Layer II", "Layer III");
    var $info_sampling_rates = array(
    0        =>    array(0 => false, 4 => false, 8 => false, 12 => false),
        1        =>    array(0 => "44100 Hz", 4 => "48000 Hz", 8 => "32000 Hz", 12 => false),
        2        =>    array(0 => "22050 Hz", 4 => "24000 Hz", 8 => "16000 Hz", 12 => false),
        2.5    =>    array(0 => "11025 Hz", 4 => "12000 Hz", 8 => "8000 Hz", 12 => false)
    );
    var $info_channel_modes = array(0 => "stereo", 64 => "joint stereo", 128 => "dual channel", 192 => "single channel");
    var $file = "";
    var $fh = false;
    var $error = false;
    var $id3_parsed = false;
    var $id3 = array(
/*          "tag"            =>    "",
            "title"        =>    "unknown",
            "author"        =>    "unknown",
            "album"        =>    "unknown",
            "year"        =>    "unknown",
            "comment"    =>    "unknown",
            "genre_id"    =>    0,
            "genre"        =>    "unknown"
*/  );
    var $info = array();

    function mp3($file, $exitonerror=true) {

      $file=rawurldecode($file);
        if (file_exists($file)) {
          $this->file = $file;
           $this->fh = fopen($this->file,"r");
            global $HTTP_HOST, $PHP_SELF;
        } else {
          $this->error = "NO_SUCH_FILE";
            if ($exitonerror) $this->exitonerror();
        }
//            $this->get_id3v2header();
        $second = $this->synchronize();

//            echo("2nd byte = $second <b>" . decbin($second) . "</b><br>");
        $third = ord(fread($this->fh, 1));
        $fourth = ord(fread($this->fh, 1));
        $this->info['second']=$second;
        $this->info['third']=$third;
        $this->info['fourth']=$fourth;
        $this->info["version_id"] = ($second & 16) > 0 ? ( ($second & 8) > 0 ? 1 : 2 ) : ( ($second & 8) > 0 ? 0 : 2.5 );
        $this->info["version"] = $this->info_versions[ $this->info["version_id"] ];
        $this->info["layer_id"] = ($second & 4) > 0 ? ( ($second & 2) > 0 ? 1 : 2 ) : ( ($second & 2) > 0 ? 3 : 0 );
        $this->info["layer"] = $this->info_layers[ $this->info["layer_id"] ];
        $this->info["protection"] = ($second & 1) > 0 ? "no CRC" : "CRC";
        $this->info["sampling_rate"] = $this->info_sampling_rates[ $this->info["version_id"] ][ ($third & 12)];
        $this->info["padding"] = ($third & 2) > 0 ? "on" : "off";
        $this->info["private"] = ($third & 1) > 0 ? "on" : "off";
        $this->info["channel_mode"] = $this->info_channel_modes[$fourth & 192];
        $this->info["copyright"] = ($fourth & 8) > 0 ? "on" : "off";
        $this->info["original"] = ($fourth & 4) > 0 ? "on" : "off";

        $this->id3_parsed = true;
        fseek($this->fh, -128, SEEK_END);
        $line = fread($this->fh, 10000);
    if (preg_match("/^TAG/", $line)) {
          $this->id3 = unpack("a3tag/a30title/a30author/a30album/a4year/a30comment/C1genre_id", $line);
            $this->id3["genre"] = $this->id3_genres_array[$this->id3["genre_id"]];
            $this->id3["filesize"] = filesize($this->file);
            $this->id3["bitrate"] = $this->info_bitrates[ $this->info["version_id"] ][ $this->info["layer_id"] ][ ($third & 240) ];
      if(!is_null($this->id3["bitrate"]) and $this->id3["bitrate"]!=0){
        $this->id3["length"] = $this->calculate_length();
      }
      else{
        $this->id3["bitrate"]=0;
        $this->id3["length"] = 0;
      }
    } else {
        $this->error = "no idv3 tag found";
        }
    }
    function exitonerror() {
      //echo($this->error);
      exit;
    }
    function set_id3($title = "", $author = "", $album = "", $year = "", $comment = "", $genre_id = 0) {
      $this->error = false;
      $this->wfh = fopen($this->file,"a");
      fseek($this->wfh, -128, SEEK_END);
      fwrite($this->wfh, pack("a3a30a30a30a4a30C1", "TAG", $title, $author, $album, $year, $comment, $genre_id), 128);
        fclose($this->wfh);
    }
    function get_id3() {
      return $this->id3;
  }
        // get_info() helper methods
  function calculate_length() {
      $length = floor(($this->id3["filesize"]) / $this->id3["bitrate"] * 0.008);
        return($length);
  }
    function get_info() {

        return $this->info;
  }
  function synchronize() {
    $finished = false;
    rewind($this->fh);
    $trash=fread($this->fh, 300);
    while (!$finished) {
      do{
              $skip = ord(fread($this->fh, 1));
//                    echo("inside synchronize() skip = $skip <b>" . decbin($skip) . "</b><br>");
      }while ($skip != 255 && !feof($this->fh));

          if (feof($this->fh)) {
              $this->error="NO_HEADER";
                //if ($exitonerror) $this->exitonerror();
      }
            $store = ord(fread($this->fh, 1));
//                echo("inside synchronize() store = $store <b>" . decbin($store) . "</b><br>");
            if ($store >= 225) {
                $finished = true;
            } else if (feof($this->fh)) {
              $this->error="NO_HEADER";
                if ($exitonerror) $this->exitonerror();
            }
    }
        return($store);
  }
    function get_id3v2header() {
      $bytes = fread($this->fh, 3);
        if ($bytes != "ID3") {
          echo("no ID3 tag");
            return(false);
        }
            // get major and minor versions
        $major = fread($this->fh, 1);
        $minor = fread($this->fh, 1);
        //echo("ID3v$major.$minor");
    }
    function close() {
      @fclose($this->fh);
    }
}

class player{
  private $stream_file;
  private $status;

  public function player($file){
    if(file_exists($file)){
       $this->stream_file=fopen($file,'r');
     }else{
        $this->set_status('WRONG_FILE');
    }
  }
  private function set_status( $status ){//<<ready
    $this->status = $status;
  }
  public function get_status(){//<<ready
    return $this->status;
  }
  public function stream(){
    rewind($this->stream_file);
     fpassthru($this->stream_file);
  }
}

function logging($text){

    $pathToHostingClass=pathinfo(realpath(__FILE__));

    $log_file="bad_log-$(date +%m-%Y)";
    $log_adress=$pathToHostingClass["dirname"].'/../logs/';
    $command="echo \"$(date '+%d-%m-%Y %H:%M:%S.%N') '".$text."'\" >> ".$log_adress.$log_file;
    //echo $command;
    
    return exec($command);
}
function mysql_array(&$mysql_result){

    for($i=0;$i<mysql_num_rows($mysql_result);$i++){
      for($j=0;$j<mysql_num_fields($mysql_result);$j++){
        $field=mysql_fetch_field($mysql_result,$j);
        $ret[$i][$field->name]=mysql_result($mysql_result,$i,$j);
      }
    }
  return $ret;
}
function check_text(&$string){
    $string=str_replace('\"','\'',$string);
    preg_match_all('/([0-9A-Za-z�-��-�\-\._])+/',$string,$out,PREG_SET_ORDER);
    if(!$out){
      return false;
    }
    $string=mysql_real_escape_string($string);
    return $string;
  }
function gen_unick_name(){
        $len=25;
        $str='';
        $rand_values=array(
        '1','2','3','4','5','6','7','8','9','0',
        'a','b','c','d','e','f','g','h','i','j',
        'k','l','m','n','o','p','q','r','s','t',
        'u','v','w','x','y','z','A','B','C','D',
        'E','F','G','H','I','J','K','L','M','N',
        'O','P','Q','R','S','T','U','V','W','X',
        'Y','Z');

        for($i=0;$i<$len;$i++){
          list($usec, $sec) = explode(' ', microtime());

      $seed=(float) $sec + ((float) $usec * 100000);
      srand($seed);
      $randval= rand(1,62);

      $str.=$rand_values[$randval];
        }
        return $str;
  }

?>