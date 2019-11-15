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

// 画面表示用データ取得
//================================
// レビューIDのGETパラメータを取得
$r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';
//DBからコメント情報を取得
$r_comment = getComment($r_id);
arsort($r_comment);
debug("コメント情報");
//debug($r_comment);


// DBからレビューデータを取得
$viewData = getReviewOne($r_id);
date_default_timezone_set('Asia/Tokyo');
$nowdate = date("Y-m-d H:i:s",strtotime("-3 day"));//strtotimeでdatetimeの方を調べる

if(empty($viewData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
$post_user = getUser($viewData['user_id']) ;
//debug('取得したユーザー情報：'.print_r($post_user,true));
//debug('取得したDBデータdetail：'.print_r($viewData,true));
//debug('取得したコメントデータdetail：'.print_r($r_comment,true));
//debug('ログイン中のユーザーID：'.print_r($_SESSION['user_id'],true));

//post送信されていた場合
if(!empty($_POST['comment'])){
  require('auth.php');

  //例外処理
  try{
    //dbへ接続
    $dbh = dbConnect();
    //sql文作成
    $sql = 'INSERT INTO comment(comment, comment_user, review_id, create_date) VALUES (:comment, :c_user, :r_id, :date)';
    $data = array(':comment' => $_POST['comment'], ':c_user' => $viewData['user_id'], ':r_id' => $r_id, ':date' => date('Y-m-d H:i:s'));
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $_SESSION['msg_success'] = SUC05;
      unset($_POST);
      header('location: reviewdetail.php?r_id='.$r_id);
      exit();//←忘れずに！
    }

  }catch (Exception $e) {
    error_log('エラー発生');
    $err_msg['common'] = MSG08;
  }
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<!--画面表示--------------------------------------------->

<?php
$siteTitle = '投稿詳細ページ';
require('head.php');
?>


<body>
  <?php
  require('header.php');
  ?>
<p id="js-show-msg" style="display:none;" class="msg-slide">
  <?php echo getSessionFlash('msg_success'); ?>
</p>

<section class="detail-wrapper">
    <h1><?php echo ($viewData['title']); ?></h1>

  <div class="detail-info-wrapper">

    <div class="detail-pic">
      <img src="<?php echo (!empty($viewData['pic'])) ? $viewData['pic'] : "images/sample.png"; ?>" alt="ゲーム画像">
      <?php if ($viewData['create_date'] > $nowdate){  ?>
        <div class="top-posts__new">
          NEW!
        </div>
      <?php } ?>
    </div>


    <div class="detail-other">
      <div class="detail-body">
        <p><span class="u-strong">本文</span><br></p>
        <p><?php echo nl2br(h($viewData['body'])); ?></p>
      </div>

      <p><span class="u-strong">投稿者：</span><?php echo ($post_user['username']); ?><br>
        <span class="u-strong">カテゴリー：</span><?php echo ($viewData['category']); ?><br>
        <span class="u-strong">お気に入り：</span><i class="fa fa-heart icn-like js-click-fav <?php if(isFav($_SESSION['user_id'],$viewData['id'])){echo
        'active';} ?>" aria-hidden="true" data-review_id="<?php echo ($viewData['id']); ?>" ></i><br>
        <span class="u-strong">参考URL：</span><a href="<?php echo h($viewData['abouturl']); ?>"><?php echo h($viewData['abouturl']); ?></a>
      </p>

      <!--もしセッションにユーザーIDがあり、かつそのユーザーIDとセッションIDが同じ場合編集できるようにする-->
      <?php if(!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $post_user['id']) : ?>
        <button><a href="reviewpost.php?r_id=<?php echo ($viewData['id']); ?>">記事を編集する</a></button>
      <?php endif;?>
    </div>
  </div>
  <div class="detail-commnet">
    <?php
      if(!empty($r_comment)){
       foreach ($r_comment as $val):
      $u_id = $val['comment_user'];
      $u_name = getUser($u_id);
    ?>
    <p>
      <a href="#">ユーザー名：<?php print_r($u_name['username']); ?></a><br>
      <?php echo ($val['comment']); ?><br>
      <span style="font-size:13px;">
      <?php echo ($val['create_date']); ?>posted.
      </span>
    <p>

    <?php
     endforeach;}
    ?>

    <?php
      if(!empty($_SESSION['user_id'])){
    ?>

    <form action="" method="post">
      <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
        <h2>コメント投稿</h2><br>
        <input type="text" name="comment" class="textbox" >
        </label>
      <input type="submit" placeholder="コメント" class="btn btn-primary" value="コメントする">
    </form>

    <?php
      }else{
    ?>

    <h4>ログインするとコメント投稿が可能です。</h4>

    <?php
      }
    ?>

  </div>
</section>

  <?php
    require('footer.php');
  ?>
