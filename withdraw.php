<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// post送信されていた場合

if(!empty($_POST)){
  debug('post送信があります');
  //例外処理
  try{
    //dbへ接続
    $dbh = dbConnect();
    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
    $data = array(':us_id' => $_SESSION['user_id']);
    //クエリ実高
    $stmt = queryPost($dbh, $sql,$data);

    //クエリ実行成功の場合
    if($stmt){
      //セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header("Location:index.php");
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG03;
    }

  }catch(Exception $e){
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '退会';
require('head.php');
?>

    <style>
    .form .btn{
      float: none;
    }
    .form{
      text-align: center;
    }
    </style>

    <!--メニュー-->
    <?php
    require('header.php')
    ?>

    <!--メインコンテンツ-->
    <section id ="form-main">
      <div class="form-container">
        <form action="" method="post" class="form">
          <h2 class="title">退会しますか？</h2>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" name="submit" value="退会する" class="btn btn-mid ">
          </div>
        </form>
      </div>
      <a href="inde.php">トップページに戻る</a>
    </section>
  </div>

  <!-- footer -->
  <?php
  require('footer.php');
  ?>
