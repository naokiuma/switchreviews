<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================


//mypage。ユーザー情報をGETで取得する。
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
if(empty($u_id)){
  error_log('ユーザーIDがありません。');
  header("Location:index.php");
}

//ユーザーデーターを取得。
$userData = getUser($u_id);
//debug('$userDataの中身'.print_r($userData,true));

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

<section class="page-2colum">

  <section id="sidebar">
    <div class="myprof">
      <img src="<?php echo showImg($userData['pic']); ?>" alt="自分の画像" style="width:90%; margin:20px 20px;">
      <h3><?php echo ($userData['username']); ?></h3>
      <p>一言：<?php echo ($userData['lovegame']); ?></p>
      <p><button type="button" name="button" class="fav-button">お気に入り一覧</button></p>
      <div class="js-get-id">ここにidが入る。</div>
    </div>
    <div class="fav-games">
      <?php
         foreach($favData as $val):
       ?>
       <a href="reviewdetail.php?r_id=<?php echo htmlspecialchars($val['id']) ?>"><?php echo ($val['title']); ?></a><br>
       <?php echo ($val['body']); ?><br>
       <img src="<?php echo ($val['pic']); ?>"><br><br>
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

<script>
$('.js-get-id').html($.cookie('pageid'));
</script>

</body>
</html>
