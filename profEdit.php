<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
debug('ユーザーid情報：'.print_r(($_SESSION['user_id']),true));
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));

  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $email = $_POST['email'];
  $lovegame = $_POST['lovegame'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';

  if($dbFormData['username'] !== $username){
    validRequired($username, 'username');
  }
  if($dbFormData['email'] !== $email){
    validRequired($email, 'email');
    validEmailDup($email);
}

  if(empty($err_msg)){
    debug('バリデーションOKです。');

      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET username = :u_name, email = :email, lovegame = :lovegame, pic = :pic WHERE id = :u_id';
        $data = array(':u_name' => $username, ':email' => $email, ':lovegame' => $lovegame, ':pic' => $pic, ':u_id' => $dbFormData['id']);
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt){
          debug('クエリ成功。');
          debug('トップページへ遷移します。');
          $_SESSION['msg_success'] = SUC03;
          header("Location:index.php");
        }else{
          debug('クエリに失敗しました');
          $err_msg['commmon'] = MSG03;
      }

    } catch (Exception $e) {
      erro_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG03;
    }
  }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

  <body>

    <!-- メニュー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
      <section id="main">
        <section id="form-container">
          <form action="" method="post" id="form-main" enctype="multipart/form-data">
            <h2 class="title">プロフィール編集</h2>

            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
             名前<br>
              <input type="text" name="username" class="textbox" value="<?php echo getFormData('username'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['username'])) echo $err_msg['username'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              Email<br>
              <input type="text" name="email" class="textbox" value="<?php echo getFormData('email'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>

            <label>
              好きなゲームのタイトルやカテゴリー<br>
              <input type="text" name="lovegame" class="textbox" value="<?php echo getFormData('lovegame'); ?>">
            </label>
            プロフィール画像
            <div class="imgdrop-container">
            <label for="input-file" class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height: 300px; line-height: 300px; width:400px; position: relative; border: medium none; z-index:3;" >
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic" class="input-file" style="height:300px; width:400px;">
              <img src="<?php echo getFormData('pic'); ?>" class="prev-img" style="width:400px; height:300px; position:absolute;left:0px; <?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['pic'])) echo $err_msg['pic'];
              ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更する">
            </div>
          </form>
        </div>
      </section>
      </section>

    <!-- footer -->
    <?php
    require('footer.php');
    ?>

  </body>
?>
