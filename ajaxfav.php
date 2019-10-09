<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['review_id']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります。');
  $r_id = $_POST['review_id'];
  debug('レビューIDだ！！！：'.$r_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // レコードがあるか検索
    // likeという単語はLIKE検索とうSQLの命令文で使われているため、そのままでは使えないため、｀（バッククウォート）で囲む
    $sql = 'SELECT * FROM fav WHERE review_id = :r_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':r_id' => $r_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    // レコードが１件でもある場合
    if(!empty($resultCount)){
      // レコードを削除する
      $sql = 'DELETE FROM fav WHERE review_id = :r_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':r_id' => $r_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      // レコードを挿入する
      $sql = 'INSERT INTO fav (user_id, review_id, create_date) VALUES (:u_id, :r_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':r_id' => $r_id, ':date' => date('Y-m-d H:i:s'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
