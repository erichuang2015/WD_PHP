/**
 * 表單驗證
 * required data-check="可用,區隔" data-text="" data-check_min="" data-check_max=""
 */

/**
 * 表單清除 設計師使用a tag時用
 */
function formReset(index){
	if(typeof(index) == "undefined"){
		index = 0;
	}
	$("form:eq("+index+")")[0].reset();
	return false;
}

$(function() {
	$('input').on('keydown', function(e){
		if((e.which == 13 || (e.which == 83 && e.ctrlKey)) && $(this).attr("name")){
			formSubmit($("form").index($(this).form()));
		}
		return e.which !== 13;
	});
	$('textarea').on('keydown', function(e){
		if(((e.which == 83 && e.ctrlKey)) && $(this).attr("name")){
			formSubmit($("form").index($(this).form()));
		}
	});
});
$(window).keydown(function(event) {
	if ((event.which == 83 && event.ctrlKey)){
	  event.preventDefault();
	}
});

/**
 * 傳送表單
 */
var onlyValues=[];
function formSubmit(index) {
	onlyValues=[];
	if(typeof(index) == "undefined"){
		index = 0;
	}
	
	tempInput = [];
	msg = "";
	$(".chosen-container").removeClass("is-invalid");
	$("div").removeClass("is-invalid");
	$("form:eq("+index+")").find('input,textarea,select').removeClass("is-invalid");
	$("form:eq("+index+")").find('input,textarea,select').each(function(index) {
		if(!$(this).attr("name")){
			return ;
		}
		if($(this).attr("type") == "checkbox" || $(this).attr("type") == "radio"){
			if($(this).data("check_min")){
				if($("input[name='"+$(this).attr("name")+"']:checked").length < $(this).data("check_min")){
					msg += " [" + ($(this).data("text")?$(this).data("text"):$(this).attr("name")) + "] " + _jsMsg["LEAST_SELECT"] + " " + $(this).data("check_min") + " " + _jsMsg["ITEM"] + "\n";
					$(this).parent().addClass("is-invalid");
				}
			}
			if($(this).data("check_max")){
				if($("input[name='"+$(this).attr("name")+"']:checked").length > $(this).data("check_max")){
					msg += " [" + ($(this).data("text")?$(this).data("text"):$(this).attr("name")) + "] " + _jsMsg["SELECT_ACHIEVE_MAX"] + " " + $(this).data("check_max") + " " + _jsMsg["ITEM"] + "\n";
					$(this).parent().addClass("is-invalid");
				}
			}
		}else{
			if($(this).attr("required") && $(this).val()==""){
				// if ($("<input />").prop("required") === undefined || $(this).data("type")=="chosen" || $(this).is(":hidden")){//測試瀏覽器是否支援HTML5的required
				msg += " [" + ($(this).data("text")?$(this).data("text"):$(this).attr("name")) + "] " + _jsMsg["FIELD_REQURED"] + "\n";
				if($(this).data("type")=="chosen"){
					$(this).next("div").addClass("is-invalid");
				}else{
					tempInput.push(this);
					$(this).addClass("is-invalid");
				}
				// }
			}
			if($(this).data("check") && $(this).val()){
				var check = $(this).data("check").split(",");
				var dataText = $(this).data("text");
				var dataName = $(this).attr("name");
				var dataVal = $(this).val();
				var dataObj = $(this);
				check.forEach(function(value){
					fun = "is" + value;
					fun1 = "msg" + value;
					if(typeof(window[fun]) == "function" && typeof(window[fun1]) == "function"){
						try{
							if(!eval(fun+"('"+dataVal+"')")){
								msg += " [" + (dataText?dataText:dataName) + "] " + _jsMsg["FIELD_FORMAT_ERROR"] + " " + eval(fun1+"()") + "\n";
								tempInput.push(dataObj);
								dataObj.addClass("is-invalid");
							}
						}catch(exception){
							msg += " [" + (dataText?dataText:dataName) + "] " + _jsMsg["FIELD_FORMAT_ERROR"] + " " + eval(fun1+"()") + "\n";
							tempInput.push(dataObj);
							dataObj.addClass("is-invalid");
						}
					}else{
						console.error("data-check error : "+fun+" is not funtion");
					}
				});
			}
		}
	});
	if($('input[data-check="Password"]')){
		parent = "";
		$('input[data-check="Password"]').each(function(index){
			if(parent && (parent!=$(this).val())){
				msg += _jsMsg["USER_PASSWORD_CHECK_ERROR"];
				$(this).addClass("is-invalid");
				tempInput.push(this);
			}
			parent = $(this).val();
		});
	}

	if(msg){
		$(tempInput).each(function(){
			if($(this).is(":visible")){
				$(this).focus();
				return false;
			}
		});
		alert(msg);
		return false;
	}else{
		$('#hiddenSubmitButton'+index).trigger('click');
		setTimeout(loadingStart, 100);
	}
}

