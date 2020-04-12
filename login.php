<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合

if(!empty($_POST)){
  debug("POST送信あり。");

  $email = $_POST['email'];
  $password = $_POST['password'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  validRequired($email,'email');
  validRequired($password,'password');

  if(empty($err_msg)){
    debug('バリデーションokです。');

    try{

      $dbh = dbConnect();
      $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      //クエリ成功
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身:'.print_r($result,true));

      //パスワード照合
      if(!empty($result) && password_verify($password,array_shift($result))){
        debug('パスワードがマッチしました');

          //ログイン有効期限
          $sesLimit = 60 * 60;
          $_SESSION['login_date'] = time(); //ログインデータは今の時間

          //ログイン保持にチェックがある場合
          if($pass_save){
            debug('ログイン保持にチェックがあります。');
            //ログイン有効期限を30日にする
            $_SESSION['login_limit'] = $sesLimit * 24 * 30;
          }else{
            debug('ログイン保持にチェックはありません。');
            //この場合はログイン有効期限は1時間のまま
            $_SESSION['login_limit'] = $sesLimit;
          }
 
          //ユーザーIDとサクセスメッセージをセッションに格納
          $_SESSION['user_id'] = $result['id'];
          $_SESSION['msg_success'] = SUC01;
          debug('セッション変数の中身:'.print_r($_SESSION,true));
          header("Location:index.php");//トップページへ
          return;



        }else{
          debug('パスワードが違います');
          $err_msg['common'] = MSG05;
        }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG03;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
  $siteTitle = "ログイン";
  require("head.php")
?>

<body>

  <!--ヘッダー-->
  <?php
  require('header.php');
  ?>
  <!--フラッシュメッセージ-->
  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <section id ="main">
    <section id="form-container">

      <form id="form-main" action="" method="post">
        <h2 class="title">ログイン</h2>
        <div class="area-msg">
          <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>

        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
          ユーザーID<span> ※メールアドレス</span><br>
          <input type="email" name="email" class="textbox" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
        </div>
        <label class="<?php if(!empty($err_msg['password'])) echo 'err'; ?>">
          パスワード<br>
          <input type="password" name="password" class="textbox" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?>
        </div>
        <label>
          <input type="checkbox" name="pass_save">
          ログイン情報を保持する<br>
        </label>

        <div class="btn-container">
          <input type="submit" class="btn" value="ログインする">
        </div>

      </div>

      </section>
  </section>

  <?php
  require('footer.php');
  ?>
  </body>
</html>
