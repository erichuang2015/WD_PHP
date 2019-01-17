
function shoppingListReload(shoppingList) {
    loadingStart();
    $('#shoppingListDiv').html("");
    if(typeof(shoppingList) == "undefined"){
        var shoppingList;
        $.ajax({
            url: _jsPath+"/shopping",
            type: "GET",
            data: {
                ajax: "shoppingList"
            },
            dataType: 'text',
            success: function(msg) {
                try {
                    temp = JSON.parse(msg);
                    shoppingList = temp.data;
                    shoppingListReload(shoppingList);
                } catch (e) {
                    alert("error : " + e);
                }
                loadingStop();
            }
        });
    }else{
        if(typeof(shoppingList.length) == "undefined"){
            shoppingList.length = 0;
        }
        $("#shoppingCountSpan").html("購物車("+(shoppingList.length*1)+")");
        if(shoppingList.length>0){
            $(shoppingList).each(function(k,v){
                $('#shoppingListDiv').append($('#cartItemtDiv').html());
                var temp = $('#shoppingListDiv').find('div.cart-item:last');
                $(temp).find('.product-link').attr('href', 'products/detail/' + v.productId);
                $(temp).find('img').attr('src', v.picture);
                $(temp).find('.title').html(v.name);
                $(temp).find('.ng-scope').html('數量：' + v.count);
                $(temp).find('.price-details').html('NT ' + v.price * v.count);
                $(temp).find('.remove').data("productid",v.productId);
                $(temp).find('.remove').data("specifications",v.specifications);
                $(temp).find('.remove').on('click',function(){
                    shoppingRmProduct($(temp).find('.remove'),false);
                });
            });
            $('#shoppingListDiv').append('<div class="cart-chkt-btn"><button onclick="javascript:location.href=\'shopping\'"> 訂單結帳 </button></div>');
        }else{
            $('#shoppingListDiv').append('<div class="ng-hide"> 你的購物車是空的 </div>');
        }
    }

}
/**
 * 新增商品
 */
function shoppingAddProduct($obj) {
    loadingStart();
    $.ajax({
        url: _jsPath+"/shopping",
        type: "GET",
        data: {
            ajax: "addProduct",
            productid: $obj.data("productid"),
            specifications: $obj.data("specifications"),
            count: $obj.val(),
        },
        dataType: 'text',
        success: function(msg) {
            try {
                temp = JSON.parse(msg);
                if (!temp.response) {
                    alert(temp.message);
                    if(temp.data["toUrl"]){
                        location.href = temp.data["toUrl"];
                    }
                }
                shoppingListReload(temp.data);
                loadingStop();
                // window.location.reload();
                // 左側
            } catch (e) {
                alert("error : " + e);
            }
        }
    });
}

/**
 * 刪除商品
 */
function shoppingRmProduct($obj,flag) {
    loadingStart();
    $.ajax({
        url: _jsPath+"/shopping",
        type: "GET",
        data: {
            ajax: "rmProduct",
            productid: $obj.data("productid"),
            specifications: $obj.data("specifications")
        },
        dataType: 'text',
        success: function(msg) {
            try {
                temp = JSON.parse(msg);
                if(typeof(flag) == "undefined"){
                    alert(temp.message);
                    if (!temp.response) {
                        loadingStop();
                        return false;
                    }
                    window.location.reload();
                }
                shoppingListReload();
            } catch (e) {
                alert("error : " + e);
            }
        }
    });
}

/**
 * 修改商品數量
 */
function shoppingEditCount($obj) {
    loadingStart();
    $.ajax({
        url: _jsPath+"/shopping",
        type: "GET",
        data: {
            ajax: "editCount",
            productid: $obj.data("productid"),
            specifications: $obj.data("specifications"),
            count: $obj.val(),
        },
        dataType: 'text',
        success: function(msg) {
            try {
                loadingStop();
                temp = JSON.parse(msg);
                alert(temp.message);
                if (!temp.response) {
                    return false;
                }
                window.location.reload();
            } catch (e) {
                alert("error : " + e);
            }
        }
    });
}

/**
 * loading
 */
function loadingStart(selector,theme){

    if(!selector){
        selector = "body";
    }
    if(theme){
        $(selector).loading( {
            theme: 'dark',
            zIndex: 99999999
        });
    }else{

        $(selector).append('<div class="loader loader-default is-active" data-text="'+_jsMsg["LOADING"]+'"></div>');
        $(selector).loading({
            overlay: $(".loader"),
            zIndex: 99999999
        });
    }
}

function loadingStop(selector){
    if(!selector){
        selector = "body";
    }
    $(selector).loading("stop");
}


$(window).keydown(function(event) {
    if (event.which == 27){
      loadingStop();
    }
});

$(function(){
    $("img").lazyload();
});


//https://console.firebase.google.com
$(function(){
    // fcm();
});
function fcm(){
    if (window.location.href.search('https:')>=0 && typeof(firebase)!="undefined"){
        const messaging = firebase.messaging();
        //--要求授權
        messaging.requestPermission()
        .then(function(){
            return messaging.getToken();
        })
        .then(function(token){ //--取得Token
            $.ajax( {
                url : _jsPath+'/fcm',
                data: {push_token:token},
                type:"GET",
                dataType:'text',
                async: true,
                success: function(msg){
                }
            });
            console.log('Token: '+token);
        })
        .catch(function(err){
            console.log('Have Error: '+err); 
        });
        
        messaging.onMessage(function(payload){ //--接收到通知時
          console.log('onMessage: ', payload);
        });

    }
}