<?php
if(!empty($_SESSION['user_id'])){
  $user = getUser($_SESSION['user_id']);
  $user_menuname = ($user['username']);
}
?>

<header>
  <div class="header-wrapper">
    <div class="header-logo">
      <a href="index.php"><img src="images/logo.png" alt=""></a>
    </div>
    <div id="top-nav">
      <div class="menu__trigger">
        <?php
          if(!empty($_SESSION['user_id'])){
        ?>
        <h4><?php echo h($user_menuname); ?></h4>
        <?php
          }
        ?>

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
    <li><a href="searchreviews.php"><i class="fas fa-gamepad"></i> 投稿をさがす</a></li>
    <li><a href="reviewpost.php"><i class="fas fa-gamepad"></i> 投稿する</a></li>
    <li><a href="twitter.php"><i class="fas fa-gamepad"></i>ツイートを見てみる</a></li>
    <li><a href="profEdit.php"><i class="fas fa-gamepad"></i> プロフィール修正</a></li>
    <li><a href="logout.php"><i class="fas fa-gamepad"></i> ログアウト</a></li>
    <li><a href="news.php"><i class="fas fa-gamepad"></i>ニュース</a></li>

  <?php
    }else{
  ?>
  <li><a href="signup.php"><i class="fas fa-gamepad"></i> ユーザー登録</a></li>
  <li><a href="login.php"><i class="fas fa-gamepad"></i> ログイン</a></li>
  <li><a href="searchreviews.php"><i class="fas fa-gamepad"></i> 投稿をさがす</a></li>
  <li><a href="news.php"><i class="fas fa-gamepad"></i>ニュース</a></li>

  <?php
    }
  ?>
</ul>
</div>
