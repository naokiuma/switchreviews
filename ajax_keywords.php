
<?php
//キーワード検索で候補を見つける
require('function.php');
$keywords = ($_POST['keywords']);

if($_POST){
    //debug($keywords);

    $dbh = dbConnect();
    $sql = "SELECT gametitle FROM reviews WHERE (gametitle LIKE '$keywords%') ";
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);
    //$return_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $return_array = $stmt->fetchAll(PDO::FETCH_COLUMN);
    debug('検索でのクエリ結果情報です!!：'.print_r($return_array,true));//ok！
    if($return_array){
        $result = json_encode($return_array,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        //debug($result);

        //print_r($return_array);
        echo($result);
        exit; //処理の終了

        
    }
    
}

