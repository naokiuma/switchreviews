console.log("switch.jsを読み込みます");

$(function(){
            // Ajax button click
            $('#twitter_switch').on('click',function(){
              $.ajax({
                  url:'./twitter_switch.php',
                  type:'POST',
                  dataType: 'json', //必須。json形式で返すように設定
              })
              // Ajaxリクエストが成功した時発動
              .done( (data) => {
                  console.log(data);
                  //return data;

              })
              // Ajaxリクエストが失敗した時発動
              .fail( (data) => {
                  console.log("失敗しました");
              })
          });
      });


/*
axios.get(url).then(function(response){
  self.coins = response.data;
  });
*/
