//console.log("switch.jsを読み込みます");

Vue.component('tweet-data',{
  data:function(){
    return{
      hashs:{},
      hash_flg:false,
      words:{},
      words_flg:true
    }
  },
  template:`
  <div>
    <button id="twitter_switch" v-on:click="gethash" type="button" name="button">Switchのハッシュタグ</button>
    <button id="twitter_switch" v-on:click="getwords" type="button" name="button">最近の投稿に関するツイート</button>

      <div class="tweets" v-if="hash_flg">
        <div class="each-tweet" v-for="item in hashs">
        <p><a target="_blank" :href="'https://twitter.com/' + item.screen_name">{{ item.screen_name }}</a></p>
        <img :src="item.profile_image_url" alt="">
        <p>Tweet：  {{ item.text }}</p>
        </div>
      </div>



      <div class="tweets" v-if="words_flg">
        <div class="each-tweet" v-for="item in words">
        <p><a target="_blank" :href="'https://twitter.com/' + item.screen_name">{{ item.screen_name }}</a></p>
        <img :src="item.profile_image_url" alt="">
        <p>Tweet：  {{ item.text }}</p>
        </li>
      </div>

  </div>
  `,
  mounted(){
    console.log("読み込みました");
  },
  methods:{
    gethash:function(){
      console.log("gethashのajax開始");
      this.hash_flg = true;
      this.words_flg = false;
      $.ajax({
          url:'./twitter_switch.php',
          type:'POST',
          dataType: 'json', //必須。json形式で返すように設定
      })
      // Ajaxリクエストが成功した時発動
      .done( (data) => {
          //console.log(data);
          this.hashs = data;
          console.log(this.hashs);
          //return data;
      })
      // Ajaxリクエストが失敗した時発動
      .fail( (data) => {
          console.log("失敗しました");
      })
    },
  getwords:function(){
    console.log("getwordsのajax開始");
    this.hash_flg = false;
    this.words_flg = true;
    $.ajax({
      url:'./twitter_new.php',
      type:'POST',
      dataType:'json',
    })
    //成功の場合
    .done((data) =>{
      //console.log(data);
      this.words = data;
      console.log(this.words);
    })
    //ajax失敗の場合
    .fail((data) =>{
      console.log("失敗しました");
    })
  }

  }

})

new Vue({
  el:'#twitter',
})
