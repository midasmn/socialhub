<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$target_page = isset($_GET['target_page']) ? $_GET['target_page'] : 'image.php';

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


$rtn_ifream_st = f_get_tile_page($db_conn,$page_size,$page,$target_page);

$title = "ソーシャルハブ";
$keywords = "";
$description = "";
$h1_index = ($search_tag=="" ? '<i class="fa fa-birthday-cake" aria-hidden="true"></i>公開画像一覧':'<i class="fa fa-birthday-cake" aria-hidden="true"></i>'.$search_tag.':公開画像一覧');

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

    <div class="col-xs-12 " >
      <?php echo $rtn_ifream_st; ?>
    </div>

    <!-- <iframe src="http://pixabay.com/" frameborder="1" width="600" height="600" style="width:100%;"></iframe> -->
    <!-- <iframe src="http://pixabay.com/" frameborder="1" width="600" height="600" style="zoom:0.55"></iframe> -->

      <!-- ページトップへ -->
      <a href="" class="btn btn-default pull-right" id="page-top">
        <i class="fa fa-angle-up fa-fw"></i>
      </a>

    </div><!-- .row -->
  </div><!-- .container -->
</div><!-- .wrap -->
<?php require('footer.php');?>
</body>
</html>
