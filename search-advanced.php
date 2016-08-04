<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitterR.php");

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

// パラメータ
$ands = isset($_GET['ands']) ? $_GET['ands'] : "";
$ands = str_replace("　", " ", $ands);
$phrase = isset($_GET['phrase']) ? $_GET['phrase'] : "";
$phrase = str_replace("　", " ", $phrase);
$ors = isset($_GET['ors']) ? $_GET['ors'] : "";
$ors = str_replace("　", " ", $ors);
$nots = isset($_GET['nots']) ? $_GET['nots'] : "";
$nots = str_replace("　", " ", $nots);
$tag = isset($_GET['tag']) ? $_GET['tag'] : "";
$tag = str_replace("　", " ", $tag);

$lang = isset($_GET['lang']) ? $_GET['lang'] : "";

$min_retweets = isset($_GET['min_retweets']) ? $_GET['min_retweets'] : "";
$min_faves = isset($_GET['min_faves']) ? $_GET['min_faves'] : "";
$min_replies = isset($_GET['min_replies']) ? $_GET['min_replies'] : "";

$from = isset($_GET['from']) ? $_GET['from'] : "";
$to = isset($_GET['to']) ? $_GET['to'] : "";
$name = isset($_GET['name']) ? $_GET['name'] : "";

$near = isset($_GET['near']) ? $_GET['near'] : "";
$near = str_replace("　", " ", $near);
$within = isset($_GET['within']) ? $_GET['within'] : "";
$units = isset($_GET['units']) ? $_GET['units'] : "";

$since = isset($_GET['since']) ? $_GET['since'] : "";
$until = isset($_GET['until']) ? $_GET['until'] : "";

$filter = isset($_GET['filter']) ? $_GET['filter'] : "";   
$attd = isset($_GET['attd']) ? $_GET['attd'] : "";    

