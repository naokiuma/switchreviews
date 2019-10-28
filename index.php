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

?>


<!--画面表示--------------------------------------------->

<?php
$siteTitle = 'トップページ';
require('head.php');
?>

<body>
  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>
  <?php
  require('header.php');
  ?>

  <section class="bg-box">
    <h2>Play and enjoy!</h2>
  </section>

  <section class="about-wrapper">
    <div class="about-right">
      <div class="about-text-wrap">
        <h2 class="about-title">Switchインディーズライフとは？</h2>
          <p>たくさんのインディーズゲームがどんどんどん毎週のようにリリースされるswitch。<br>
            レビューして、質問して、コメントして、お気に入りして。<br>
            スイッチインディーズゲームをもっと楽しむためのサイトです。<br>
            <br>
            <a href="https://twitter.com/intent/tweet?button_hashtag=スイッチインディーズライフ&ref_src=twsrc%5Etfw" class="twitter-hashtag-button" data-show-count="false">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

        </p>
      </div>
    </div>
  </section>

  <section class="disc">
    <h1 class="about-h1">About</h1>
    <div class="disc-left">
      <br><h3>投稿する</h3>
      <p>感想や攻略メモ、欲しいゲームでもどんどん投稿しよう！<br></p>
      <img src="images/disc1.jpg" alt="" class="disc-left-image">

    </div>

    <div class="disc-center">
      <br><h3>コメント/お気に入り</h3>
      <p>投稿にコメントして交流しよう。<br>
      攻略のメモ情報なんかももちろんOK!</p>
      <img src="images/disc2.jpg" alt="" class="disc-center-image">
    </div>


  </section>

<!--ゲームレビューエリア-->
  <section class="top-games">
    <h2>最近の投稿一覧</h2>
    <a href="searchreviews.php"><button>もっと検索する</button></a>

    <div class="top-posts">

    <?php foreach ($topgames as $row): ?>
    <article class="top-posts-sample">

        <div class="posts-text">
        <a href="reviewdetail.php?r_id=<?php echo h($row['id']) ?>">
          <?php echo h($row['title']);
          echo "<br>";?></a>
        <p><?php echo h($row['body']);
        echo "<br>";?>
        参考URL : <?php echo h($row['abouturl']);
        echo "<br>";
        ?>
        投稿者：<?php echo h($row['username']); ?>
        </p>
        </div>
        <div class="pic">
          <img src="<?php echo h($row['pic']);?>" alt="" style="width:500px;">
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
    <h1>始めよう！</h1>
    <button type="button" class="first-button">
      <a href="signup.php">ユーザー登録する</a><br>
    </button>

    <br>
    <br>
    <?php
      }
    ?>
  </section>

  <?php
  require('footer.php');
  ?>
