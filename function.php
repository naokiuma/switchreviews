<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');


require('db.php');

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ:'.$str);
  }
}


//================================
// セッション準備・セッション有効期限を延ばす
//================================

//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();



//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//改行
function h($key){
  return htmlspecialchars($key, ENT_QUOTES,"UTF-8");
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定

define('MSG01','入力必須です。');
define('MSG02','パスワードが一致してないです、確認して。');
define('MSG03','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG04', 'そのEmailは既に登録されています');
define('MSG05', 'パスワードが異なります。');
define('MSG06', '6文字以上で入力してください');
define('MSG07', '1000文字以内で入力してください');
define('MSG08', '何かしら正しくありません');
define('MSG09', 'ファイルサイズが大きすぎます。');
define('MSG10', 'メールアドレス形式ではありません。');
define('MSG11', 'そのユーザーは存在しません。');

define('SUC01','ログインに成功しました。');
define('SUC02','登録しました！');
define('SUC03','プロフィールを更新しました。');
define('SUC04','投稿しました！');
define('SUC05','コメントを投稿しました！');

//グローバル定数エラーメッセージを格納する用。
$err_msg = array();




//バリデーション色々。
//存在性チェック
function validRequired($str,$key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//最小文字数チェック
function validMinLen($str,$key,$min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//最大文字数チェック
function validMaxLen($str, $key, $max){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG07;
  }
}

//パスワード再入力に間違いはないか
function validpassMatch($str1,$str2,$key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//セレクトボックスチェック
/*
function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG08;
  }
}
*/

//メルアド形式かどうかチェック
function validEmailcheck($email,$key){
  global $err_msg;
  if(!filter_var( $email,FILTER_VALIDATE_EMAIL)){
    $err_msg[$key] = MSG10;
  }
}



//メルアドの重複確認
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try{

    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG04;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG03;
  }
}

function queryPost($dbh, $sql, $data){
  $stmt = $dbh->prepare($sql);
  if(!$stmt->execute($data)){
    debug('クエリ大失敗！！');
    debug('失敗したSQL:'.print_r($stmt,true));
    debug('SQLエラー'.print_r($stmt->errorInfo(),true));
    $err_msg['common'] = MSG03;
    return 0;
  }
  debug('クエリ成功');
  return $stmt;
}


//セッションを一回だけ取得する
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

//================================
// ログイン認証
//================================
function isLogin(){
  //ログインしている場合
  if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');

    //現在日時が最終ログイン日時プラス有効期限を超えていた場合
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです。');

      //セッションを削除しログアウトする
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限以内です。');
      return true;
  }
  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}

//================================
// お気に入り情報がある確認
//================================

function isFav($u_id,$r_id){
  debug('お気に入り情報があるか確認します。');
  debug('ユーザーID：'.$u_id);
  debug('レビューID：'.$r_id);
  //例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM fav WHERE review_id = :r_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, 'r_id' => $r_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('お気に入りです');
      return true;
    }else{
      debug('特に気に入ってません');
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//全てのレビューデータを取得する（ログイン有無に関わらない。トップページ用）
function getReviewsTop(){
  debug('ログイン有無に関わらず、top用のpostを検索します');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users AS u RIGHT JOIN reviews AS r ON u.id = r.user_id ORDER BY r.id desc LIMIT 6';
    $stmt = $dbh->query($sql);
    $result_r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //debug('クエリ結果情報：'.print_r($result_r,true));
    return $result_r;
    $dbh = null;
  } catch(Exceptopn $e){
    echo "エラー発生:" .htmlspecialchars($e->getmessage(),
    ENT_QUOTES,'UTF-8)') ."<br>";
    die();
  }
}


//3日以内の記事のみ取得
function getReviewsNew(){
  debug('投稿から3日以内の記事を検索');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM reviews Where DATE_ADD(create_date, INTERVAL 1 DAY)';
    //$sql = 'SELECT * FROM reviews WHERE delete_flg = 0 LIMIT 6';
    debug('sqlの結果情報：'.print_r($sql,true));
    $stmt = $dbh->query($sql);
    $result_r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    debug('クエリ結果情報：'.print_r($result_r,true));
    return $result_r;
    $dbh = null;
  } catch(Exceptopn $e){
    echo "エラー発生:" .htmlspecialchars($e->getmessage(),
    ENT_QUOTES,'UTF-8)') ."<br>";
    die();
  }
}