$mode = isset($_GET['mode']) ? $_GET['mode'] : "";    
if($mode=="go")
{
    $options = array();
    $cnt = 100;
    // 
    $q_st = "";
    $q_st .= $ands;//AND検索
    if($phrase){$q_st .= ' "'.$phrase.'" ';}//厳密
    if($ors)//OR検索
    {
      $i=1;
      $ors_arr = explode( ' ', $ors );
      foreach($ors_arr as $or_itm)
      { 
        if($i==1){
          $q_st .= $or_itm;
        }else{
          $q_st .= ' OR '.$or_itm;
        }
        $i++;
      }
    }
    if($nots)//除外
    {
      $nots_arr = explode( ' ', $nots );
      foreach($nots_arr as $nots_itm)
      { 
        $q_st .= ' -'.$nots_itm;
      }
    }
    if($tag)//ハッシュタグ
    {
      $q_st .= ' '.$tag;
    }
    // 
    if(is_numeric($min_retweets)&&$min_retweets>0)//リツィート数  
    {
      $q_st .= ' min_retweets:'.$min_retweets;
    }
    if(is_numeric($min_faves)&&$min_faves>0)//お気に入り数  
    {
      $q_st .= ' min_faves:'.$min_faves;
    }
    if(is_numeric($min_replies)&&$min_replies>0)//リプライ
    {
      $q_st .= ' min_replies:'.$min_replies;
    }
    // 
    if($name)//スクリーンネーム
    {
      if(strpos($name,'@')===false){
        $q_st .= ' @'.$name;
      }else{
        $q_st .= ' '.$name;
      }
    }
    if($from)//ユーザーあてリプライ
    {
      if(strpos($from,'@')===false){
        $q_st .= ' from:@'.$from;
      }else{
        $q_st .= ' from:'.$from;
      }
    }
    if($to)//ユーザーあてリプライ
    {
      if(strpos($to,'@')===false){
        $q_st .= ' to:@'.$to;
      }else{
        $q_st .= ' to:'.$to;
      }
    }

    if($since)//日付
    {
      $q_st .= ' since:'.$since;
    }
    if($until)//日付
    {
      $q_st .= ' until:'.$until;
    }
    if($filter)//filter
    {
      $filter_itm_st = "";
      $i=1;
      foreach($filter as $filter_itm)
      { 
        if($i==1)
        {
          $filter_itm_st .= $filter_itm;
        }else{
          $filter_itm_st .= ' '.$filter_itm;
        }
        $i++;
      }
      $q_st .= ' filter:'.$filter_itm_st;
    }

    if($attd)//attd
    {
      foreach($attd as $attd_itm)
      { 
        $q_st .= ' '.$attd_itm;
      }
    }
    // 
    if($near)//attd
    {
      $near_arr = explode( ' ', $near );
      $near_itm_st = "";
      $i=1;
      foreach($near_arr as $near_itm)
      { 
        if($i==1)
        {
          $near_itm_st .= $near_itm;
        }else{
          $near_itm_st .= ' '.$near_itm;
        }
        $i++;
      }
      $q_st .= ' near:'.$near_itm_st;
    }
    if($within)
    {
        $q_st .= ' within:'.$within.$units;
    }

    // echo  "<br>".$q_st;
    $options = ['q' => $q_st, 'lang'=>$lang ,'count' => $cnt , 'result_type'=>'mixed'];//言語
    echo  "<!-- options=".var_dump($options)."-->";


    $statuses = f_twitter_api($db_conn,$options);//twitterAPI
    $rtn_st = f_disp_result($statuses);//表示
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
      <form class="t1-form twitter-form" method="GET">
        <legend class="t1-legend"><span>キーワード（複数の場合は半角スペースで区切る）</span></legend>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードをすべて含む" name="ands" value="<?=$ands?>" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワード全体を含む" name="phrase"  value="<?=$phrase?>" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードのいずれかを含む" name="ors"  value="<?=$ors?>" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のキーワードを含まない" name="nots"  value="<?=$nots?>" >
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-lg">
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
              </span> 
            <input type="text" class="form-control" placeholder="次のハッシュタグを含む" name="tag"  value="<?=$tag?>" >
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
            <input type="text" class="form-control" placeholder="リツィート数" name="min_retweets"  value="<?=$min_retweets?>" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="お気に入り数" name="min_faves"  value="<?=$min_faves?>" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="リプライ数" name="min_replies"  value="<?=$min_replies?>" >
          </div>
      </div>
      <legend class="t1-legend"><span>ユーザー名</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次のアカウントのツイート" name="from"  value="<?=$from?>" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次のアカウントからのツイート" name="to"  value="<?=$to?>" >
          </div>
      </div>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="@スクリーンネームのツイート" name="name"  value="<?=$name?>" >
          </div>
      </div>
      <legend class="t1-legend"><span>場所</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-pencil"></span>
            </span> 
            <input type="text" class="form-control" placeholder="次の場所の周辺" name="near"  value="<?=$near?>" >
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
            <input type="text" id="since" class="form-control" placeholder="YYYY-MM-DD" name="since"  value="<?=$since?>" >
            <span class="input-group-addon">
            -
            </span>
            <input type="text" id="until" class="form-control" placeholder="YYYY-MM-DD" name="until"   value="<?=$until?>" >
          </div>
      </div>    
      <legend class="t1-legend"><span>フィルター</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn  btn-lg btn-default ">
                <input type="checkbox" autocomplete="off" name="filter[]" value="images" >画像
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter[]" value="videos" >動画
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter[]" value="links" >リンク
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter[]" value="verifled" >認証アカウント
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="filter[]" value="vine" >Vine
              </label>
            </div>
          </div>
      </div>
      <legend class="t1-legend"><span>その他</span></legend>
      <div class="form-group">
          <div class="input-group input-group-lg">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn  btn-lg btn-default ">
                <input type="checkbox" autocomplete="off" name="attd[]" value=":)" >ポジティブ
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd[]" value=":(" >ネガティブ
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd[]" value="?" >疑問形
              </label>
              <label class="btn  btn-lg btn-default">
                <input type="checkbox" autocomplete="off" name="attd[]" value="retweets" >リツイートを含む
              </label>
            </div>
          </div>
      </div>
      <!-- ボタン -->
      <hr>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <input type="hidden" name="mode" value="go">
            <button id="btn_update" type="submit" class="btn btn-lg btn-danger btn-block">検索</button>
        </div>
      </div>
      <!-- .ボタン -->
      </form>

    </div>
    <!-- .検索窓 -->


    <div class="row col-md-12 " style="margin-top: 20px;">
      <?php echo $rtn_st; ?>
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
