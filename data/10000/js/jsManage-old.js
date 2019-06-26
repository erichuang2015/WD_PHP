// JavaScript Document
	$(function(){
		//mobile classLink
		var $clone = $('ul.classLink').clone().removeClass('classLink'),
			  $current_txt = $('ul.classLink').find('.current').text();
		var $flag = true;
		$('ul.classLink').after('<div class="m_classLink"><a class="main"><b></b><i class="fa fa-angle-down"></i></a></div>');
		$('.m_classLink').append($clone).end().find('a.main b').text($current_txt);
		$('.m_classLink').find('a.main').on("click",function(){
			if($flag){
				$(this).next('ul').stop().slideDown(200);
				$(this).find('i').removeClass('fa fa-angle-down').addClass('fa fa-angle-up');
				$flag = false;
			}
			else{
				$(this).next('ul').stop().slideUp(200);
				$(this).find('i').removeClass('fa fa-angle-up').addClass('fa fa-angle-down');
				$flag = true;
			}
		})
	});