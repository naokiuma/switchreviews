<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　投稿登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログインしてなければリダイレクト
if(empty($_SESSION['login_date'])){
  $_SESSION['msg_success'] = "ログインしてください";
  header("Location:login.php"); //ログインページへ
}

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
//getデータを取得する
$r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';
//dbからデータを取得、存在している場合のみdbformdata

$dbFormData = (!empty($r_id)) ? getReview($_SESSION['user_id'], $r_id) : '';
//新規登録か、編集か判別フラグ。dbデータが場合
$edit_flg = (empty($dbFormData)) ? false : true;
debug('editflg:'.$edit_flg);
//DBからカテゴリーを取得
$dbCategoryData = getCategory();
debug('商品id:'.$r_id);
debug('フォーム用DBデータ:'.print_r($dbFormData,true));
debug('カテゴリデータ:'.print_r($dbCategoryData,true));

//パラメータ改ざんチェック
//getパラメータはあるがurlをいじくった場合、正しい商品データが取れないので、トップへ遷移
if(!empty($r_id) && empty($dbFormData)){
  debug('GETパラメータのレビューidが違います。マイページへ遷移します');
  header("Location:index.php");//トップぺ
}



//post送信時の処理
//================================
//ポストがある場合
if(!empty($_POST)){
  debug('post送信があります');
  debug('post情報:'.print_r($_POST,true));
  debug('file情報:'.print_r($_FILES,true));

  //変数にユーザー情報を代入する
  $title = $_POST['title'];
  $gametitle = $_POST['gametitle'];
  $category = $_POST['category_id'];
  $body = $_POST['body'];
  $abouturl = $_POST['abouturl'];
  $pic = (!empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic = (empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    //未入力チェック
    validRequired($title, 'title');
    validMaxLen($body, 'body', 1000);
  }else{
    if($dbFormData['title'] !== $title){
    validRequired($title, 'title');
    }
    if($dbFormData['category_id'] !== $category){
      //セレクトボックスチェッウ
      validSelect($category,'category_id');
    }
    if($dbFormData['body'] !== $body){
      //最大文字数チェック
      validMaxLen($body, 'body', 1000);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションokです');

    //例外処理
    try{
      //dbへ接続
      $dbh = dbConnect();
      //sql文作成
      //編集画面の場合はupdate、新規登録画面の場合はinsertする。
      if($edit_flg){
        debug('レビュー更新します。');
        $sql = 'UPDATE reviews SET title = :title, gametitle = :gametitle,category_id = :category, body = :body, pic = :pic, abouturl = :abouturl WHERE user_id = :u_id AND id = :r_id';
        $data = array(':title' => $title, ':gametitle' => $gametitle, ':category' => $category, ':body' => $body, ':pic' => $pic, ':abouturl' => $abouturl, ':u_id' => $_SESSION['user_id'], ':r_id' => $r_id);
      }else{
        debug('新規登録です。');
        $sql = 'INSERT INTO reviews (title, gametitle,category_id, body, pic, abouturl, user_id, create_date) values (:title, :gametitle,:category, :body, :pic, :abouturl, :u_id, :date)';
        $data =array(':title' => $title, ':gametitle' => $gametitle, ':category' => $category, ':body' => $body, ':pic' => $pic, ':abouturl' => $abouturl, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('sqlの中身:'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      //クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('記事一覧へ遷移します。');
        header("Location:searchreviews.php"); //マイページへ
      }

    } catch (Exception $e){
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG03;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
  $siteTitle = (!$edit_flg) ? 'レビュー投稿' : 'レビュー編集';
  require('head.php');
?>

  <body class="page-review page-2colum">

    <!-- メニュー -->
    <?php
    require('header.php');
    ?>

      <!--main-->
      <section id ="main">
        <div id="form-container">
          <form action="" method="post" id="form-main" enctype="multipart/form-data" style="box-sizing:border-box;">
            <h2 class="title">ゲームのレビュー/メモを投稿する</h2>
            プレイ済みゲームおすすめレビュー、いまいちレビュー、
            購入前製品の登録、参考にしたいURLなんかも登録できます。
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>

            <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
              <h3>投稿タイトル</h3>
              <input type="text" class="textbox" name="title" value="<?php echo getFormData('title'); ?>">
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['title'])) echo $err_msg['title'];
              ?>
            </div>

            <label>
              <h3>ゲームタイトル</h3>
              <input type="text" class="textbox" name="gametitle" value="<?php echo getFormData('gametitle'); ?>">
            </label>


            <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
              <h3>カテゴリ</h3>
              <select name="category_id" id="">
                <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
                <?php
                foreach ($dbCategoryData as $key => $val){
                ?>
                <option value ="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; }?> >
                   <?php echo $val['name']; ?><!--ここにカテゴリテーブルのnameの物が入っている。-->
                </option>
                <?php
                  }
                ?>
              </select><br>
              <span class="area-msg">※必須</span>
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
                ?>
            </div>


            <label class="<?php if(!empty($err_msg['body'])) echo 'err'; ?>">
              <h3>レビュー/メモ<br></h3>
              <textarea name="body" id="js-count" class="post_main_text"><?php echo getFormData("body"); ?></textarea>
              <p><span id="js-count-view">0</span>/1000文字まで</p>
            </label>
            <div class="area-msg">
              <?php
              if(!empty($err_msg['body'])) echo $err_msg['body'];
              ?>
            </div>



            <label>
              <h3>参考URL</h3>
              <input type="url"  name="abouturl" class="textbox" name="title" value="<?php echo getFormData('abouturl'); ?>"><br>
              <span>※トレーラーやプレイ動画などのURLを入力します。</span>
            </label>
            <h3>ゲーム画像</h3>
            <span>※PCではドラッグ＆ドロップ可。未設定の場合、サンプル画像が設定されます。<br>※※約10MBまで</span>
            <div>
              <div class="imgdrop-container">
                <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
                  <input type="hidden" name="MAX_FILE_SIZE" value="9145728"><!--元は3145728-->
                  <input type="file" name="pic" class="input-file" >
                  <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style= "<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                </label>
                <div class="area-msg">
                  <?php
                  if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                  ?>
                </div>
              </div>
            </div>

            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>">
            </div>
          </form>
        </div>
      </section>





          </form>

        </div>

      </section>


      <!-- footer -->
      <?php
      require('footer.php');
      ?>


    </div>

  </body>