//DBからユーザーデータを取得
function getUser($u_id){
  debug('ユーザー情報を取得します。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if($stmt){
      debug('クエリ成功。');
      //debug($stmt,true);
    }else{
      debug('クエリに失敗しました。');
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
  // クエリ結果のデータを返却
  return $stmt->fetch(PDO::FETCH_ASSOC);
}


//レビューデータを取得
function getReview($u_id, $r_id){
  debug:('レビューデータを取得します。');
  debug('ユーザーid:'.$u_id);
  debug('レビューID:' .$r_id);
  //例外処理
  try{
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM reviews WHERE user_id = :u_id AND id = :r_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':r_id' => $r_id);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
    //クエリ結果のデータを1レコード返却
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }else{
    return false;
  }

} catch (Exception $e){
  error_log('エラー発生:'. $e->getMessage());
}
}

//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
  // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
  if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
  }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
  }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現ページが1の場合は左に何も出さない。右に５個出す。
  }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２個出す。
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}


//レビューデータの検索結果を取得
//検索用の処理。
function searchReviews($s_key, $span = 20){
  global $category;
  debug('検索ワードに一致するデータを取得します。');

  try{
  $dbh = dbConnect();
  //タイトルか名前にヒットするか検索
  if(!empty($s_key)){
    $sql = "SELECT * FROM reviews WHERE (title LIKE '%$s_key%' OR gametitle LIKE '%$s_key%' OR body LIKE '%$s_key%') ";
    if(!empty($category)) $sql .= ' AND category_id = ' .$category;
  }else{
    $sql = "SELECT * FROM reviews ";
    if(!empty($category)) $sql .= ' WHERE category_id = ' .$category;
  }

  if(!empty($category)) $sql .= ' AND category_id = ' .$category;
  $data = array();
  $stmt = queryPost($dbh, $sql, $data);
  debug('検索でのクエリ結果情報です!!：'.print_r($stmt,true));
  
  $result_s['total'] = $stmt->rowCount(); //総レコード数
  $result_s['total_page'] = ceil($result_s['total']/$span); //総ページ数
  $result_s['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  //debug('全体のクエリ結果情報：'.print_r($result_s,true));
  return $result_s;
  $dbh = null;

  if(!$stmt){
    return false;
  }

  } catch (Exception $e){
    error_log('エラー発生:'. $e->getMessage());
  }
}



//Twitterの検索ワードを作る。投稿記事最新からランダムで取り出す。
function searchcronWord(){
  debug('検索ワードを確定します。');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM reviews WHERE gametitle IS NOT NULL ORDER BY create_date DESC LIMIT 0, 3;';
    $stmt = $dbh->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $key = array_rand($result,1);//リザルトから一つ、キーをランダムで取得。
    $search_word = $result[$key];//結果のうち、1件を取得。
    //debug($stmt);
    //debug(print_r($result));//print_rなら配列を出せる
    //debug(print_r($search_word['gametitle']));
    //debug($search_word['gametitle']);
    $word = $search_word['gametitle'];
    return $word;
  }catch (Exception $e){
      error_log('エラー発生:' . $e->getMessage());
    }
}




//レビューデータの数値取得
function getReviewsList($currentMinNum = 1, $category, $span = 20){
  debug('レビュー情報一覧を取得します');
  //例外処理。まずはページ件数を取得する
  try{
    $dbh = dbConnect();
    $sql = 'SELECT id FROM reviews' ;
    if(!empty($category)) $sql .= ' WHERE category_id = ' .$category;
    //    $sql = 'SELECT * FROM users AS u RIGHT JOIN reviews AS r ON u.id = r.user_id ORDER BY r.id desc LIMIT 6';見本
    $sql .= ' ORDER BY id desc';

    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $result_s['total'] = $stmt->rowCount(); //総レコード数
    $result_s['total_page'] = ceil($result_s['total']/$span); //総ページ数
    if(!$stmt){
      return false;
    }
    //ページング用のSQL文作成
    $sql = 'SELECT * FROM reviews';
    if(!empty($category)) $sql .= 'WHERE category_id = '.$category;
    $sql .= ' ORDER BY id desc LIMIT '.$span.' OFFSET '.$currentMinNum;
    //SELECT * FROM reviews ORDER BY id desc LIMIT 20 OFFSET 0 phpmyadminで行けたやつ

    $data = array(); //本来はここでプリペアードしている必要あり
    debug('SQL文章:'.$sql);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      //クエリ結果のデータを全レコードを格納
      $result_s['data'] = $stmt->fetchAll();
      debug('$resultの中身'.print_r($result_s,true));
      return $result_s;
    }else{
      return false;
    }
  } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
  }
}

