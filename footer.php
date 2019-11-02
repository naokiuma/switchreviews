<footer id="footer">
  Copyright <a href="#">スイッチインディーズライフ</a>. All Rights Reserved.
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script><!--jquery-cookie-->
<script src="js/jquery.bgswitcher.js"></script>
<script src="app.js"></script><!--ajaxの動き。-->

<script>

//ドロップダウンメニュー
$(function(){
  var menuclick = $('.js-toggle-sp-menu');
  $(menuclick).on("click",function(){
    console.log("動作");
    var target = $('.toggle_menu');
    $(target).slideToggle(600);
    //$(target).css('display','flex');
    //$(target).toggleClass("menu_active");
  })
})



  $(function(){
    //テキストカウントの処理。
  var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
      $countUp.on('keyup', function(e){
      $countView.html($(this).val().length);
    });


    //クリック時にヒョイっと動く検索。
  $('.search-button').on("click",function(){
    $('.search-container').slideToggle(300);
    $('.cover').toggleClass("js-cover");
  });

  //クリック時にヒョイっと動くお気に入り。
  $('.fav-button').on("click",function(){
    $('.fav-games').slideToggle();
  });

//どーんと出てくるテキストラッピングエリア。
if($('.about-text-wrap').length){

  $('.about-text-wrap').css("opacity","0");
  $(window).scroll(function(){
    var imgPos = $('.about-text-wrap').offset().top;
    var scroll = $(window).scrollTop();
    var windowHeight = $(window).height();
    if(scroll > imgPos - windowHeight + windowHeight/6){
      $('.about-text-wrap').css("opacity","1");
      $('.about-text-wrap').css("transition","all 0.5s");
      $('.about-text-wrap').css("transform","scale(1.1, 1.1)"); //どーんと出てくる。
    }else{
      $('.about-text-wrap').css("opacity","0");
    }
  });
}

//右から左
  $(window).scroll(function(){
    $('.disc').addClass('move');
    var imgPos = $('.disc').offset().top;
    var scroll = $(window).scrollTop();
    var windowHeight = $(window).height();
    if(scroll > imgPos - windowHeight + windowHeight/2){
      $('.disc').removeClass("move");
    }
  });



  //bgスライドショー
  $(".bg-box").bgSwitcher({
        images: ['images/billbord2.jpg','images/billbord3.jpg','images/billbord4.jpg','images/billbord5.jpg'], // 切り替える背景画像を指定
        interval: 3000, // 背景画像を切り替える間隔を指定 2000=2秒
    });





    // 非同期でお気に入り登録・削除
    var $fav,
    favreviewID;
    $fav = $('.js-click-fav') || null; //もしjs-clickがない場合はnull.nullというのはnull値という値で、「変数の中身は空ですよ」と明示するためにつかう値
    favreviewID = $fav.data('review_id') || null;
    // 数値の0はfalseと判定されてしまう。review_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
    if(favreviewID !==undefined && favreviewID !== null){
      $fav.on('click',function(){
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxfav.php",
          data: { review_id : favreviewID}
        }).done(function( data ){
          console.log('Ajax Success');
          // クラス属性をtoggleでつけ外しする
          $this.toggleClass('active');
        }).fail(function( msg ) {
          console.log('Ajax Error');
        });
      });
    }


  //フラッシュメッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 3000);
  }




  //画像プレビュー
  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover', function(e){
  e.stopPropagation();
  e.preventDefault();
  $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e){
  e.stopPropagation();
  e.preventDefault();
  $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e){
  $dropArea.css('border', 'none');
  var file = this.files[0],            // 2. files配列にファイルが入っています
  $img = $(this).siblings('.prev-img'), // 3. $(this)にすることでjQuery形式にし、jQueryのsiblingsメソッドで兄弟のimgを取得。（同列の中のprev-imgclassを取得。）また、domを入れる変数には、頭に$をつけるならわし。
  fileReader = new FileReader();   // 4. ファイルを読み込むFileReaderオブジェクト。newでやる

  // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
  fileReader.onload = function(event) {
  // 読み込んだデータをimgに設定
  $img.attr('src', event.target.result).show();
  };

  // 6. 画像読み込み
  fileReader.readAsDataURL(file);
});



});


</script>


</body>
</html>
