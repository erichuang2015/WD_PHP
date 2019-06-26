// JavaScript Document
//相關連結圖
 var pro_photo_height = $('.link-photo').width() * 3 / 4;
			 	 $('.link-photo').css('height', pro_photo_height  + 'px');

				 window.onresize = function (){
				 		var pro_photo_height = $('.link-photo').width() * 3 / 4;
				 		$('.link-photo').css('height', pro_photo_height  + 'px');
				 }

//產品介紹圖
var pro_photo_height = $('.pro-photo').width() * 4 / 4;
			 	 $('.pro-photo').css('height', pro_photo_height  + 'px');

				 window.onresize = function (){
				 		var pro_photo_height = $('.pro-photo').width() * 4 / 4;
				 		$('.pro-photo').css('height', pro_photo_height  + 'px');
				 }