/**
 * 防止重複提交&表單確認
 */
$(function(){
	$("form").each(function(index,el){
		$("form:eq("+index+")").append('<button type="submit" style="display:none" id="hiddenSubmitButton'+index+'"></button>');
		$("form:eq("+index+")").on('submit', function(){
			return oneSubmit();
	    });

	});
});
var oneSubmitCount = 0;
function oneSubmit() {
	oneSubmitCount++;
	return (oneSubmitCount==1);
}



function isIDCard(id){
    var head = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
    var A1 = new Array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3);
    var A2 = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5);
    var Mx = new Array(0, 8, 7, 6, 5, 4, 3, 2, 1, 1);
    var i = head.indexOf(id.charAt(0));
    if((id.length != 10) || (i == -1)){
    	return false;
    }

    var sum = A1[i] + A2[i] * 9;

    for(i = 1; i < 10; i++){
        v = parseInt(id.charAt(i));
        if(isNaN(v)){
        	return false;
        }
        sum = sum + v * Mx[i];
    }

    return ((sum % 10) == 0);
}

function msgIDCard(){
	return "";
}

function isAccount(account){
	var checkString=/[^a-zA-Z0-9_@.]/g;
	return 	(account.length > 4) &&
			(account.length < 50) &&
			(!account.match(checkString));
}

function msgAccount(){
	return _jsMsg["LAN_5_49_AND_SYMBOL"];
}

function isEnglishNumber(string){
	var checkString=/[\W]/g;
	return (!string.match(checkString));
}

function msgEnglishNumber(){
	return _jsMsg["IS_ENGLISH_OR_NUMBER"];
}

function isPassword(password){
	var checkString = /^(?=^.{8,255}$)((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]))^.*$/;
	return password.match(checkString);
}

function msgPassword(){
	return _jsMsg["PASSWORD_MSG"];
}

function isEmail(email){
	var checkString = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/;
	if(email.match(checkString)){
		var result;
		$.ajax({
		    url: _jsPath+"/ajax.php",
		    type: "GET",
		    data: {
		        isEmail: email
		    },
		    dataType:'text',
		    async: false,
		    success: function(msg){
		    	result = (msg == "ok");
		    }
		});
		return result;
	}else{
		return false;
	}
}

function msgEmail(){
	return "";
}

function isNumber(number){
	return 	!isNaN(number);
}

function msgNumber(){
	return  _jsMsg["IS_NUMBER"];
}

function isNumberMin450(number){
	return 	!isNaN(number) && (number>=450 || number==0);
}

function msgNumberMin450(){
	return  _jsMsg["IS_NUMBER_MIN450"];
}

function isOnly(data){
	if(onlyValues.indexOf(data)>=0){
		return false;
	}
	onlyValues.push(data);
	return true;
}

function msgOnly(){
	return  _jsMsg["IS_ONLY"];
}


function isSenderName(data){
	var checkString=/['`!＠#%&*+\""<>|_\[\],，]/g;
	return 	(stringBytes(data)<=10) &&
			(!data.match(checkString));
}

function msgSenderName(){
	return _jsMsg["IS_10SIZE_NOT_SPECIAL"];
}

function isSenderPhone(data){
	var checkString=/[^0-9_()#-]/g;
	return 	(stringBytes(data)<=20) &&
			(!data.match(checkString));
}

function msgSenderPhone(){
	return _jsMsg["IS_20SIZE_NOT_SPECIAL"];
}

function isSenderCellPhone(data){
	var checkString = /^09[0-9]{8}$/;
	return 	!!data.match(checkString);
}

function msgSenderCellPhone(){
	return _jsMsg["IS_NUMBER_10SIZE_09HEAD"];
}

function isSenderZipCode(data){
	return 	!isNaN(data) && (stringBytes(data)<=5);
}

function msgSenderZipCode(){
	return _jsMsg["IS_NUMBER_5SIZE"];
}

function isSenderAddress(data){
	return 	(stringBytes(data)<=60);
}

function msgSenderAddress(){
	return _jsMsg["IS_60SIZE"];
}





/**
 * 計算長度 (中文2 英文1)
 */
function stringBytes(c) {
    var n = c.length,s;
    var len = 0;
    for (var i = 0; i < n; i++) {
        s = c.charCodeAt(i);
        while (s > 0) {
            len++;
            s = s >> 8;
        }
    }
    return len;
}