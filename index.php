<?php


require('function.php');
//require('twitter.php');


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
  <h2>Play and enjoy!<br>ゲームの楽しさは外側にも広がります。</h2>
</section>

<div class="top_news">
  <div class="top_news__left">
    <h2>NEWS</h2>
    <p><a href="#">More　<i class="fas fa-arrow-right"></i></a></p>
  </div>
  <div class="top_news__right">
    <ul>
     <li>
      <a href="#">
        <time datetime="2019-11-12">2019/11/12</time><span>サイトを公開しました。</span>
      </a>
     </li>
     <li>
      <a href="#">
        <time datetime="2019-11-12">2019/11/12</time><span>サイトを公開しました。</span>
      </a>
     </li>
     <li>
      <a href="#">
        <time datetime="2019-11-12">2019/11/12</time><span>サイトを公開しました。</span>
      </a>
     </li>
    </ul>
  </div>
</div>

  <section class="about__wrapper">
    <div class="about__box">
      <div class="about-text-wrap">
        <h3 class="about-title">About</h3>
        <p>毎週多くのインディーズゲームがリリースされるNintendo Switch。<br>
          有名で良質なゲームも多くありますが、目に止まりにくい良質なゲームだって沢山あります。<br>
          そんなゲームに関する感想や期待、攻略方法なんかを語り合ったり、ツイッターでゲームについて呟いている人を見つけたり。<br>
          Nintendo Switch Indies LiFEは、もっとゲームを楽しむためのサイトです。<br>
          <br>
          <a href="https://twitter.com/intent/tweet?button_hashtag=Switchインディーズライフ&ref_src=twsrc%5Etfw" class="twitter-hashtag-button" data-show-count="false">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </p>
      </div>
      <img class="about__box_img3" src="images/disc3.jpg" alt="">
      <img class="about__box_img2" src="images/disc2.jpg" alt="">
    </div>
<!--
    <div class="about-img__wrapper">
      <img src="images/disc4.png" alt="">
      <img src="images/disc2.jpg" alt="">
    </div>
-->
  </section>

  <section class="disc">

    <h2 class="disc__about">できること</h2>

    <div class="disc__about__wrapper">
      <div class="disc__card">
        <h3>投稿する</h3>
        <p>お気に入りのゲーム、気になるゲームについて投稿しよう。</p>
        <i class="fas fa-laptop disc__card__image"></i>
      </div>

      <div class="disc__card">
        <h3>コメントする</h3>
        <p>投稿にメモやコメントを投稿。</p>
        <i class="fas fa-comments disc__card__image"></i>
      </div>

      <div class="disc__card">
        <h3>お気に入り</h3>
        <p>投稿をお気に入りしておけばマイページから確認可能です。</p>
        <i class="far fa-star disc__card__image"></i>
      </div>

      <div class="disc__tweet">
        <h3>ツイッターで見つける</h3>
        <p>本サービス内で新しく投稿されたゲームや、話題のゲームに関するツイートを収集、表示。<br>気になるゲームの話題をツイートしているアカウントを見つけ、フォローしよう！</p>
        <i class="fab fa-twitter-square disc__card__image"></i>
      </div>
    </div>

  </section>

  <!--ゲームレビューエリア-->
  <section class="top-games">
    <div class="top-games__search">
      <h2 class="top-games__search__text">新着投稿一覧</h2>
    </div>
    <p>新しいポスト順に表示します。<a class="more_link" href="searchreviews.php"><i class="fas fa-arrow-right"></i>もっと見る</a>
    </p>


    <div class="top-posts">

      <?php foreach ($topgames as $row): ?>
        <article class="top-posts-sample">

          <div class="posts-text">
            <a href="reviewdetail.php?r_id=<?php echo h($row['id']) ?>">
              <?php echo h($row['title']);
              echo "<br>";?></a>
              <p class="overflow-ellipsis"><?php echo h($row['body']);?><br>
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
              <img src="<?php echo h($row['pic']);?>" alt="">
            <?php }else{ ?>
              <img src="images/disc1.jpg" alt="">
            <?php } ?>
          </div>
        </article>
      <?php endforeach; ?>

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
