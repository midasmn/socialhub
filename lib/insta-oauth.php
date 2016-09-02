<?php
	require_once("config.php");
	// 設定
	$client_id = INSTA_CLIENT_ID;		// クライアントID
	$client_secret = INSTA_CLIENT_SECRET ;		// クライアントシークレット
	$redirect_uri = explode( '?' , ( !isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] )[0] ;		// このプログラムを設置するURL
	$scope = 'basic+comments+relationships+likes' ;		// スコープ

	// セッションスタート
	session_start() ;

	// HTML用
	$html = '' ;

	// [手順2] リクエストトークン($_GET["code"])とアクセストークンの交換
	if( isset($_GET['code']) && !empty($_GET['code']) && isset($_SESSION['state']) && !empty($_SESSION['state']) && isset($_GET['state']) && !empty($_GET['state']) && $_SESSION['state'] == $_GET['state'] )
	{
		// リクエスト用のコンテキストを作成
		$context = array(
			'http' => array(
				'method' => 'POST' ,
				'content' => http_build_query( array(
					'client_id' => $client_id ,
					'client_secret' => $client_secret ,
					'grant_type' => 'authorization_code' ,
					'redirect_uri' => $redirect_uri ,
					'code' => $_GET['code'] ,
				) ) ,
			) ,
		) ;

		// CURLを使ってリクエスト
		$curl = curl_init() ;

		// オプションのセット
		curl_setopt( $curl , CURLOPT_URL , 'https://api.instagram.com/oauth/access_token' ) ;
		curl_setopt( $curl , CURLOPT_HEADER, 1 ) ; 
		curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;			// メソッド
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;								// 証明書の検証を行わない
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;								// curl_execの結果を文字列で返す
		curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;			// リクエストボディ
		curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;										// タイムアウトの秒数

		// 実行
		$res1 = curl_exec( $curl ) ;
		$res2 = curl_getinfo( $curl ) ;

		// 終了
		curl_close( $curl ) ;

		// 取得したデータ
		$json = substr( $res1, $res2['header_size'] ) ;				// 取得したデータ(JSONなど)
		$header = substr( $res1, 0, $res2['header_size'] ) ;		// レスポンスヘッダー (検証に利用したい場合にどうぞ)

		// 取得したJSONをオブジェクトに変換
		$obj = json_decode( $json ) ;

		// エラー判定
		if( !$obj || !isset($obj->user->id) || !isset($obj->user->username) || !isset($obj->user->profile_picture) || !isset($obj->access_token) )
		{
			$error = 'データを上手く取得できませんでした…。' ;
		}
		else
		{
			// 各データを整理
			$user_id = $obj->user->id ;		// ユーザーID
			$user_name = $obj->user->username ;		// ユーザーネーム
			$user_picture = $obj->user->profile_picture ;		// ユーザーアイコン
			$access_token = $obj->access_token ;		// アクセストークン

			// セッション終了
			$_SESSION = array() ;
			session_destroy() ;

			// 出力する
			$html .=  '<h2>実行結果</h2>' ;
			$html .=  '<dl>' ;
			$html .=  	'<dt>ユーザーID</dt>' ;
			$html .=  		'<dd>' . $user_id . '</dd>' ;
			$html .=  	'<dt>ユーザー名</dt>' ;
			$html .=  		'<dd>' . $user_name . '</dd>' ;
			$html .=  	'<dt>アイコン画像</dt>' ;
			$html .=  		'<dd><img class="_img" src="' . $user_picture . '"></dd>' ;
			$html .=  	'<dt>アクセストークン</dt>' ;
			$html .=  		'<dd>' . $access_token . '</dd>' ;
			$html .=  '</dl>' ;
		}

		// 取得したデータ
		$html .= '<h2>取得したデータ</h2>' ;
		$html .= '<p>下記のデータを取得できました。</p>' ;
		$html .= 	'<h3>JSON</h3>' ;
		$html .= 	'<p><textarea rows="8">' . $json . '</textarea></p>' ;
		$html .= 	'<h3>レスポンスヘッダー</h3>' ;
		$html .= 	'<p><textarea rows="8">' . $header . '</textarea></p>' ;
	}

	// [手順1] 初回アクセスの場合、ユーザーをアプリ認証画面へアクセスさせる
	else
	{
		// CSRF対策
		session_regenerate_id( true ) ;
		$state = sha1( uniqid( mt_rand() , true ) ) ;
		$_SESSION['state'] = $state ;

		// リダイレクトさせる
		header( 'Location: https://api.instagram.com/oauth/authorize/?client_id=' . $client_id . '&redirect_uri=' . rawurlencode( $redirect_uri ) . '&scope=' . $scope . '&response_type=code&state=' . $state ) ;

		exit ;
	}

	// エラー時の処理
	if( isset($error) || !empty($error) )
	{
		$html = '<p><mark>' . $error . '</mark>もう一度、認証をするには、<a href="' . explode( '?' , $_SERVER['REQUEST_URI'] )[0] . '">こちら</a>をクリックして下さい。</p>' ;
	}

?>

<?php
	// ブラウザに[$html]を出力 (HTMLのヘッダーとフッターを付けましょう)
	echo $html ;
?>