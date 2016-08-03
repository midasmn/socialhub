<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitter.php");

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
$page_size = 50;
$page_all_cnt = f_get_page_all_cnt($db_conn,"ALL","");
$page = (!isset($page)) ? 1 : $page;
$page_count = ceil($page_all_cnt / $page_size);

$start = ($page - 1) * ($page_size);
$end = ($page * $page_size - 1 < $page_all_cnt) ? $page * $page_size - 1 : $page_all_cnt;
$page_sql = " limit $start ,$page_size ";

$sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
  `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
  FROM `shub_contents`  order by id_str desc  $page_sql";

$result = @mysqli_query($db_conn,$sql);
if(!$result)
{
}else{
  $numrows = mysqli_num_rows($result);
  if($numrows>0)
  {
    $cnt = 1;
    $rtn_st = '';
    while($link = mysqli_fetch_row($result))
   {

      list($id,$sns_id,$search_tag,$user_id,$screen_name,$tweet_name,$tweet_icon,$id_str,$tweet_text,$tweet_date,$tweet_hashtag,$tweet_img,$tweet_img_short,$tweet_img_display_url,$star_flg,$del_flg) = $link;
      // 
      $url ='https://twitter.com/search?q='.$screen_name.'" target="_blank"';
      // タブ用画像パス
      $img_path = $tweet_img;
      $url_ch = "detail.php?id=".$id;
      $rtn_st .= '<div class="col-xs-12 col-md-2" >';
      $rtn_st .= '<div class="thumbnail">';
      $rtn_st .= '<a href="'.$url_ch.'"><img '.($del_flg==2 ? 'class="img-circle"':'').'src="'.$img_path.'">';
      $rtn_st .= '<div class="caption">';
      $rtn_st .= '<p><a href="'.$url.'" target="_blank"><img class="img-circle" src="'.$tweet_icon.'">'.$screen_name.'</a>&nbsp;&nbsp;'.$tweet_date.'</p>';
      if($tweet_hashtag) //タグ分割
      {
        $tag_arr = explode( ',', $tweet_hashtag );
        foreach($tag_arr as $tag_itm)
        { 
          $rtn_st .= '<p><h5><a href="index.php?mode=search_tag&search_tag='.$tag_itm.'"><span class="label label-default">'.$tag_itm.'</span></a></h5></p>';
        }
      }
      $rtn_st .= '<p class="text-left">';
      // 優先ボタン
      $rtn_st .= '<a href="javascript: void(0)" class="heartbtn" data-id="'.$id.'" >'."\n";
      $rtn_st .= '<span class="glyphicon glyphicon-heart hearttggle_'.$id.' btn-sm btn-'.($star_flg==1 ? 'success': 'default').'" title="オウンド公開"></span>'."\n";
      
      $rtn_st .= '</a>';
      // いいね
      $rtn_st .= '<a href="javascript: void(0)" class="starbtn" data-id="'.$id.'" >'."\n";
      $rtn_st .= '<span  class="glyphicon glyphicon-star startggle_'.$id.' btn-sm btn-'.($star_flg==2 ? 'primary':'default').'" title="キープ"></span>'."\n";
      $rtn_st .= '</a>'."\n";
      // .優先ボタン

      $rtn_st .= '</p>';

      $rtn_st .= '<p class="text-right">';
      // ボツボタン
      $rtn_st .= '<a href="javascript: void(0)" class="delbtn" data-id="'.$id.'">';
      if($del_flg=="0"){
        $rtn_st .= '<span  class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-danger" title="戻す"></span>';
        $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-default" title="ボツに"></span>';
      }else{
        $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-danger" title="戻す"></span>';
        $rtn_st .= '<span  class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-default" title="ボツに"></span>';
      }
      $rtn_st .= '</a>';
      // .ボツボタン
      $rtn_st .= '</p>';
      // 
      $rtn_st .= '</div>';
      $rtn_st .= '</div>';
      $rtn_st .= '</div>';
      $cnt++;
    }
    $rtn_st .= '</div>';
  }else{
    $rtn_st = 'ZERO';
  }
}
// 
$cur_cnt = $page_size*$page;
if($cur_cnt>$page_all_cnt){
$cur_cnt = $cur_cnt-$page_size+$numrows;
}

$title = $numrows."点表示(".number_format($cur_cnt)."点/".number_format($page_all_cnt)."点)";
$keywords = "";
$description = "";
$h1_index = $title;
$h2_index = $description;

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

$rtn_tab2 = "favicon-192x192.png";
// 登録数

$menu_tab1_st = "タグ検索";
$menu_tab2_st = "画像一覧";
$menu_tab3_st = "タグ一覧";

$contents_cnt = f_get_contents_cnt($db_conn);
$tag_cnt = f_get_tag_cnt($db_conn);

?>
<?php require('header.php');?>
<body>
<div id="wrap">
<?php require('menu.php');?>


<!-- ページのコンテンツすべてをwrapする（フッター以外） -->
  <div class="container"  style="margin-top: 1px;">
    <div class="row" >
      <?php if($exm_itemno) :?>
      <?php else: ?>
          <!-- 広告 -->
          <?php if (is_mobile()) :?>
          <?php else: ?>
          <?php endif; ?>
      <?php endif; ?>
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

    
      <!-- 画像$ -->
      <?php echo $rtn_st; ?>

      <?php
      echo '<div class="text-center h5">';
      echo '<ul class="pagination pagination-sm">';
      for($i = 1; $i <= $page_count; $i++)
      {
        if($page==$i)
        {
          echo '<li class="active"><a href="' . $myself.'?page='.$i.'">' . $i . '</a></li>';
        }else{
          echo '<li ><a href="' . $myself.'?page='.$i.'">' . $i . '</a></li>';
        }
      }
      echo '</ul>';
      echo '</div>';
      ?>


    </div>

      <!-- ページトップへ -->
      <a href="" class="btn btn-default pull-right" id="page-top">
        <i class="fa fa-angle-up fa-fw"></i>
      </a>

    </div><!-- .row -->
  </div><!-- .container -->
</div><!-- .wrap -->

<!-- 広告 -->
<div class="col-md-12" style="margin-top: 20px;height: 80px;text-align: center;">
  <!-- ＜スポンサーリンク＞ -->
  <?php if (is_mobile()) :?>
  <!-- スマートフォン向けコンテンツ -->
  <?php else: ?>
  <?php endif; ?>
</div>
<!-- 広告 -->



<?php require('footer.php');?>
<script>
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
  $(".delbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id: id,
        exm: 'del'
      }, function(rs) {
      $(".deltggle_"+id).toggle();
    });
    });
  $(".heartbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id : id,
        exm: 'heart'
      }, function(rs) {
        if ($(".hearttggle_"+id).hasClass('btn-success')) {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        } else {
          $(".hearttggle_"+id).removeClass("btn-default").addClass("btn-success");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        }
    });
    });
  $(".starbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id: id,
        exm: 'star'
      }, function(rs) {
        if ($(".startggle_"+id).hasClass('btn-primary')) {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        } else {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-default").addClass("btn-primary");
        }
    });
    });
});
</script>

<?php if($language_id) :?>
  <script>// Let's call it 2 times just for fun...
// Show full page Loading Overlay
$.LoadingOverlay("show");
// Hide it after 3 seconds
setTimeout(function(){
    $.LoadingOverlay("hide");
}, 1000);
</script>
<?php endif; ?>
</body>
</html>
