<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//カレントページ
$currentPageNum = (!empty ($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは1

//表示件数
$listSpan = 20;
//現在の表示レコードの先頭を算出。
//1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
$currentMinNum = (($currentPageNum-1)*$listSpan);
//DBからレビューデータを取得。検索していたら検索の単語。


$category = (!empty($_POST['c_id'])) ? $_POST['c_id'] : '';
$search_word = (!empty($_POST['s_key'])) ? $_POST['s_key']: '';
$dbReviewsData = (!empty($_POST)) ? searchReviews($search_word) : getReviewsList($currentMinNum, $category);
//arsort($dbReviewsData);

$dbCategoryData = getCategory();
//debug('カテゴリデータを取る'.print_r($dbCategoryData,true));


if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header("Location:index.php"); //トップページへ
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '投稿一覧';
require('head.php');
?>

<!-- ヘッダー -->
<?php
  require('header.php');
?>
<!--main-->


<section class="search-main">
  <div class="search-title">
    <div class="search-left">
      <label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
      <span class="search-word" style="<?php if(empty($search_word)) echo 'display:none';?>">【 <?php echo $search_word; ?> 】の検索結果<br></span>
      <span class="total-num"><?php echo ($dbReviewsData['total']); ?></span>件の投稿が見つかりました
    </div>
    <div class="search-right">
      <span class="num"><?php echo (!empty($dbReviewsData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbReviewsData['data']); ?></span>件 / <span class="num"><?php echo ($dbReviewsData['total']); ?></span>件中
    </div>
  </div>
  <div class="panel-list">
   <?php
      foreach($dbReviewsData['data'] as $key => $val):
    ?>
        <div class="panel-head">
          <a href="reviewdetail.php?r_id=<?php echo htmlspecialchars($val['id']) ?>" class="panel_a"><?php echo ($val['title']); ?></a>
          <p class="panel_p"><?php echo nl2br($val['body']); ?></p>
          <img class= "panel-head-img" src="<?php echo ($val['pic']); ?>" >
        </div>

      </a>
    <?php
      endforeach;
    ?>
  </div>
  <?php pagination($currentPageNum, $dbReviewsData['total_page']); ?>

<div class="cover"></div>

<!--フッターの検索バーエリア-->
<button type="button" class="search-button" name="button">検索する</button>
    <div class="search-container">
        <form id="search-form-main" action="" method="post">
          <h2 class="title">投稿を検索する</h2>
          <label class="search-word">
            <select name="c_id" class="js-select-category">
              <option value="">カテゴリ指定</option>
              <?php
              foreach ($dbCategoryData as $key => $value) {
              ?>
              <option value="<?php echo $value['id'] ?>">
                <?php echo $value['name']; ?>
              </option>
              <?php
              }
              ?>
            </select>
            <input type="text" name="s_key" class="textbox" placeholder="キーワードで検索" >
            <p style="margin-bottom:0px">このカテゴリには<span class ="js-category-success">0</span>件の投稿があります。</p>/
          </label>
            <br><input type="submit" value="Search!" style="font-size:30px; border-radius: 20px; border:solid 1px; font-size: 30px; box-shadow:4px 4px; margin-bottom:30px;">
        </form>

    </div>

</section>

  <?php
  require('footer.php'); //フッターの中でapp.jsを読み込んでいる。
  ?>
