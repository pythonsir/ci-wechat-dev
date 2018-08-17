var loading;

var app;

$(function () {

     app = new Vue({
        el: '#list',
        data: {
            list:[],
        },
        created:function () {

            $.ajax({
                url:'/index.php/me/getdata',
                type:'POST',
                dataType:'json',
                beforeSend:function (xhr, settings) {

                    loading = weui.loading('加载中...');

                },
                success:function (data) {

                    loading.hide();

                    if(data['status'] == 'ok'){

                        app.list = data['res'];

                    }





                },
                error:function (xhr, type) {

                   console.log('xhr:'+xhr+" type:"+type)

                }


            })


        }
    })

})

