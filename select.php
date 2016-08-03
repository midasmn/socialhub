<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitter.php");

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$target_page = isset($_GET['target_page']) ? $_GET['target_page'] : 'select.php';

if (is_mobile())
{
  $page_size = 10;
}else{
  $page_size = 30;
}

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

$search_tag=$_GET['search_tag'];
if($search_tag)
{
  if(strpos($search_tag,'#') === false){
    //'abcd'のなかに'bc'が含まれていない場合
    $search_tag = '#'.$search_tag;
    $sns_id = 1;
    $rtn_ifream_st = f_get_img_page($db_conn,$page_size,$page,$target_page,$search_tag);
  } 
}else{
  $sns_id = 1;
  $rtn_ifream_st = f_get_img_page($db_conn,$page_size,$page,$target_page,"");
}

$title = "ソーシャルハブ";
$keywords = "";
$description = "";
$h1_index = '<i class="fa fa-heart" aria-hidden="true"></i>セレクト画像一覧';

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
          <?php echo $rtn_ifream_st; ?>
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
</body>
</html>
