
<?php
require_once("config.php");
require_once("lib.php");
// OAuthライブラリの読み込み
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;


function f_twitter_api($db_conn,$options)
{
	//twitterAppsで取得
	$consumerKey        = TWITTER_KEY; //CONSUMER_KEY; // https://apps.twitter.com から取得
	$consumerSecret     = TWITTER_SECRET; //CONSUMER_SECRET;　// https://apps.twitter.com から取得
	$accessToken        = TWITTER_ACCESS_TOKEN; //ACCESS_TOKEN;　// https://apps.twitter.com から取得
	$accessTokenSecret  = TWITTER_TOKEN_SECRET; //ACCESS_TOKEN_SECRET;　// https://apps.twitter.com から取得
	//接続
	$TwitterOAuth = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
	$statuses = $TwitterOAuth->get('search/tweets', $options)->statuses;
	if ($statuses && is_array($statuses)) 
	{
		return $statuses;
	}else{
		return "ZERO";
	}
}

// 表示テスト用
function f_tw_test($statuses)
{
	$sts_cnt = count($statuses);
	echo "<br>件数:".$sts_cnt;
	// 一番古いデータからDBへ書き込む
	for ($i = $sts_cnt-1; $i >= 0; $i--) 
	{
		$tweet_hashtag = "";
		$tweet_img = $tweet_img_short = $tweet_img_display_url = "";
		$tweet = $statuses[$i];
		// var_dump($tweet);
		// echo "<hr>";
		$has_media = true;
		$screen_name = $tweet->user->screen_name;
		$user_id = $tweet->user->id;
		$id_str = $tweet->id_str;
		$since_id = $tweet->since_id;
		$tweet_text = $tweet->text;
		$tweet_name = $tweet->user->name;
		$tweet_icon = $tweet->user->profile_image_url;
		$tweet_date = date('Y-m-d H:i:s', strtotime($tweet->created_at)) ;
		// 
		// echo "<br>screen_name=".$screen_name;
		// echo "<br>user_id=".$user_id;
		// echo "<br>since_id=".$since_id;
		// echo "<br>id_str=".$id_str;
		// echo "<br>tweet_text=".$tweet_text;
		// echo "<br>tweet_name=".$tweet_name;
		// echo '<br>tweet_icon=<img src="'.$tweet_icon.'">';
		// echo "<br>tweet_date=".$tweet_date;		
		foreach($tweet->entities->hashtags as $hashtag) 
		{
			$tweet_hashtag .= $hashtag->text.",";
		}
		// echo "<br>tweet_hashtag=".$tweet_hashtag;
		// 
		if (is_array($tweet->entities->media)) 
		{
			foreach($tweet->entities->media as $key => $media) 
			{
				if (isset($tweet->entities->media[$key])) 
				{
					$tweet_img = $tweet->entities->media[$key]->media_url;//画像URL
					$tweet_img_short = $tweet->entities->media[$key]->url;//画像短縮URL
					$tweet_img_display_url = $tweet->entities->media[$key]->display_url;//表示表URL
					// 
					$tweet_img_expanded_url = $tweet->entities->media[$key]->expanded_url;//動画TweetURL
					
					// echo '<br>tweet_img=<img src="'.$tweet_img.'">';
					// echo "<br>tweet_img_expanded_url=".$tweet_img_expanded_url;
					// DBへデータを書き込む処理
					$sns_id = 1; //Twitterは1
					// $rtn = f_insert_shub_contents($db_conn,$sns_id, $search_tag, $user_id, $screen_name, $tweet_name, $tweet_icon, $id_str, $tweet_text, $tweet_date, $tweet_hashtag, $tweet_img, $tweet_img_short, $tweet_img_display_url);   
				}
			}
		}
	}
}

	  



// 表示
function f_disp_result($statuses)
{
	$sts_cnt = count($statuses);
	// 
	$rtn_st = '';
	$rtn_st .= '<div class="col-xs-12 col-md-12" >';
	$rtn_st .=   '<h5 class="col-md-8" style="margin-bottom: 20px;;font-weight:200;1px;text-shadow:1px1px 0 rgba(0,0,0,0.1);color:#fff;">'."\n";
	$rtn_st .= "検索結果：".number_format($sts_cnt)."件";
	$rtn_st .= '</h5>';
	$rtn_st .= '</div>';
	// 一番古いデータからDBへ書き込む
	for ($i = $sts_cnt-1; $i >= 0; $i--) 
	{
		$tweet_hashtag = "";
		$tweet_img = $tweet_img_short = $tweet_img_display_url = "";
		$tweet = $statuses[$i];
		// var_dump($tweet);
		// echo "<hr>";
		$has_media = true;
		$screen_name = $tweet->user->screen_name;
		$user_id = $tweet->user->id;
		$id_str = $tweet->id_str;
		$since_id = $tweet->since_id;
		$tweet_text = $tweet->text;
		$tweet_name = $tweet->user->name;
		$tweet_icon = $tweet->user->profile_image_url;
		$tweet_date = date('Y-m-d H:i:s', strtotime($tweet->created_at)) ;
		// 	
		foreach($tweet->entities->hashtags as $hashtag) 
		{
			$tweet_hashtag .= $hashtag->text.",";
		}
		// echo "<br>tweet_hashtag=".$tweet_hashtag;
		// 
		if (is_array($tweet->entities->media)) 
		{
			foreach($tweet->entities->media as $key => $media) 
			{
				if (isset($tweet->entities->media[$key])) 
				{
					$tweet_img = $tweet->entities->media[$key]->media_url;//画像URL
					$tweet_img_short = $tweet->entities->media[$key]->url;//画像短縮URL
					$tweet_img_display_url = $tweet->entities->media[$key]->display_url;//表示表URL
					// 
					$tweet_img_expanded_url = $tweet->entities->media[$key]->expanded_url;//動画TweetURL
					$sns_id = 1; //Twitterは1
					// DBに書き込み
				}
			}
		}
		// 
		$url ='https://twitter.com/search?q='.$screen_name.'" target="_blank"';
        // タブ用画像パス
        $img_path = $tweet_img;
        $url_ch = "detail.php?id=".$id;
        $rtn_st .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-2" >';
        $rtn_st .= '<div class="thumbnail  ">';
        $rtn_st .= '<a href="'.$url_ch.'"><img  src="'.$img_path.'">';
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
	}
	$rtn_st .= '</div>';
	return $rtn_st;
}