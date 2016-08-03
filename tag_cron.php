
<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
// OAuthライブラリの読み込み
// require "lib/twitteroauth/autoload.php";
// use Abraham\TwitterOAuth\TwitterOAuth;
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
////////////////////////////////////
$lang = "jp";
////////////////////////////////////
if ( isset($_REQUEST['language_id']) ) {
  $language_id = (int)$_REQUEST['language_id'];
} else {
  $language_id= 9; // 日本
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$target_page = isset($_GET['target_page']) ? $_GET['target_page'] : 'tag_cron.php';

if (is_mobile())
{
  $page_size = 10;
}else{
  $page_size = 30;
}
$page_all_cnt = f_get_page_all_cnt($db_conn,"CRON","");


$rtn_st = '';
// 
//セレクト画像
$base_sql = "SELECT `id`,`search_tag`, `disporder`, `exm_flg`, `createdate` FROM `shub_cron` order by `exm_flg` desc ,`createdate` desc ";
$page_count = ceil($page_all_cnt / $page_size);
$start = ($page - 1) * ($page_size);
$end = ($page * $page_size - 1 < $page_all_cnt) ? $page * $page_size - 1 : $page_all_cnt;
$page_sql = " limit $start ,$page_size ";
$sql = $base_sql.$page_sql;
// echo "<br>sql=".$sql;
$result = mysqli_query($db_conn,$sql);
if(!$result)
{
	$rtn_st = 'エラー';
}else
{
	$numrows = mysqli_num_rows($result);
	if($numrows>0)
	{
  		$rtn_st .= '<div class="col-xs-12 col-md-12" >';
  		$rtn_st .= '<h5 class="col-md-8" style="margin-bottom: 20px;;font-weight:200;1px;text-shadow:1px1px 0 rgba(0,0,0,0.1);color:#fff;">';


      $cur_cnt = $page_size*$page;
      if($cur_cnt>$page_all_cnt)
      {
        $cur_cnt = $cur_cnt-$page_size+$numrows;
      }
      // 
      $from_cnt = $cur_cnt-$numrows+1;
      $rtn_st .= $search_tag_st."(".number_format($page_all_cnt)."件中".number_format($from_cnt)."-".number_format($cur_cnt)."件表示)".number_format($page)."/".number_format($page_count)."ページ";

  		$rtn_st .= '</h5>';
  		$rtn_st .= '</div>';
  		// 
  		$rtn_st .= '<div class="col-xs-12 col-md-12" >';
  		$rtn_st .= '<div class="table-responsive">';
  		$rtn_st .= '<table class="table">';
  		$rtn_st .= '<tr class="text-center default">';
  		$rtn_st .= '<td>#タグ</td>';
  		$rtn_st .= '<td>登録日</td>';
  		$rtn_st .= '<td>クーロン設定</td>';
  		$rtn_st .= '</tr>';
		while($link = mysqli_fetch_row($result))
		{
			list($id,$search_tag,$disporder,$exm_flg,$createdate) = $link;
			$search_tag_st = str_replace("#", "", $search_tag);
      		$tag_cnt = f_get_page_all_cnt($db_conn,"TAG",$search_tag);
      		$rtn_st .= '<tr class="h2">';
      		$rtn_st .= '<td ><a  href="list.php?search_tag='.$search_tag_st.'">'.$search_tag;
      		$rtn_st .= '  <span class="badge">'.number_format($tag_cnt).'</span>';
      		$rtn_st .= '</a></td>';
      		$rtn_st .= '<td class="text-center">'.$createdate.'</td>';
      		$rtn_st .= '<td class="text-center">';
			// ボツボタン
	        $rtn_st .= '<a href="javascript: void(0)" class="cronbtn" data-id="'.$id.'">';
	        if($exm_flg=="0"){
	          $rtn_st .= '<span  class="glyphicon glyphicon-trash crontggle_'.$id.' btn-lg btn-danger" title="戻す"></span>';
	          $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-thumbs-up crontggle_'.$id.' btn-lg btn-default" title="ボツに"></span>';
	        }else{
	          $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-trash crontggle_'.$id.' btn-lg btn-danger" title="戻す"></span>';
	          $rtn_st .= '<span  class="glyphicon glyphicon-thumbs-up crontggle_'.$id.' btn-lg btn-default" title="ボツに"></span>';
	        }
	        $rtn_st .= '</a>';
	        // .ボツボタン
	        $rtn_st .= '</td>';
	        $rtn_st .= '</tr>';
		}
		$rtn_st .= '</table>';
		$rtn_st .= '</div>';
		$rtn_st .= '</div>';
    // 
    $pager_st = pager($page,$page_all_cnt,$page_size,$target_page,"");
    $rtn_st .= $pager_st;
	}else
	{
	$rtn_st .= '検索結果はありません。';
	}
}


$title = "ソーシャルハブ";
$keywords = "";
$description = "";
$h1_index = '<i class="fa fa-cogs" aria-hidden="true"></i>クーロン設定';

$description .= '('.date(Y).'-'.date(m).'-'.date(d).':'.$tgcnt.')';
$site_name = $title;
//
$og_title = $title;
$og_image = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$og_url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$og_site_name = $title;
$og_description = $description;
$h1_st = $title;
$h1_st_s = "  ";
$culhtml = "https://faceapglezon.info/socialhub/";
$crlhtmltitle = $title;
$footer_sitename = $title;
$itemprop_name = $title;
$itemprop_description = $description;
$itemprop_author = "https://faceapglezon.info/socialhub/";
// //
$fb_app_id = 557991774408353;
$article_publisher = "https://www.facebook.com/faceapglezon";
// //
$twitter_site = "@FaceApGleZon";
$sns_url = "http://".$_SERVER["HTTP_HOST"].htmlspecialchars($_SERVER["PHP_SELF"]);

?>
<?php require('header.php');?>
<body>
<div id="wrap">
<?php require('menu.php');?>


<!-- ページのコンテンツすべてをwrapする（フッター以外） -->
  <div class="container"  style="margin-top: 1px;">
    <div class="row" >
        <!-- タイトル -->
        <div class="col-md-12" style="margin-top: 15px;text-align: center;">
          <h1 class="h3 " style="color:white;text-transform: none; ">
            <?php echo $h1_index; ?>
          </h1>
          <h2 class="h5" style = "color:#A6A6A6;">
            <?php echo $h2_index; ?>
          </h2>
        </div>
        <!-- .タイトル -->
    </div>

    <div class="row col-md-12 " style="margin-top: 20px;">
          <?php echo $rtn_st; ?>
    </div>
      <!-- ページトップへ -->
      <a href="" class="btn btn-default pull-right" id="page-top">
        <i class="fa fa-angle-up fa-fw"></i>
      </a>

    </div><!-- .row -->
  </div><!-- .container -->
</div><!-- .wrap -->


<?php require('footer.php');?>
<script>
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
  $(".cronbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id: id,
        exm: 'cron'
      }, function(rs) {
      $(".crontggle_"+id).toggle();
    });
    });
});
</script>
</body>
</html>
