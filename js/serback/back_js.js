/**
 * 後台登入js 
 * by MTsung 2018
 */

function grecaptcha(){
    if($('#verifycode').val()==''){
        grecaptcha.execute();
        return false;
    }else{
        remember();
    }
}

function login() {
    remember();
    $('form').submit();
}

function remember() {
    if($('#remember-me:checked').length > 0){
        localStorage.setItem('serbackAccount', $('[name="account"]').val());
        localStorage.setItem('serbackPassword', $('[name="password"]').val());
        // localStorage.setItem('serbackLang', $('[name="lang"]').val());
        localStorage.setItem('serbackRememberMe', 'checked');
    }else{
        localStorage.removeItem("serbackAccount");
        localStorage.removeItem("serbackPassword");
        // localStorage.removeItem("serbackLang");
        localStorage.removeItem("serbackRememberMe");
    }
}

$(function() {
	$('[data-toggle="tooltip"]').tooltip();
    if (localStorage.getItem('serbackRememberMe') == 'checked') {
    	if($('#remember-me')[0]){
	        $('#remember-me')[0].checked = true;
	        $('[name="account"]').val(localStorage.getItem('serbackAccount'));
	        $('[name="password"]').val(localStorage.getItem('serbackPassword'));
    	}
    }
});



/**
 * 後台js 
 * by MTsung 2018
 */


/**
 * 傳送表單 delete用
 */
function formSubmitDelete(index) {
	if(typeof(index) == "undefined"){
		index = 0;
	}
	if($("input[name='checkElement[]']").is(':checked')){
		if(confirm(_jsMsg["DELETE_CONFIRM"])){
			$("form:eq("+index+")").attr("action", location.pathname + "/delete" + location.search);
			$('#hiddenSubmitButton'+index).trigger('click');
			return true;
		}else{
			return false;
		}
	}else{
		alert(_jsMsg["DELETE_SELECT_NULL"]);
		return false;
	}
}


/**
 * 閒置登出
 * @return {[type]} [description]
 */
$(function(){
    setTimeout(checkDoingTime, 5);//最開始檢查一次
});
function checkDoingTime() {
	$.ajax({
        url: _jsPath+"/ajax.php",
        type: "GET",
        data: {
            doingTime: 'serback'
        },
        dataType:'text',
        async: false,
        success: function(msg){
            if(msg=="logout"){
            	toLogin();
            }else if(parseInt(msg)>0){
				setTimeout(checkDoingTime, (parseInt(msg)+10)*1000);
            }
        }
    });
}
function toLogin(){
	alert(_jsMsg["ATOU_LOGOUT"]);
	window.location.href = _jsSerbackPath+"/login";
}

$(function(){
	$("#sidebar").find(".active .collapse a").click(function() {
		setTimeout(loadingStart, 500);
	});
});

/**
 * 分類子項全選
 */
function treeChange(id){
	$(':checkbox[data-parent="'+id+'"]').prop("checked",$(':checkbox[value="'+id+'"]:not(.onoffswitch-checkbox)').is(':checked'));
	$(':checkbox[data-parent="'+id+'"]').each(function(index, el) {
		next = $(this).val();
		if(typeof(next) != "undefined"){
			treeChange(next);
		}
	});
}


/**
 * 分類開合
 */
function treeFoldStar(id){
	$('span[data-id="'+id+'"]').toggleClass("glyphicon-plus glyphicon-minus");
	treeFold(id,!$('span.glyphicon-plus[data-id="'+id+'"]').length);
	tableColor();
}
function treeFold(id,show){
	if(show){
		$('span[data-id="'+id+'"]').removeClass("glyphicon-plus");
		$('span[data-id="'+id+'"]').addClass("glyphicon-minus");
		$('tr[data-parent="'+id+'"]').show();
	}else{
		$('span[data-id="'+id+'"]').removeClass("glyphicon-minus");
		$('span[data-id="'+id+'"]').addClass("glyphicon-plus");
		$('tr[data-parent="'+id+'"]').hide();
	}
	$('tr[data-parent="'+id+'"]').each(function(index, el) {
		next = $(this).data("id");
		if(typeof(next) != "undefined"){
			treeFold(next,show);
		}
	});
}

