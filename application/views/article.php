<!DOCTYPE html>
<html>
<head>
    <base href="<?= $basepath; ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>慧远故里育英助学协会</title>
    <link rel="stylesheet" href="/static/css/weui.css">
    <link href="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.min.css" rel="stylesheet">
    <script src="/static/js/index.min.js"></script>
    <style>

        body {
            font-family: Arial;
        }

        .warper {

        }

        .top {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
            background-color: #df2413;
            padding: .27rem 0 .27rem 0;
        }

        .top .nickname {
            margin-left: .27rem;
            font-size: .43rem;
            color: #fff;
        }

        .top .tleft {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            padding-left: .4rem;
        }

        .top .tcenter {
            flex: 1;
            padding-left: .8rem;
        }

        .top .tcenter h3 {
            color: #fff;
            font-size: .43rem;
        }

        [v-cloak] {
            display: none;
        }

        .divider {
            padding-top: .13rem;
            padding-bottom: .13rem;
            width: 100%;
        }

        .center {
            padding: .4rem .27rem 0 .27rem;

        }

        .error--text {
            color: #ff5252 !important;
            caret-color: #ff5252 !important;
        }

        .m2 {
            margin-top: 15px !important;
        }
        .weui-toast{

            width: 3rem;
            min-height: 3rem;
            margin-left:-1.5rem;

        }
        .weui-toast__content{
            font-size: 0.37rem;
        }


    </style>
</head>
<body>
<div id="app">

    <v-app id="inspire">


                <div class="warper">
                    <div class="top" v-cloak>

                        <div class="tleft">
                            <v-avatar
                                size="0.93rem"
                                color="grey lighten-4"
                            >
                                <img :src="headimgurl" alt="avatar">
                            </v-avatar>
                        </div>
                        <div class="tcenter">
                            <h3>慧远故里育英助学美文-征集通道</h3>
                        </div>
                    </div>
                    <div class="center">

                        <v-form v-model="valid" ref="form">
                            <v-text-field
                                v-model="formValue.title"
                                :rules="titleRules"
                                :counter="255"
                                label="标题"
                                required
                            ></v-text-field>
                            <v-text-field
                                v-model="formValue.author"
                                :rules="authorRules"
                                label="作者"
                                required
                            ></v-text-field>
                            <v-textarea
                                class="m2"
                                outline
                                name="input-7-4"
                                label="文章正文"
                                height="400"
                                :rules="articleRules"
                                v-model="formValue.article"
                            ></v-textarea>
                        </v-form>
                        <v-fab-transition @click="submitData">
                            <v-btn
                                fixed
                                dark
                                fab
                                bottom
                                right
                                color="#df2413"
                                @click="submitData"

                            >
                                <span v-cloak>提交</span>
                            </v-btn>
                        </v-fab-transition>
                    </div>
                    <div class="bottom"></div>
                </div>




    </v-app>

    </div>

<script src="/static/js/zepto.min.js"></script>
<script src="/static/js/weui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
<script>

    var loading;

    new Vue(
        {
            el: '#app',
            data(){
                return {
                    dialog: false,
                    headimgurl: '<?= $headimgurl?>',
                    nickname: '<?= $nickname?>',
                    valid: true,
                    formValue: {
                        title: '',
                        author: '',
                        article: '',
                    },
                    titleRules: [
                        v => !!v || '文章标题不能为空'
                    ],
                    authorRules: [
                        v => !!v || '文章作者不能为空'
                    ],
                    articleRules: [
                        v => !!v || '文章内容不能为空'
                    ]
                }
            },
            methods: {
                submitData() {

                    let _this = this;

                    if (this.$refs.form.validate()) {
                        // Native form submission is not yet supported

                        $.ajax({
                            url: '<?= $basepath . 'article/save'?>',
                            data: _this.formValue,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend:function (xhr, settings) {

                                loading = weui.loading('提交中...');

                            },
                            success: function (res) {

                                loading.hide();

                                if (res.status == 'ok') {

                                    weui.toast(res.message, 3000);

                                    _this.$refs.form.reset()

                                } else {
                                    weui.alert(res.message);
                                }

                            },
                            error: function (res) {
                                weui.alert('系统错误,请重试!');
                            }
                        })

                    }


                }
            }


        }
    )
</script>
</body>
</html>
