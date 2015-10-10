<?php

session_start();

require_once('config.php');
require_once('functions.php');

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

$post_id = $post['id'];
$post_name = $post['name'];
$post_body = $post['body'];


$comments = array();

$sql = "select * from comments where post_id = $post_id and status = 'active' order by created desc";
foreach ($dbh->query($sql) as $row) {
	array_push($comments, $row);
}



if ($_SERVER['REQUEST_METHOD'] != "POST") {

} else {

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
		$sql = "insert into comments
				(name, password, body, post_id, created, modified)
				values
				(:name, :password, :body, :post_id, now(), now())";
		$stmt = $dbh->prepare($sql);
		$params = array(
			":name" => $name,
			":password" => $password,
			":body" => $body,
			":post_id" => $post['id']
		);
		$stmt->execute($params);

		header('Location: '. $_SERVER['PHP_SELF']."?id=".$id);
		exit;
	}

}

?>


<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<link rel="stylesheet" href="assets/css/mypage_bootstrap.min.css.min.css">   
		<link rel="stylesheet" href="assets/css/mypage_font-awesome.css">
		<link id="theme-style" rel="stylesheet" href="assets/css/mypage_styles.css">
		<title>View</title>
	</head>
	<body>
		<header class="header">
		    <div class="container">                       
		        <div class="profile-content pull-left">
		            <h1 class="name">NAME : <?php echo $post['name']; ?></h1>
		            <h2 class="desc">POST AT : <?php echo $post['created']; ?></h2>   
		        </div>
		    </div>
		</header>
		
		<div class="container sections-wrapper">
		    <div class="row">
		        <div class="primary col-md-8 col-sm-12 col-xs-12">
		            <section class="about section">
		                <div class="section-inner">
		                    <h2 class="heading">POST</h2>
		                    <div class="content">
		                        <p>
		                            <?php echo $post['body']; ?>
		                        </p>    
		                    </div>
		                </div>
		            </section>
		        </div>
		        <div class="secondary col-md-4 col-sm-12 col-xs-12">                            
		            <aside class="languages aside section">
		                <div class="section-inner">
		                    <h2 class="heading">GIVE A COMMENT</h2>
		                    <div class="content">
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
	                    				COMMENT：
	                    			</p>
	                    			<p>
	                    				<textarea name="body" cols="40" rows="5"><?php echo $body; ?></textarea>
	                    				<span class="error">
	                    					<?php echo $error['body']; ?>
	                    				</span>
	                    			</p>

	                    			<p>
	                    				<input type="submit" value="コメント">
	                    			</p>
                    			</form>
		                    </div>
		                </div>
		            </aside>
		            <aside class="languages aside section">
		                <div class="section-inner">
		                    <h2 class="heading">COMMENT INDEX</h2>
		                    <div class="content">
                        		<ul class="list-unstyled" style ="list-style-type: none;">
                        			<?php foreach ($comments as $comment) : ?>
                        				<li class="item">
                         					<p>
                        						COMMENT : <?php echo $comment['body']; ?>
                        					</p>
                        					<p>
                        						NAME : <?php echo $comment['name']; ?>
                        					</p>
                        					<p>
                        						POST AT <?php echo $comment['created']; ?>
                        					</p>
                        					<p>
                        						<a href="comments/edit.php?id=<?php echo $comment['id']; ?>">
                        							EDIT 
                        						</a>
                        						 / 
                        						<a href="comments/delete.php?id=<?php echo $comment['id']; ?>">
                        							DELETE
                        						</a>
                        					</p>
                        				</li>
                        			<?php endforeach ; ?>
                        		</ul>
		                    </div>
		                </div>
		            </aside>
		        </div>
		    </div>
		</div>
		<p><a href="index.php">戻る</a></p>
	</body>
</html>