<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

?>

<?php
$siteTitle = 'ニュース';
require('head.php');
?>

<!-- ヘッダー -->
<?php
  require('header.php');
?>

<section class="news__container" id="news">
  <div>
    <h2>NEWS一覧</h2>
  </div>

  <div class="news__post">
    <time datetime="2019-12-09">2019/12/09</time>
    <h3 class="news__title"><span>キーワード検索時にタイトル候補を表示</span></h3>
    <p class="new__contens">
    <a href="searchreviews.php">投稿検索</a>のページでキーワード検索をする時、<br>
    候補のゲームタイトルをリアルタイムで表示させ、検索をしやすくしました。
    </p>
    <img src="images/news1209.png" style="width:100%;" alt="リアルタイム検索">
  </div>

  <div class="news__post">
    <time datetime="2019-11-22">2019/11/22</time>
    <h3 class="news__title"><span>サイトを公開しました。</span></h3>
    <p class="new__contens">
      Nintendo Switch indiews Lifeを公開しました。<br>
      ポートフォリオサイトとしての公開ですが、せっかくなのでいろんなゲームの投稿メモとしても使っていこうと思います。<br>
      是非ともお気軽に投稿やコメントしてください！
    </p>
  </div>

</section>



<?php
  require('footer.php');
?>
