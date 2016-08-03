<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitter.php");

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$target_page = isset($_GET['target_page']) ? $_GET['target_page'] : 'search-advanced.php';

if (is_mobile())
{
  $page_size = 10;
}else{
  $page_size = 30;
}
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

$mode=$_POST['mode'];
if(!$mode){
  $mode=$_GET['mode'];
}
$search_tag=$_POST['search_tag'];
if(!$search_tag){
  $search_tag=$_GET['search_tag'];
}

if($mode=="search_tag"||$page)
{
  if($search_tag)
  {
    $search_tag = f_twitter_tag($db_conn,$search_tag);
    $sns_id = 1;
    $rtn_ifream_st = f_get_img_page($db_conn,$page_size,$page,$target_page,$search_tag);
  }
}

if($search_tag){
  $title = "ソーシャルハブ | ".$search_tag;
}else{
  $title = "ソーシャルハブ";
}

$keywords = "";
$description = "";
$h1_index = ($search_tag=="" ? '<i class="fa fa-search" aria-hidden="true"></i>高度な検索':'<i class="fa fa-search" aria-hidden="true"></i>'.$search_tag.':タグ検索');
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
    <!-- 検索窓 -->
      <form class="t1-form twitter-form">
        <legend class="t1-legend"><span>キーワード（複数の場合は半角スペースで区切る）</span></legend>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードをすべて含む" name="ands" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワード全体を含む" name="phrase" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードのいずれかを含む" name="ors" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードを含まない" name="nots" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のハッシュタグを含む" name="tag" >
            </div>
        </div>


        <legend class="t1-legend"><span>使用言語</span></legend>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-hand-right" style="color:red;"></span>
              </span>
              <select class="form-control" name="lang">
                <option value="" selected="selected">▼使用言語</option>
                <option value="all" selected="selected">すべての言語</option>
                <option value="ja">日本語 (日本語)</option>
                <option value="en">英語 (English)</option>
              </select>
            </div>
      </div>


      <legend class="t1-legend"><span>ツィート（入力数字以上を検索）</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="リツィート数" name="min_retweets:" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="お気に入り数" name="min_faves:" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="リプライ数" name="min_replies:" >
          </div>
      </div>



      <legend class="t1-legend"><span>ユーザー名</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次のアカウントからのツイート" name="from" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次のアカウントへの返信" name="to" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次のアカウントへの@ツイート" name="ref" >
          </div>
      </div>






      <legend class="t1-legend"><span>場所</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次の場所の周辺" name="place_id" >
          </div>
      </div>
      <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-hand-right" style="color:red;"></span>
              </span>
              <select class="form-control" name="within">
                <option value="" selected="selected">▼この範囲内で</option>
                  <option value="1">1</option>
                  <option value="5">5</option>
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                  <option value="500">500</option>
                  <option value="1000">1000</option>
              </select>
            </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn  btn-lg btn-default active">
                <input type="radio" name="units"  value="km" autocomplete="off" checked> キロ
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="radio" name="units"  value="mi" autocomplete="off"> マイル
              </label>
            </div>
          </div>
        </div>
      <legend class="t1-legend"><span>日付</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar" style="color:red;"></span>
            </span>
            <input type="text" id="since" class="form-control" placeholder="YYYY-MM-DD" name="since" >
            <span class="input-group-addon">
            -
            </span>
            <input type="text" id="until" class="form-control" placeholder="YYYY-MM-DD" name="until"  >
          </div>
      </div>
      <legend class="t1-legend"><span>フィルター</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn  btn-lg btn-default ">
                <input type="checkbox" autocomplete="off" name="filter" value="filter:images" >画像
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter" value="filter:videos" >動画
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter" value="filter:links" >リンク
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter" value="filter:verifled" >認証アカウント
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter" value="filter:vine" >Vine
              </label>
            </div>
          </div>
      </div>
      <legend class="t1-legend"><span>その他</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn  btn-lg btn-default ">
                <input type="checkbox" autocomplete="off" name="attd" value=":)" >ポジティブ
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd" value=":(" >ネガティブ
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd" value="?" >疑問形
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd" value="retweets" >リツイートを含む
              </label>
            </div>
          </div>
      </div>
      <!-- ボタン -->
      <hr>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <input type="hidden" name="ref" value="go">
            <button id="btn_update" type="submit" class="btn btn-lg btn-danger btn-block">検索</button>
        </div>
      </div>
      <!-- .ボタン -->
      </form>

    </div>
    <!-- .検索窓 -->


    <div class="row col-md-12 " style="margin-top: 20px;">
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
<script>
$('#since').datepicker({
  format: 'yyyy-mm-dd',
  language: 'ja',
  autoclose: true,
  clearBtn: true,
  endDate: Date(),
  orientation:'bottom auto'
});
$('#until').datepicker({
  format: 'yyyy-mm-dd',
  language: 'ja',
  autoclose: true,
  clearBtn: true,
  endDate: Date(),
  orientation:'auto'
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
