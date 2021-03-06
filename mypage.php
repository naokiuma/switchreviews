<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================


//mypage。ユーザー情報をGETで取得する。
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';

//ユーザーデーターを取得。
$userData = getUser($u_id);
debug('$userDataの中身'.print_r($userData,true));

if(empty($userData)){
  $_SESSION['msg_success'] = MSG11;
  header("Location:index.php");

}

//DBからポストデータを取得
$reviewData = getMyreviews($u_id);
debug('$reviewDataの中身'.print_r($reviewData,true));

// DBからお気に入りデータを取得
$favData = getMyfav($u_id);
//debug('$favDataの中身'.print_r($favData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<!--HTML部分------------------------------->
<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<body class="page-mypage">

    <!-- メニュー -->
    <?php
      require('header.php');
    ?>

<section class="mypage-container">

  <section id="sidebar">
    <div class="myprof">
      <img src="<?php echo showImg($userData['pic']); ?>" alt="自分の画像">
      <h3>ユーザー名：<span><?php echo ($userData['username']); ?></span></h3>
      <h3>好きなゲーム：<span><?php echo ($userData['lovegame']); ?></span></h3>

      <!--もしアクセスしているユーザーの場合編集可能-->
      <?php if($_SESSION['user_id']== $u_id) : ?>
        <p><a href="profEdit.php">プロフィールを編集する</a></p>
      <?php endif; ?>




      <button type="button" name="button" class="fav-button">お気に入り投稿一覧</button>
    </div>
    <div class="fav-games">
      <?php
         foreach($favData as $val):
       ?>
       <div class="fav-game">
         <a href="reviewdetail.php?r_id=<?php echo h($val['id']) ?>"><?php echo ($val['title']); ?></a>
         <p><?php echo ($val['body']); ?><p>
         <img src="<?php echo ($val['pic']); ?>">
       </div>
       <?php
         endforeach;
       ?>
     </div>
  </section>

  <section id="mypage-posts">
    <h2>投稿一覧</h2>
    <?php
       foreach($reviewData as $val):
     ?>
     <a href="reviewdetail.php?r_id=<?php echo htmlspecialchars($val['id']) ?>" style="color:black; font-size:25px;"><?php echo ($val['title']); ?></a>
     <p><?php echo ($val['body']); ?></p>
     <img src="<?php echo ($val['pic']); ?>">
     <p>参考URL：<?php echo ($val['abouturl']); ?></p>
     <?php
       endforeach;
     ?>
  </section>
</section>
  <!-- footer -->
<?php
  require('footer.php');
?>

</body>
</html>