//自分の投稿一覧
function getMyreviews($u_id){
  debug('自分の投稿一覧を取得します。ユーザーID:'.$u_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //sql文作成
    $sql = 'SELECT * FROM reviews WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' . $e->getMessage());
  }
}

//自分のおきに入り一覧
function getMyfav($u_id){
  debug('自分のお気に入り情報を取得します。');
  debug('ユーザーID：'.$u_id);
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM fav AS f LEFT JOIN reviews AS r ON f.review_id = r.id WHERE f.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}


function getReviewOne($r_id){
  debug('レビュー情報を取得します。レビューID：'.$r_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //sql文作成
    $sql = 'SELECT r.id, r.title, r.gametitle,r.body, r.pic, r.user_id, r.create_date, r.update_date, r.abouturl, c.name AS category
            FROM reviews AS r LEFT JOIN category AS c ON r.category_id = c.id WHERE r.id = :r_id AND r.delete_flg = 0';
    $data = array(':r_id' => $r_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function getComment($r_id){
  debug('コメント情報を取得します');
  try{
    //dbへせつぞく
    $dbh = dbConnect();
    //sql文さくせう
    $sql = 'SELECT * FROM comment WHERE review_id = :r_id AND delete_flg = 0';
    $data = array('r_id' => $r_id);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      //クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生:' .$e->getMessage());
  }
}



function getCategory(){
  debug('カテゴリー情報を取得します');
  //例外処理
  try{
    //dbへせつぞく
    $dbh = dbConnect();
    //sql文さくせう
    $sql = 'SELECT * FROM category';
    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      //クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生:' .$e->getMessage());
  }
}



  function getFormData($str){
    global $dbFormData;
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
      //フォームのエラーがある場合
      if(!empty($err_msg[$str])){
        //POSTにデータがある場合
        if(isset($_POST[$str])){//フォームで数字や数値の0が入っている場合もあるので、issetを使うこと
          return $_POST[$str];
        }else{
          //ない場合（フォームにエラーがある＝POSTされてるハズなので、まずありえないが）はDBの情報を表示
          return $dbFormData[$str];
        }
      }else{
        //POSTにデータがあり、DBの情報と違う場合（このフォームも変更していてエラーはないが、他のフォームでひっかかっている状態）
        if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
          return $_POST[$str];
        }else{//そもそも変更していない
          return $dbFormData[$str];
        }
      }
    }else{
      if(isset($_POST[$str])){
        return $_POST[$str];
      }
    }
  }



  //---------------------------------------
  // 画像処理

  function showImg($path){
    if(empty($path)){
      return 'images/game_gamen.png';
    }else{
      return $path;
    }
  }

  function uploadImg($file, $key){
    debug('画像アップロード処理開始');
    debug('FILE情報：'.print_r($file,true));

    if (isset($file['error']) && is_int($file['error'])) {
      try {
        // バリデーション
        // $file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
        //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
        switch ($file['error']) {
            case UPLOAD_ERR_OK: // OK
                break;
            case UPLOAD_ERR_NO_FILE:   // ファイル未選択の場合
                throw new RuntimeException('ファイルが選択されていません');
            case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズが超過した場合
            case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過した場合
                throw new RuntimeException('ファイルサイズが大きすぎます');


            default: // その他の場合
                throw new RuntimeException('その他のエラーが発生しました');
        }

        // $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
        // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
        $type = @exif_imagetype($file['tmp_name']);
        if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
            throw new RuntimeException('画像形式が未対応です');
        }

        // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
        // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
        // DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
        // image_type_to_extension関数はファイルの拡張子を取得するもの
        $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
        if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
            throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }
        // 保存したファイルパスのパーミッション（権限）を変更する
        chmod($path, 0644);

        debug('ファイルは正常にアップロードされました');
        debug('ファイルパス：'.$path);
        return $path;

      } catch (RuntimeException $e) {

        debug($e->getMessage());
        global $err_msg;
        $err_msg[$key] = $e->getMessage();


      }
    }
  }
