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

$stmt = $dbh->prepare("select * from comments where id = :id limit 1");
$stmt->execute(array(":id" => $id));
$comment = $stmt->fetch() or die("no one found!");

if ($_SERVER['REQUEST_METHOD'] != "POST") {

	// setToken();	

} else {

	// checkToken();

	$password = $_POST['password'];

	$error = array();
	
	// パスワードが空かどうかチェック
	if ($password == '') {
		$error['password'] = 'パスワードを入力してください';
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

	if (empty($error)) {

		$sql = "update comments set status = 'deleted' where id =$id";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();

		header('Location: ../view.php?id='. $comment['post_id']);
		exit;
	}
}

?>


<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>データの削除</title>
	</head>
	<body>
		<h1>削除しますか？</h1>
		<form action="" method="POST">
			<p>
				パスワード：
				<input type="password" name="password" value=""> 
				<span class="error">
					<?php echo $error['password']; ?>
				</span>
			</p>

			<p>
				<input type="submit" value=" DELETE">
				<input type="hidden" name="id" value="">
			</p>
		</form>
		<p><a href="../view.php?id=<?php echo $comment['post_id']; ?>">BACK</a></p>

	</body>
</html>