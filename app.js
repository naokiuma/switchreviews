$(function($){
  $('.js-select-category').change(function(e){
  //$('.js-select-category').on('change',function(e){ でもいけるよ！

    var $that = $(this);
    //var target = $('.js-select-category option:selected').val();
    //console.log(target, true);
    console.log("検索開始！");
    console.log($(this).val(), true);
    $.ajax({
      type: 'post',
      url: 'ajax.php',
      dataType: 'json',
      data: {
        "id": $(this).val()
      }
    }).then(function(data){
    if(data){
      //console.log(data);
      //console.log(data.count);
      $('.js-category-success').text(data.count);
      $('.js-category-success').addClass('js-category-success-effect');
    }
    });
  });
});
