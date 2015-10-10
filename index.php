<?php

session_start();

require_once('config.php');
require_once('functions.php');


// 記事一覧関連コード
$dbh = connectDb();

$posts = array();

// 記事一覧を最新順で全県取得
$sql = "select * from posts where status = 'active' order by created desc";
foreach ($dbh->query($sql) as $row) {
	array_push($posts, $row);
}



if ($_SERVER['REQUEST_METHOD'] != "POST") {
	
	// setToken();	

} else {

	// checkToken();

	$name = $_POST['name'];
	$password = $_POST['password'];
	$body = $_POST['body'];
	$_SESSION['name'] = $_POST['name'];

	$dbh = connectDb();

	$error = array();
	
	// 名前が空かどうかチェック
	if ($name == '') {
		$error['name'] = 'お名前を入力してください';
	}
	
	// パスワードが空かどうかチェック
	if ($password == '') {
		$error['password'] = 'パスワードを入力してください';
	}

	// 記事内容が空かどうかチェック
	if ($body == '') {
		$error['body'] = '記事内容を入力してください';
	}

	// 名前の長さチェック(4字以上8字以下)
	if (!$name == '') {
		if (strlen($name) < 4) {
			$error['name'] = 'お名前は4以上でお願いします';
		}
		if (strlen($name) > 8) {
			$error['name'] = 'お名前は8字以下でお願いします';
		}
	}

	// パスワードの長さチェック(4字以上8字以下)
	if (!$password == '') {
		if (strlen($password) < 4) {
			$error['password'] = 'パスワードは4字以上でお願いします';
		}
		if (strlen($password) > 8) {
			$error['password'] = 'パスワードは8字以下でお願いします';
		}
	}

	// 記事の長さチェック(400字以下)
	if (!$body == '') {
		if (strlen($body) > 400) {
			$error['body'] = '記事内容は400字以下でお願いします';
		}
	}



	// 上記のエラーチェックをパスしたら登録処理を実行する
	if (empty($error)) {
		$sql = "insert into posts
				(name, password, body, created, modified)
				values
				(:name, :password, :body, now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":name" => $name,
			":password" => $password,
			":body" => $body
		);
		$stmt->execute($params);

		// 登録処理後、ログインページへリダイレクト処理をおこなう
		header('Location: '.SITE_URL);
		exit;
	}

}

?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<!-- 設定各種 -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<!-- トップ全体のCSS -->
		<link rel="stylesheet" type="text/css" href="assets/css/demo.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/component.css" />
		<script src="assets/js/modernizr.custom.js"></script>
		<!-- index用のCSS -->
		<link rel="stylesheet" type="text/css" href="assets/css/timeline_default.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/timeline_component.css" />
	</head>
	<body>
		<div class="container">
			<div id="splitlayout" class="splitlayout">
				<div class="intro">
					<div class="side side-left">
						<header class="codropsheader clearfix">
							<h1>Nexseed Board</h1>
						</header>
						<div class="intro-content">
							<h1><span>INDEX</span><span>check friend'S posts</span></h1>
						</div>
						<div class="overlay"></div>
					</div>
					<div class="side side-right">
						<div class="intro-content">
							<h1><span>POST</span><span>share yout emotion</span></h1>
						</div>
						<div class="overlay"></div>
					</div>
				</div>

				<!-- ここから右側スライドのページ -->
				<div class="page page-right">
					<div class="page-inner">
						<section>
							<form action="" method="POST">
								<p>
									NAME：
									<input type="text" name="name" value="<?php echo $name; ?>"> 
									<span class="error">
										<?php echo $error['name']; ?>
									</span>
								</p>
								<p>
									PASSWORD：
									<input type="password" name="password" value=""> 
									<span class="error">
										<?php echo $error['password']; ?>
									</span>
								</p>
								<p>
									POST：
									<input type="text" name="body" value="<?php echo $body; ?>"> 
									<span class="error">
										<?php echo $error['body']; ?>
									</span>
								</p>								
								<p>
									<input type="submit" value="SUBMIT">
									<input type="hidden" name="id" value="">
								</p>
							</form>
						</section>
					</div>
				</div>
				<!-- ここまで右側スライドのページ -->

				<!-- ここから左側スライドのページ -->
				<div class="page page-left">
					<div class="page-inner">
						<h2>INDEX</h2>
						<p>Last Post : <?php echo $_SESSION['name']; ?></p>
						<ul>
							<?php foreach ($posts as $post) : ?>
								<li>
									<p>
										<a href="view.php?id=<?php echo $post['id']; ?>">
											<?php echo h($post['body']); ?>
										</a>
									</p>
									<p>
										<?php echo h($post['name']); ?>
									</p>
									<p>
										<?php echo h($post['created']); ?>
									</p>
									<p>
										<a href="edit.php?id=<?php echo $post['id']; ?>">
											EDIT  
										</a>
										<a href="delete.php?id=<?php echo $post['id']; ?>">
											DELETE
										</a>
									</p>
									<br>
								</li>
							<?php endforeach ; ?>
						</ul>
						<div class="container">
							<div class="main">
								<ul class="cbp_tmtimeline">
									<li>
										<time class="cbp_tmtime" datetime="2015-10-10 00:52:04"><span>2015-10-10 00:52:04</span></time>
										<div class="cbp_tmicon cbp_tmicon-phone"></div>
										<div class="cbp_tmlabel">
											<h2>ここに名前を入力</h2>
											<p>本日は晴れなり。良い天気かな。楽しい日になるとよいのだが。</p>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<!-- ここまで左側スライドのページ -->

				<!-- スライド後の戻るボタン -->
				<a href="#" class="back back-right" title="back to intro">&rarr;</a>
				<a href="#" class="back back-left" title="back to intro">&larr;</a>
			</div>
		</div>
		<script src="assets/js/classie.js"></script>
		<script src="assets/js/cbpSplitLayout.js"></script>
	</body>
</html>