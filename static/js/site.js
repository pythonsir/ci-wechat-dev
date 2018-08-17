
var mySwiper = new Swiper ('.swiper-container', {
    loop: true,
    autoplay:true,
    // 如果需要分页器
    pagination: {
        el: '.swiper-pagination',
    }
})

// 失去焦点时检测
weui.form.checkIfBlur('#form');

$('#formSubmitBtn').on('click',function (e) {

    var loading;

     weui.form.validate('#form', function (error) {
             if (!error) {


              weui.confirm('因慧远故里育英助学协会的银行开户信息变更办理中，为安全起见，决定临时借用为其提供互联网技术支撑服务的山西省耐特斯达科技有限公司进行电子收款，请您理解，谢谢！', {
                          title: '重要声明!',
                  buttons: [{
                          label: '不同意',
                      type: 'default',
                      onClick: function(){ console.log('no') }
                  }, {
                          label: '同意',
                          type: 'primary',
                          onClick: function(){


                              $.ajax({
                                  url:'/index.php/welcome/makeOrder',
                                  type:'POST',
                                  data:{"username":$('input[name="username"]').val(),"phone":$('input[name="phone"]').val(),"money":$('input[name="money"]').val()},
                                  dataType:'json',
                                  beforeSend:function (xhr, settings) {

                                      loading = weui.loading('提交中...');

                                  },
                                  success:function (data) {

                                      loading.hide();

                                      if(data['status'] == 'ok'){

                                          const param = data['param'];

                                          if (typeof WeixinJSBridge == "undefined"){
                                              if( document.addEventListener ){
                                                  document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                                              }else if (document.attachEvent){
                                                  document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                                                  document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                                              }
                                          }else{
                                              jsApiCall(param);
                                          }

                                      }else if(data['status'] == 'no'){

                                          weui.alert('系统错误!');
                                      }


                                  },
                                  error:function () {
                                      weui.alert('系统异常!');
                                  }

                              })



                          }
                      }]
              });






             }


         });

    
})


//调用微信JS api 支付
function jsApiCall(param)
{

    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        JSON.parse(param),
    function(res){

            if(res.err_msg == "get_brand_wcpay_request:ok"){

                window.location.href="http://hygl.zgftlm.com/index.php/me/index";

            }


    }
);
}

function callpay()
{
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall();
    }
}
