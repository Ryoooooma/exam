<?php

// Data Souce Name の略で、DB接続時の第一引数に必要となる（ポート番号の指定も可能）
define('DSN', 'mysql:host=localhost;dbname=exam');

// DB接続時の第二引数に必要となる
define('DB_USER', 'root');

// DB接続時の第三引数に必要となる
define('DB_PASSWORD', 'root');

// 相対バス、絶対パスどちらで指定しているかを認識していくこと。
// [ アドバンス ]階層が変わることを考える（拡張性）と相対パスで指定
define('SITE_URL', 'http://192.168.33.10/exam/');

error_reporting(E_ALL & ~E_NOTICE);

// session_set_cookie_params(0, '/twitter_2/');
