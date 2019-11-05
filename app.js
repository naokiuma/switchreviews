//カテゴリ検索時に行われる処理

$(function($){
  $('.js-select-category').change(function(e){

    var $that = $(this);
    //var target = $('.js-select-category option:selected').val();
    //console.log(target, true);
    console.log("検索開始！洗濯カテゴリのidです");
    console.log($(this).val(), true);
    $.ajax({
      type: 'post',
      url: 'ajax.php',
    //  dataType: 'json',
      data: {
        "id": $(this).val()
      }　
    }).then(function(data){
    if(data){
      console.log("ajax完了");
      console.log(data);
      //console.log(data.count);
      $('.js-category-success').text(data);
      $('.js-category-success').addClass('js-category-success-effect');
    }
    });
  });
});
/*
参考
$.ajax({
  type: "POST",
  url: "ajaxfav.php",
  data: { review_id : favreviewID}
}).done(function( data ){
  console.log('Ajax Success');
  */
