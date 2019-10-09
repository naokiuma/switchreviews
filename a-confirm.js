$(function(){
$('.js-signup').on('submit',function(e){
  return false;
  //e.preventDefault();

  $.ajax({
    type: 'post',
    url: 'ajaxconfirm.php',
    dataType:'json',
    data: {
      name: $('#js-get-val-name').val(),
      id: $('#js-get-val-id').val()
    }
  }).done(function(data,status){
    console.log(data);
    console.log(status);
    $('#confirmArea').text(data.name);
  });
});

});
