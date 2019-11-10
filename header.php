<header>
  <div class="header-wrapper">
    <div class="header-logo">
      <a href="index.php">Switchインディーズライフ</a>
    </div>
    <div id="top-nav">
      <div class="menu__trigger">
        <button class="js-toggle-sp-menu" type="button" name="button">MENU</button>
      </div>
    </div>
  </div>

</header>
<div class="toggle_menu">
<ul>
  <?php
    if(!empty($_SESSION['user_id'])){
  ?>
    <li><a href="mypage.php?u_id=<?php echo h($_SESSION['user_id']) ?>"><i class="fas fa-gamepad my-gray"></i>マイページ</a></li>
    <li><a href="index.php"><i class="fas fa-gamepad"></i> トップページ</a></li>
    <li><a href="searchreviews.php"><i class="fas fa-gamepad"></i> 記事一覧</a></li>
    <li><a href="reviewpost.php"><i class="fas fa-gamepad"></i> レビュー投稿</a></li>
    <li><a href="profEdit.php"><i class="fas fa-gamepad"></i> プロフィール編集</a></li>
    <li><a href="logout.php"><i class="fas fa-gamepad"></i> ログアウト</a></li>
    <li><a href="withdraw.php"><i class="fas fa-gamepad"></i> 退会する</a></li>

  <?php
    }else{
  ?>

  <li><a href="index.php"><i class="fas fa-gamepad my-gray"></i> トップページ</a></li>
  <li><a href="searchreviews.php"><i class="fas fa-gamepad"></i> 記事一覧</a></li>
  <li><a href="signup.php"><i class="fas fa-gamepad"></i> ユーザー登録</a></li>
  <li><a href="login.php"><i class="fas fa-gamepad"></i> ログイン</a></li>

  <?php
    }
  ?>
</ul>
</div>
