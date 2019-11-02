<?php


require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//ログイン認証
require('auth.php');

//================================
// PHP処理。みんなの投稿データを引っ張ってくる。
//================================

//全てのレビューデータを取得する（ログイン有無に関わらない。トップページ用）
$topgames = getReviewsTop();
//配列を昇順にした。
arsort($topgames);


//$newreviews = getReviewsNew();
//debug("ここが新しい記事です。".$newreviews);

date_default_timezone_set('Asia/Tokyo');
$nowdate = date("Y-m-d H:i:s",strtotime("-3 day"));//strtotimeでdatetimeの方を調べる
debug("3日前".$nowdate);
//debug(gettype($nowdate));//型を調べる。

?>


<!--画面表示--------------------------------------------->

<?php $siteTitle = 'トップページ'; require('head.php'); ?>


<body>
<p id="js-show-msg" style="display:none;" class="msg-slide">
  <?php echo getSessionFlash('msg_success'); ?>
</p>
<?php require('header.php'); ?>

<section class="bg-box">
  <h2>Play and enjoy!</h2>
</section>

  <section class="about-wrapper">
    <div class="about-box">
      <div class="about-text-wrap">
        <h2 class="about-title">Switchインディーズライフとは？</h2>
        <p>たくさんのインディーズゲームがどんどん毎週のようにリリースされるNintendo Switch。<br>
          ゲームに関する感想、期待や攻略情報などのメモを投稿し、よりゲームを楽しむ手助けをするサイトです。<br>
          <br>
          <a href="https://twitter.com/intent/tweet?button_hashtag=Switchインディーズライフ&ref_src=twsrc%5Etfw" class="twitter-hashtag-button" data-show-count="false">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </p>
      </div>
    </div>
  </section>

  <section class="disc move">

    <h2 class="disc__about">ーできることー</h2>

    <div class="disc__about__wrapper">
      <div class="disc__card">
        <h3>投稿する</h3>
        <p>お気に入りのゲーム、気になるゲームについて投稿しよう。</p>
        <i class="fas fa-laptop disc__card__image"></i>
        <!--<img src="images/disc1.jpg" alt="" class="disc__card__image">-->
      </div>

      <div class="disc__card">
        <h3>コメントする</h3>
        <p>投稿にメモやコメントを投稿。</p>
        <i class="fas fa-comments disc__card__image"></i>
        <!--<img src="images/disc2.jpg" alt="" class="disc__card__image">-->
      </div>

      <div class="disc__card">
        <h3>お気に入り</h3>
        <p>投稿をお気に入りしておけばマイページから確認可能です。</p>
        <i class="far fa-star disc__card__image"></i>
        <!--<img src="images/disc2.jpg" alt="" class="disc__card__image">-->
      </div>
    </div>

  </section>

  <!--ゲームレビューエリア-->
  <section class="top-games">
    <div class="top-games__search">
      <h2>ー新着投稿一覧ー</h2>
    </div>


    <div class="top-posts">

      <?php foreach ($topgames as $row): ?>
        <article class="top-posts-sample">

          <div class="posts-text">
            <a href="reviewdetail.php?r_id=<?php echo h($row['id']) ?>">
              <?php echo h($row['title']);
              echo "<br>";?></a>
              <p><?php echo h($row['body']);
              echo "<br>";?>
              <a href="<?php echo h($row['abouturl']);?>">参考URL</a>
              投稿者：<?php echo h($row['username']); ?>
            </p>
          </div>
          <?php if ($row['create_date'] > $nowdate){  ?>
            <div class="top-posts__new">
              NEW!
            </div>
          <?php } ?>

          <div class="pic">
            <?php if ($row['pic']){  ?>
              <img src="<?php echo h($row['pic']);?>" alt="" style="width:400px;">
            <?php }else{ ?>
              <img src="images/disc1.jpg" alt="" style="width:400px;">
            <?php } ?>
          </div>
        </article>
      <?php endforeach; ?>

    </div>

    <div class="top-games__search">
      <a href="searchreviews.php"><button>もっと検索する</button></a>
    </div>

  </section>

</div>



<section class="top-signup">
  <?php
  if(empty($_SESSION['user_id'])){
    ?>
    <h2>早速始めよう！</h2>
    <button type="button" class="first-button">
      <a href="signup.php">ユーザー登録する</a><br>
    </button>

    <br>
    <?php
  }
  ?>
</section>

<?php
require('footer.php');
?>
