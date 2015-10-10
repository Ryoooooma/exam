<?php

require_once('../config.php');
require_once('../functions.php');

$dbh = connectDb();

if (preg_match('/^[1-9][0-9]*$/', $_GET['id'])) {
	$id = (int)$_GET['id'];
} else {
	echo "不正なIDです。";
	exit;
}

$stmt = $dbh->prepare("select * from posts where id = :id limit 1");
$stmt->execute(array(":id" => $id));
$post = $stmt->fetch() or die("no one found!");

if ($_SERVER['REQUEST_METHOD'] != "POST") {

	// setToken();	

} else {

	// checkToken();

	$name = $_POST['name'];
	$password = $_POST['password'];
	$body = $_POST['body'];

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

	// ブログ記事の長さチェック(400字以下)
	if (!$body == '') {
		if (strlen($body) > 400) {
			$error['body'] = 'ブログ内容は400字以下でお願いします';
		}
	}

	if (empty($error)) {

		$sql = "update posts set
				name = :name,
				password = :password,
				body = :body,
				created = now(),
				modified = now()
				where id = :id";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":name" => $name,
			":password" => $password,
			":body" => $body,
			":id" => $id
		);
		$stmt->execute($params);

		header('Location: '.SITE_URL);
		exit;
	}
}


?>


<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>データの編集</title>
	</head>
	<body>
		<h1>【編集する】</h1>
		<form action="" method="POST">

			<p>
				お名前：
				<input type="text" name="name" value="<?php echo $post['name']; ?>"> 
				<span class="error">
					<?php echo $error['name']; ?>
				</span>
			</p>

			<p>
				パスワード：
				<input type="password" name="password" value=""> 
				<span class="error">
					<?php echo $error['password']; ?>
				</span>
			</p>

			<p>
				記事内容：
			</p>
			<p>
				<textarea name="body" cols="40" rows="5"><?php echo $post['body']; ?></textarea>
				<span class="error">
					<?php echo $error['body']; ?>
				</span>
			</p>

			<p>
				<input type="submit" value="更新！">
				<input type="hidden" name="id" value="">
			</p>
		</form>
		<p><a href="index.php">戻る</a></p>

	</body>
</html>