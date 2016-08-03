<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');


$id = isset($_POST['id']) ? $_POST['id'] : "";
$exm = isset($_POST['exm']) ? $_POST['exm'] : "";

if($exm=='del')//普通
{
  $cur=0;
  $exm_flg = f_get_comon_item($db_conn,'shub_contents','del_flg','id',$id);
  if($exm_flg==$cur)
  {
    $sql = "UPDATE `shub_contents` SET `del_flg` = 1 WHERE `id`=$id";
  }else{
    $sql = "UPDATE `shub_contents` SET `del_flg` = $cur WHERE `id`=$id";
  }
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
    $rtn_st ="NG";                                              
  }else{
    $rtn_st ="OK";
  }
  // 
  f_update_shub_cnt ($db_conn,'SELECT');
  f_update_shub_cnt ($db_conn,'KEEP');
  f_update_shub_cnt ($db_conn,'TRASH');
}elseif($exm=='heart')//いいね
{
  $cur=1;
  $exm_flg = f_get_comon_item($db_conn,'shub_contents','star_flg','id',$id);
  if($exm_flg==$cur)
  {
    $sql = "UPDATE `shub_contents` SET `star_flg` = 0 WHERE `id`=$id";
  }else{
    $sql = "UPDATE `shub_contents` SET `star_flg` = $cur WHERE `id`=$id";
  }
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
    $rtn_st ="NG";                                              
  }else{
    $rtn_st ="OK";
  }
  // 
  f_update_shub_cnt ($db_conn,'SELECT');
  f_update_shub_cnt ($db_conn,'KEEP');
  f_update_shub_cnt ($db_conn,'TRASH');
}elseif($exm=='star'){
  $cur=2;
  $exm_flg = f_get_comon_item($db_conn,'shub_contents','star_flg','id',$id);
  if($exm_flg==$cur)
  {
    $sql = "UPDATE `shub_contents` SET `star_flg` = 0 WHERE `id`=$id";
  }else{
    $sql = "UPDATE `shub_contents` SET `star_flg` = $cur WHERE `id`=$id";
  }
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
    $rtn_st ="NG";                                              
  }else{
    $rtn_st ="OK";
  }
  f_update_shub_cnt ($db_conn,'SELECT');
  f_update_shub_cnt ($db_conn,'KEEP');
  f_update_shub_cnt ($db_conn,'TRASH');
}elseif($exm=='cron'){
  $cur=1;
  $exm_flg = f_get_comon_item($db_conn,'shub_cron','exm_flg','id',$id);
  if($exm_flg==$cur)
  {
    $sql = "UPDATE `shub_cron` SET `exm_flg` = 0 WHERE `id`=$id";
  }else{
    $sql = "UPDATE `shub_cron` SET `exm_flg` = $cur WHERE `id`=$id";
  }
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
    $rtn_st ="NG";                                              
  }else{
    $rtn_st ="OK";
  }
}
return 
?>