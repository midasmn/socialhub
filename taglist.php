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
// タグ一覧
$rtn_ifream_st = f_get_taglist($db_conn,$id);
// 
$title = "タグ一覧";
$keywords = "";
$description = "";
$h1_index = '<i class="fa fa-tags" aria-hidden="true"></i>タグ一覧';
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
// 
// $menu_tab1_st = "タグ検索";
// $menu_tab2_st = "画像一覧";
// $menu_tab3_st = "タグ一覧";

// $contents_cnt = f_get_contents_cnt($db_conn);
// $tag_cnt = f_get_tag_cnt($db_conn);

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
      <?php echo $rtn_ifream_st; ?>
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
</body>
</html>
