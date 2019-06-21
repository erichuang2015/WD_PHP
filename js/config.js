/**
 * js config 
 * by MTsung 2018
 * 
 */

var _jsPath = $('#config').data('path') ? $('#config').data('path') : ""; 				//沒有則空值
var _jsSerbackPath = _jsPath+'/serback';
var _fbAppID = $('#config').data('fb_app_id') ? $('#config').data('fb_app_id') : "";
var _googleClientID = $('#config').data('google_client_id') ? $('#config').data('google_client_id') : "";
var _lang = $('#config').data('lang');

if(typeof($('#config').data('fb_app_id'))=="undefined"){
    $.ajax({
        url: _jsPath+"/ajax.php",
        type: "GET",
        data: {
            appKey: 'fbAuthAppID'
        },
        dataType:'text',
        async: false,
        success: function(msg){
            _fbAppID = msg;
        }
    });
}

if(typeof($('#config').data('google_client_id'))=="undefined"){
    $.ajax({
        url: _jsPath+"/ajax.php",
        type: "GET",
        data: {
            appKey: 'googleAuthAppID'
        },
        dataType:'text',
        async: false,
        success: function(msg){
            _googleClientID = msg;
        }
    });
}


// 取得語言包
$.ajax({
    url: _jsPath+"/ajax.php",
    type: "GET",
    data: {
        getLanguageMsg: 1,
        language: _lang
    },
    async: true,
    success: function(msg){
        if(msg){
            _jsMsg = msg;
            if(typeof(msg)!="object"){
                _jsMsg = JSON.parse(msg);
            }
        }
    }
});


// 備份
$.ajax({
    url: _jsPath+"/__backup__",
    timeout: 1,//直接讓他timeout
    success: function(msg){}
});

setTimeout(function(){
    if(location.host.indexOf("localhost") != 0 && location.host.indexOf("127.0.0.1") != 0){
        console.clear();
        console.log('%c 請勿在此複製/貼上任何東西，以免遭惡意攻擊。','color:#ff0000;font-size:30px;');
        console.log("%c ", "padding:1px 200px;line-height:60px;background:url('https://www.104portal.com.tw/images/logo.png') no-repeat;");
    }

}, 500);