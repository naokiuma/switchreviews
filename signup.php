<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//すでにログインしている場合
//header("Location:index.php");

$page_flag = 0; //初期状態
if(!empty($_POST['btn_confirm']) ){
  $page_flag = 1; //確認ページフラグ
}elseif(!empty($_POST['btn_submit']) ){
  $page_flag = 2; //登録するフラグ
}elseif(!empty($_POST['cansell']) ){
  $page_flag = 0;
}


debug('ページフラグ:'.print_r($page_flag,true));
//postがある場合

if(!empty($_POST) && $page_flag === 1){

  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $pass_re = $_POST['pass_re'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';

  //未入力確認。
  validRequired($username,'username');
  validRequired($email,'email');
  validRequired($password,'password');
  validRequired($pass_re,'pass_re');
  validMinLen($password,'password');

  if(empty($err_msg)){
    validpassMatch($password,$pass_re,'pass_re');
    //validEmailcheck($email,'email');
    validEmailDup($email);
  }elseif(!empty($err_msg)){
    $page_flag = 0;
  }
}


if(empty($err_msg) && $page_flag === 2){
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $pass_re = $_POST['pass_re'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';

    //例外処理
    try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (username,email,password,pic,create_date) VALUES(:username,:email,:password,:pic,:create_date)';
        $data = array(':username' => $username, ':email' => $email, ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':pic' => $pic,
        ':create_date' => date('Y-m-d H:i:s'));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ成功
      if($stmt){
        //ログイン有効期限
        $sesLimit = 60*60;
        //最終ログイン日時を現在に。
        $_SESSION['login_date'] = time();
        $_SESSION['login_limit'] = $sesLimit;
        //ユーザーIDとサクセスメッセージを格納
        $_SESSION['user_id'] = $dbh->lastInsertId();
        $_SESSION['msg_success'] = SUC02;
        debug('セッション変数の中身:'.print_r($_SESSION,true));
        header("Location:index.php");//トップページへ
        }

      } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG03;
    }
  }
?>





<?php
  $siteTitle = "ユーザー登録";
  require("head.php")
?>

<body>

  <!--ヘッダー-->
<?php
require('header.php');
?>
<?php if($page_flag === 0): ?>
    <!--初期状態-->

  <section id ="main">
    <section id="form-container">

      <form id="form-main" action="" method="post" enctype="multipart/form-data">
        <h2 class="title">ユーザー登録をする</h2>


        <div class="area-msg">
          <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>

        <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
          ユーザー名<span>※あなたの名前です。</span><br>
          <input type="text" name="username" class="textbox" id="js-get-val-name" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?>
        </div>
        <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
          ユーザーID <span>※メールアドレスです。</span><br>
          <input type="email" name="email" class="textbox" id="js-get-val-id" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
        </div>

        <label class="<?php if(!empty($err_msg['password'])) echo 'err'; ?>">
          パスワード<span>※英数字6文字以上</span><br>
          <input type="password" name="password" class="textbox" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?>
        </div>

        <label class="<?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>">
          パスワード再入力<br>
          <input type="password" name="pass_re" class="textbox" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
        </div>
        <br>
        プロフィール画像
        <div class="imgdrop-container">
        <label class="area-drop">
          <input type="hidden" name="MAX_FILE_SIZE" value="9145728">
          <input type="file" name="pic" class="input-file" >
          <img src="" alt="" class="prev-img">
        </label>
        <div class="area-msg">
          <?php
          if(!empty($err_msg['pic'])) echo $err_msg['pic'];
          ?>
        </div>
        </div>

        <div class="btn-container">
          <input type="submit" class="btn" name="btn_confirm" value="確認する">
        </div>
      </form>
    </section>
  </section>



<?php elseif($page_flag === 1): ?>

  <section id ="main">
    <section id="form-container">
    <h2>この内容で投稿しますか？</h2>

  <form id="form-main" action="" method="post" enctype="multipart/form-data">
    <label>
    ユーザー名<br>
    <h2><?php if(!empty($_POST['username'])) echo $_POST['username']; ?></h2>
    <input type="text" name="username" class="textbox" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>" style="display:none;">
    </label>
    <label>
    ユーザーID<span>※メールアドレス</span><br>
    <h2><?php if(!empty($_POST['email'])) echo $_POST['email']; ?></h2>
    <input type="email" name="email" class="textbox" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" style="display:none;">
    </label>
    <label>
    パスワード<br>
    <h2><?php if(!empty($_POST['password'])) echo $_POST['password']; ?></h2>
    <input type="password" name="password" class="textbox" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>" style="display:none;">
    <!--パスさいにゅうりょく。これは見えなくて良い。-->
    <input type="password" name="pass_re" class="textbox" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>" style="display:none;">
    </label>
    プロフィール画像<br>
    <div class="imgdrop-container">
    <label for="input-file" class="area-drop">
      <input type="hidden" name="MAX_FILE_SIZE" value="9145728">
      <input type="file" name="pic" class="input-file">
      <img src="<?php if(!empty($pic)) echo $pic; ?>" alt="" class="prev-img">
    </label>
    </div>

    <div class="btn-container">
      <input type="submit" class="btn" name="btn_submit" value="登録する">
      <input type="submit" class="btn" name="cansell" value="戻る">
    </div>
  </form>

</section>
</section>

<?php endif; ?>

<?php
require('footer.php');
?>

  </body>
</html>
