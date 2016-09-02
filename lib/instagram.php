
<?php
require_once("config.php");
require_once("lib.php");
// OAuthライブラリの読み込み
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;


function f_twitter_tag($db_conn,$search_tag)
{
	if(strpos($search_tag,'#') === false){
  	//'abcd'のなかに'bc'が含まれていない場合
		$search_tag = '#'.$search_tag;		
	}
	$search_tag_exm = $search_tag ." -RT filter:images"; //検索キーワード, -RTはリトイートを除く
	//twitterAppsで取得
	$consumerKey        = TWITTER_KEY; //CONSUMER_KEY; // https://apps.twitter.com から取得
	$consumerSecret     = TWITTER_SECRET; //CONSUMER_SECRET;　// https://apps.twitter.com から取得
	$accessToken        = TWITTER_ACCESS_TOKEN; //ACCESS_TOKEN;　// https://apps.twitter.com から取得
	$accessTokenSecret  = TWITTER_TOKEN_SECRET; //ACCESS_TOKEN_SECRET;　// https://apps.twitter.com から取得
	//接続
	$TwitterOAuth = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
	$options = ['q' => $search_tag_exm ,'count' => '100', 'lang'=>'ja','result_type'=>'mixed'];

	$sns_id = 1;
	$since_id = f_get_sns_id_max($db_conn,$sns_id,$search_tag);
	if ($since_id){
	    $options['since_id'] = $since_id; //前回の最後に取得したツイートIDから
	}
	$statuses = $TwitterOAuth->get('search/tweets', $options)->statuses;

	if ($statuses && is_array($statuses)) 
	{
	    $sts_cnt = count($statuses);
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
			// entities
			// include_entities 
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
	           			$tweet_img = $tweet->entities->media[$key]->media_url;
	           			$tweet_img_short = $tweet->entities->media[$key]->url;
	           			$tweet_img_display_url = $tweet->entities->media[$key]->display_url;
	           			// DBへデータを書き込む処理
	           			$sns_id = 1; //Twitterは1
	           			$rtn = f_insert_shub_contents($db_conn,$sns_id, $search_tag, $user_id, $screen_name, $tweet_name, $tweet_icon, $id_str, $tweet_text, $tweet_date, $tweet_hashtag, $tweet_img, $tweet_img_short, $tweet_img_display_url);         			
	      			}
	 			}

			}
	    }
	}
return $search_tag;
}