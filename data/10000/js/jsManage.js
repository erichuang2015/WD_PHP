// JavaScript Document
$(function(){
	//mobile classLink
    $('ul.classLink').each(function(k,v){
    
        var $clone = $(v).clone().removeClass('classLink'),
        $current_txt = $(v).find('.current').text();
        var $flag = true;
        $(v).after('<div class="m_classLink"><a class="main"><b></b><i class="fa fa-angle-down"></i></a></div>');
        $(v).next('.m_classLink').append($clone);
    	$(v).next('.m_classLink').find('a.main b').text($current_txt);
        $(v).next('.m_classLink').find('a.main').on("click",function(){
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
    $('a.main').each(function(){
    	if ($(this).html()=="<b></b><i class=\"fa fa-angle-down\"></i>") {$(this).html("<b>請選擇</b><i class='fa fa-angle-down'></i>");}
    })
});