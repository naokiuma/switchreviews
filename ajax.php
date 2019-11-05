
<?php
require('function.php');

if(!empty($_POST)){
  //検索カテゴリーapp.jsから。
    //echo json_encode(array('id'=> $_POST['id']));デバッグ用
    /*
  $dsn = 'mysql:dbname=switchreview;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時に例外をスロー
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
);*/

  // PDOオブジェクト生成（DBへ接続）
  //$dbh = new PDO($dsn, $user, $password, $options);
  $dbh = dbConnect();
  $stmt = $dbh->prepare('SELECT * FROM reviews WHERE category_id = :id');
  //プレースホルダに値をセットし、SQL文を実行
  $stmt->execute(array(':id' => $_POST['id']));

  //$result = $stmt->fetch(PDO::FETCH_ASSOC);
  $result = $stmt->rowCount();
  echo($result);
  //echo json_encode(array('count' => $result));

  //if(!empty($result)){
    //echo json_encode(array('count' => $result));
    //
    //  }
  exit();
}
