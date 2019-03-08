/**
 * FB登入
 */
window.fbAsyncInit = function() {
    FB.init({
        appId: _fbAppID,
        xfbml: true,
        version: 'v3.0'
    });
};

(function(d, s, id) {
    var lang = "zh-TW";
    switch(_lang) {
        case "en":
            lang = "en_US";
            break;
        case "ja":
        case "jp":
            lang = "ja_JP";
            break;
        case "ko":
        case "kr":
            lang = "ko_KR";
            break;
        default:
            lang = _lang[0]+_lang[1]+"_"+(_lang[3]?_lang[3].toUpperCase():"")+(_lang[4]?_lang[4].toUpperCase():"")
    }
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/"+lang+"/sdk.js"; //FB的多國一定要語言_大寫區碼 zh-TW ja_JP
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


function statusChangeCallback(response) {
    FB.api('/me', { fields: 'id,name,email,birthday,picture' }, function(response) {

        $.ajax({
            url: _jsPath+"/ajax.php",
            type: "GET",
            data: {
                socialLogin: 'fb',
                id: response.id,
                name: response.name,
                email: response.email,
                picture: response.picture.data.url
            },
            dataType:'text',
            async: false,
            success: function(msg){
                if(msg){
                    if(window.location.href.indexOf("?") != -1){
                        window.location.href = window.location.href+"&socialLogin=fb";
                    }else{                            
                        window.location.href = window.location.href+"?socialLogin=fb";
                    }
                }
            }
        });
    });
}


function fbLogin() {
    FB.login(function(response) {
        if (response.status === 'connected') {
            statusChangeCallback(response);
        }
    }, { scope: 'email' });//,user_birthday,user_gender
}

function fbLogout() {
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected' || response.status === 'authorization_expired' || response.status === 'not_authorized') {
            FB.logout(function(response) {
                var url = '//www.facebook.com/logout.php?access_token=' + response.authResponse.accessToken;
                $('body').append('<iframe src="'+url+'" height="0" width="0" frameborder="0" scrolling="no" id="myFrame"></iframe>');
            });
        }
    });
}

/**
 * Line登入
 */
function lineLogin() {
    window.open(_jsPath+'/include/LineLogin/line_login.php'
                , 'Line'
                , config='height=550,width=500,left='+(window.screen.width-500)/2+',top='+(window.screen.height-550)/2);
}




/**
 * Google登入
 * <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
 * <script src="https://apis.google.com/js/api:client.js"></script>
 * <a id="GoogleBtn">login</a>
 */
var googleUser = {};
var startApp = function() {
    gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
            client_id: _googleClientID,
            cookiepolicy: 'single_host_origin',
        });
        attachSignin(document.getElementById('GoogleBtn'));
    });
};

function attachSignin(element) {
    // console.log(element.id);
    auth2.attachClickHandler(element, {},
        function(googleUser) {
            var profile = googleUser.getBasicProfile();
            $.ajax({
                url: _jsPath+"/ajax.php",
                type: "GET",
                data: {
                    socialLogin: 'google',
                    id: profile.getId(),
                    name: profile.getName(),
                    email: profile.getEmail(),
                    picture: profile.getImageUrl()
                },
                dataType:'text',
                async: false,
                success: function(msg){
                    if(msg){
                        if(window.location.href.indexOf("?") != -1){
                            window.location.href = window.location.href+"&socialLogin=google";
                        }else{                            
                            window.location.href = window.location.href+"?socialLogin=google";
                        }
                    }
                }
            });
        },
        function(error) {
            // alert(_jsMsg["GOOGLE_SIGNIN_ERROR"] + JSON.stringify(error, undefined, 2));
        });
}
$(function(){
    if(typeof($('#GoogleBtn').val())!="undefined"){
        startApp();
    }
});