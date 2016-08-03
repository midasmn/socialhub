<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitter.php");

// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');

// タグリスト
function f_cron_taglist($db_conn)
{
  $rtn_st = "";
  // $strSQL = "SELECT distinct (`search_tag`) FROM `shub_contents`  order by id desc";
  $strSQL = "SELECT `search_tag` FROM `shub_cron` WHERE `exm_flg` = 1 order by `disporder` asc";
  $result = mysqli_query($db_conn,$strSQL);
  if($result)
  {
    while($link = mysqli_fetch_row($result))
    {
      list($search_tag) = $link;
      f_twitter_tag($db_conn,$search_tag);
      echo $search_tag."\n";
      // 
    }
  }
  return $rtn_st; 
}

f_cron_taglist($db_conn);
// 
f_update_shub_cnt ($db_conn,'CONT');
// f_update_shub_cnt ($db_conn,'TAG');
// f_update_shub_cnt ($db_conn,'SELECT');
// f_update_shub_cnt ($db_conn,'KEEP');
// f_update_shub_cnt ($db_conn,'TRASH');