/**
 * table重新上色
 */
function tableColor(){
	$("tr:visible").each(function (index) {
		$(this).css("background-color", "inherit");
		if (index % 2){
			$(this).css("background-color", "#f2f2f2");
		}
	});
}

/**
 * RWD 選單
 */
$(function() {
    function active() {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
        $('#sidebarCollapse').toggleClass('active');
        $('.div-full').toggleClass('active');
    }
    $('#sidebarCollapse').on('click', active);
    $('.div-full').on('click', active);
});


/**
 * RWD table checkbox全選 選單show select-chosen 取代原本enter直接傳送表單
 */
$(function() {
    
	$("table").basictable(); 
    
    $("#checkAll").click(function() {
        $("input[name='checkElement[]']").prop("checked", $("#checkAll").is(':checked'));
    });

    $('.list-unstyled .collapse').each(function(index,obj){
		$(this).find("li>a").each(function(index1,obj1){
			var temp = location.pathname.replace("/"+$("#langSelect").val(),"");
			var temp1 = $(this).attr("href").replace("/"+$("#langSelect").val(),"");
			if((temp1 != _jsSerbackPath + "/") && (0 === temp.indexOf(temp1))){
	        	$(obj).addClass('show');
	    		$(obj).parent().find("a:eq(0)").attr("aria-expanded","true");
	        }
	    });
	});

    $('select[data-type="chosen"]').each(function(index,obj){
		$(this).chosen({search_contains: true});
	});

    $('input[data-type="pickadate"]').each(function(index,obj){
		$(this).pickadate({
			format: 'yyyy-mm-dd',
			formatSubmit: 'yyyy-mm-dd',
            selectMonths: true,
            selectYears: true,
            selectYears: 200
		});
	});
	
    $('input[data-type="pickatime"]').each(function(index,obj){
		$(this).pickatime();
	});

    $('img[data-type="blowup"]').each(function(index,obj){
		$(this).blowup();
	});

});

$(window).keydown(function(event) {
	if (event.which == 27){
	  loadingStop();
	}

});

jQuery.expr[':'].Contains = function(a, i, m) {  
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;  
};

function searchMenu(value){
	$('#sidebar>ul>li>ul>li').each(function(idx,obj){
	    $(this).parent().removeClass("show");
	    $(this).parent().parent().find("a:eq(0)").attr("aria-expanded","false");
	});
	// if(value){
		$('#sidebar>ul>li>ul>li:Contains("'+value+'")').each(function(idx,obj){
		    $(this).parent().addClass("show");
	    	$(this).parent().parent().find("a:eq(0)").attr("aria-expanded","true");
		});
	// }
	$('#sidebar .active').unhighlight().highlight(value);
}
/**
 * 拖曳排序
 */
$(function(){
	$("table tbody").sortable({
        // axis: 'y',
        // containment: 'parent',
        animation: 200,
	    handle: '.glyphicon-move',
	    // items: 'tr[data-parent="0"]',
	    cancel: '',
        stop: function(){
            tableSort();
            tableColor();
        }
	});
	tableMinIndex = $('tr').find("input[type='text']").eq(0).val();
});

function tableSort(){
	if(typeof($('tbody tr').eq(0).data("parent"))!="undefined"){
		//分類樹
		$('tbody tr').each(function (index,obj){
			trPermutation($(obj).data('id'));
	    });

		$('tbody tr').each(function (index,obj){
			min = Math.min.apply(null, $('tr[data-parent="'+$(obj).data('parent')+'"').find("input[type='text']").map(function(){return $(this).val();}).get())
	    	// min = Math.min(...$('tr[data-parent="'+$(obj).data('parent')+'"').find("input[type='text']").map(function(){return $(this).val();}).get());
			$('tr[data-parent="'+$(obj).data('parent')+'"]').find("input[type='text']").each(function (index1,obj1){
	    		$(obj1).val(min*1+index1*1);
		    });
	    });
	}else{
		//一般
	    $('tbody tr').find("input[type='text']").each(function (index,obj){
	    	$(obj).val(tableMinIndex*1+index*1);
	    });
	}
}
/**
 * 移動完重新排序tr
 */
