console.log("ajax_titleを読み込みます。");
var stack = [];
$(".ajax-keywords").keyup(function(){
    if($(this).val()){
    //let val = $(this).val();
    //console.log(val);
    
    $.ajax({
        type:'post',
        dataType:'json',
        url:'./ajax_keywords.php',
        //ajaxでキーワード検索
        data:{
            "keywords":$(this).val()
        } 

    }).then(function(ajax){
        if(ajax){
            console.log($(".search-suggest__active").text());

                 //ここに成功時の処理
                let suggestList = '';
                $('.search-suggest').addClass('search-suggest__active');
                    for(let i = 0; i < ajax.length; i++) {
                        //console.log("suggestListです");
                        //console.log(suggestList);
                        //console.log("ajax[i]です");
                        //console.log(ajax[i]);
                    if($(".search-suggest__active").text().indexOf(ajax[i]) < 0 ){
                        suggestList += '<p>'+ ajax[i] + '</p>';
                    }
  
                }
                $('.search-suggest__active').append(suggestList);

                //document.getElementsByClassName('.search-suggest').innerHTML = suggestList;

        }
    })

    }else{
        $('.search-suggest').removeClass('search-suggest__active');
        $('.search-suggest').empty();

    }


});
