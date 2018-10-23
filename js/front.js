
function shoppingListReload(shoppingList) {
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
            }
        });
    }else{
        if(typeof(shoppingList.length) == "undefined"){
            shoppingList.length = 0;
        }
        $("#shoppingCountSpan").html("購物車("+shoppingList.length+")");
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
                    shoppingListReload();
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
                loadingStop();
                temp = JSON.parse(msg);
                if (!temp.response) {
                    alert(temp.message);
                    if(temp.data["toUrl"]){
                        location.href = temp.data["toUrl"];
                    }
                    return false;
                }
                shoppingListReload(temp.data);
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
                loadingStop();
                temp = JSON.parse(msg);
                if(typeof(flag) == "undefined"){
                    alert(temp.message);
                    if (!temp.response) {
                        return false;
                    }
                    window.location.reload();
                }
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
            zIndex: 9999
        });
    }else{

        $(selector).append('<div class="loader loader-default is-active" data-text="'+_jsMsg["LOADING"]+'"></div>');
        $(selector).loading({
            overlay: $(".loader"),
            zIndex: 9999
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