function trPermutation(id){
	if($('tr[data-parent="'+id+'"]').length>0){
		$('tr[data-id="'+id+'"]').after($('tr[data-parent="'+id+'"]'));
		$('tr[data-parent="'+id+'"]').each(function(index,obj){
			trPermutation($(obj).data('id'));
		});
	}
}

/**
 * bootstrap toggle啟動
 */
$(function() {
	$('[data-toggle="dropdown"]').dropdown();
    $('[data-toggle="tooltip"]').tooltip();
});

/**
 * td最大寬度
 */
$(function() {
    tdMaxWidth();
});
$(window).resize(function(){ 
    tdMaxWidth();
});
function tdMaxWidth(){
    $('td').each(function (index,obj){
		if($(window).width() <= 768){
	    	$(obj).css("max-width","");
			$(obj).css("overflow","");
    		// $(obj).css("white-space","");
    		$(obj).css("text-overflow","");
		}else{
	    	if(!isNaN($(obj).data("max_width"))){
	    		$(obj).css("max-width",$(obj).data("max_width"));
	    	}else{
	    		$(obj).css("max-width",250);//預設250
	    	}
			$(obj).css("overflow","hidden");
			// $(obj).css("white-space","nowrap");
			$(obj).css("text-overflow","ellipsis");
		}
    });
}

/**
 * 設定管理語系
 */
function setLang(value){
    $.ajax({
        url: _jsPath+"/ajax.php",
        type: "GET",
        data: {
            setSettingLang: value
        },
        dataType:'text',
        async: false,
        success: function(msg){
        	window.location.reload();
        }
    });
}


/**
 * bytes轉換 <intput data-type="size">
 */
function readablizeBytes(bytes) {
	if($.isNumeric(bytes) && bytes!=0){
	    var s = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
	    var e = Math.floor(Math.log(bytes)/Math.log(1024));
	    e = e > s.length-1 ? s.length-1 : e;//最多顯示PB
	    return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
	}else{
		return false;
	}
}
function setSizeSmall(value){
	var size = readablizeBytes($('[name="'+value+'"]').val());
	if(size){
		$('#'+value+'Small').html(size);
	}else{
		$('#'+value+'Small').html('<font color="red">'+_jsMsg["FORMAT_ERROR"]+'</font>');
	}
}
$(function() {
	$('input').each(function (){
		if($(this).data("type")=="size"){
			$('[name="'+$(this).attr('name')+'"]').keyup(function (){
    			setSizeSmall($(this).attr('name'));
    		});
    		setSizeSmall($(this).attr('name'));
		}
	})
});

/**
 * 剪裁圖片
 */
function cropperImage(data){
	var src = data.src.toLowerCase();
	if(src.indexOf(".jpg")!=-1 || src.indexOf(".jpeg")!=-1 || src.indexOf(".bmp")!=-1 || src.indexOf(".png")!=-1){
		if(confirm(_jsMsg["CROPPER_CONFIRM"])){
			$.ajax({
		        url: _jsPath+"/ajax.php",
		        type: "GET",
		        data: {
		            cropperImage: '1',
		            x: parseInt(data.x),
		            y: parseInt(data.y),
		            w: parseInt(data.width),
		            h: parseInt(data.height),
		            src: data.src
		        },
		        dataType:'text',
		        async: false,
		        success: function(msg){
		            $("img[src*='"+data.src+"']").attr("src", data.src+"?aaa=" + new Date().getTime());
		        }
		    });
		    return true;
		}
	}else{
		alert(_jsMsg["CROPPER_TYPE"]);
		return false;
	}
	
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
	    	zIndex: 9999999
	    });
	}else{

		$(selector).append('<div class="loader loader-default is-active" data-text="'+_jsMsg["LOADING"]+'"></div>');
		$(selector).loading({
			overlay: $(".loader"),
			zIndex: 9999999
		});
	}
}

function loadingStop(selector){
	if(!selector){
		selector = "body";
	}
    $(selector).loading("stop");
}