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
  <h2>Tweet About Switch</h2>
  <p>「#Nintendoswitch」とランダムで最近投稿されたゲームに関するツイートを取得しました。<br>
  ツイッターを通してスイッチに関してツイートしているユーザーを探し、交流しましょう！</p>
  <button id="twitter_switch" type="button" name="button">スイッチ関連のハッシュタグを取得</button>


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
