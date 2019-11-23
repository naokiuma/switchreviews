<?php


require('function.php');
//require('twitter.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//ログイン認証
require('auth.php');

?>

<!--画面表示--------------------------------------------->

<?php $siteTitle = 'ツイート一覧'; require('head.php'); ?>


<body>
<?php require('header.php'); ?>

<section class="twitter__container">
  <h2>ツイートを見てみよう。</h2>
  <p>「#Nintendoswitch」のハッシュタグに関するツイート、<br>
      最近投稿されたゲームに関するツイートを取得しました。<br>
      気になるツイートを見つけたら、Twitterへリンクしフォローしてみよう。<br>
      <br>
      <span>※繰り返し取得すると、api制限がかかる場合があります。<br>
      取得できない場合は、時間をあけて試してください。</span>
  </p>

  <div class="twitter__posts">
    <div id="vue">
      <tweet-data></tweet-data>
    </div>
  </div>

</section>
<?php $search_word = searchcronWord();?>


<script src="js/twitter_switch.js"></script>


<?php
require('footer.php');
?>
