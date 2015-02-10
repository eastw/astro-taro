$(document).ready(function() {	
	$('#podstava').on('click', function() {
		var podstavaText = $(this).text();
		$('#input').attr('value', podstavaText);
	});
	
	$('#enter, .profile_block').on('click', function() {
		$('#enter_form').stop().fadeToggle(200);
	});
	
	$('.vhod').click(function(){
		$('#enter').click();
		$('#enter_form').stop().fadeToggle(200);
	});
	
	$("#top, #content, #content_white, #footer").click(function() {
		$('#enter_form').stop().fadeOut(200);
	});
	$('#enter_form, #enter, .profile_block').on('click', function(e) {
		e.stopImmediatePropagation();
	});
	
	$('.signification, .signification_close').on('click', function() {
		$('#signification').stop().fadeToggle(200);
	});
	$("#top, #content, #content_white, #footer").click(function() {
		$('#signification').stop().fadeOut(200);
	});
	$('#signification, .signification').on('click', function(e) {
		e.stopImmediatePropagation();
	});
	
	var i = 0;
	$('#feedback_button, #nope').on('click', function() {
		if (i == 0) {
			$('#feedback').stop().animate({"right" : "0px"}, 300);
			i = 1;
			}
		else {
			$('#feedback').stop().animate({"right" : "-385px"}, 300);
			i = 0;
			};
	});
	$("#top, #content, #content_white, #footer").click(function() {
		$('#feedback').stop().animate({"right" : "-385px"}, 300);
		i = 0;
	});
	$('#feedback, #feedback_button').on('click', function(f) {
		f.stopImmediatePropagation();
	});

	
	$('#popup, .popup_pult_item2, .close').on('click', function() {
		$('#popup').css({"display" : "none"});
		$('#popup_content').css({"display" : "block"});
		$('#popup_final').css({"display" : "none"});
		$('#list5').css({"display" : "none"});
		$('#list1').css({"display" : "block"});
	});
	$('#popup_content').on('click', function(p) {
		p.stopImmediatePropagation();
	});
	
	$('#popup_info').children('div').first().show();
	
	$('#popup_pult div').on('click', function() {
		if ($(this).index() == 1) {
			$('#popup_nav').css({"display" : "none"});
			$('#popup_orange_line').css({"display" : "block"});
			$('.popup_pult_item_active').removeClass('popup_pult_item_active').addClass('popup_pult_item');
			$(this).removeClass('popup_pult_item').addClass('popup_pult_item_active');
			$('#popup_info').fadeOut(200, function() {
				$('#popup_otzivi').fadeIn(200);
				});
			}
		if ($(this).index() == 0) {
			$('#popup_nav').css({"display" : "block"});
			$('#popup_orange_line').css({"display" : "none"});
			$('.popup_pult_item_active').removeClass('popup_pult_item_active').addClass('popup_pult_item');
			$(this).removeClass('popup_pult_item').addClass('popup_pult_item_active');
			$('#popup_otzivi').fadeOut(200, function() {
				$('#popup_info').fadeIn(200);
				});
			}
		});
		
	$('.popup_enter_back_order').on('click', function() {
		$('#popup_nav').css({"display" : "block"});
		$('#popup_orange_line').css({"display" : "none"});
		$('#popup_pult div').eq(1).removeClass('popup_pult_item_active').addClass('popup_pult_item');
		$('#popup_pult div').eq(0).removeClass('popup_pult_item').addClass('popup_pult_item_active');
		$('#popup_otzivi').fadeOut(200, function() {
			$('#popup_info').fadeIn(200);
			});
		});
	typeSelectCheck();
	
	$('.popup_vip').on('click', function() {
		typeSelectCheck();
	});
	
	function typeSelectCheck(){
		var param = customSelectValue('order-type');
		if (param == "Персональный гороскоп") {
			$('.popup_theme').css({"display" : "block"});
			$('.popup_theme2').css({"display" : "none"});
			}
		else {
			$('.popup_theme').css({"display" : "none"});
			$('.popup_theme2').css({"display" : "block"});
		}
		/*
		var param = $('.popup_vip .selected').text();
		if (param == "Персональный гороскоп") {
			$('.popup_theme').css({"display" : "block"});
			$('.popup_theme2').css({"display" : "none"});
			}
		else {
			$('.popup_theme').css({"display" : "none"});
			$('.popup_theme2').css({"display" : "block"});
		}
		*/
	}
	
	$('.popup_enter').on('click', function() {
		$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
		var param = $('.popup_vip .selected').text();
		var param2 = '';
		if (param == "Персональный гороскоп") {
			param2 = $('.popup_theme .selected').text();
			if (param2 == "Гороскоп любовной совместимости" || param2 == "Гороскоп партнерской совместимости") {
				$('#popup_info').children('#list1').fadeOut(200, function() {
					$('#popup_info').children('#list3').fadeIn(200);
					$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
				});
			}else{
				
				$('#popup_info').children('#list1').fadeOut(200, function() {
					//$('select').selectbox();
					$('#popup_info').children('#list2').fadeIn(200);
					$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
				});
			}
		}
		else {
			//param2 = $('.popup_theme2 .selected').text();
			$('#popup_info').children('#list1').fadeOut(200, function() {
				$('#popup_info').children('#list4').fadeIn(200);
				$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
			});
		};
	});
		
	var memory = 0;	
	$('.popup_enter2').on('click', function() {
		memory = 2;
		if($('#list2-name').val() != '' &&  $('#list2-city').val() != ''){
			loadPrice();
			$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
			$('#popup_info').children('#list2').fadeOut(200, function() {
				$('#popup_info').children('#list5').fadeIn(200);
				$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
			});
		}else{
			$('#list2-name').removeClass('wrong_form');
			$('#list2-city').removeClass('wrong_form');
			if($('#list2-name').val() == ''){
				$('#list2-name').addClass('wrong_form');
			}
			if($('#list2-city').val() == ''){
				$('#list2-city').addClass('wrong_form');
			}
		}
	});
	
	$('.popup_enter3').on('click', function() {
		memory = 3;
		if(
			$('#list3-name1').val() != '' &&  $('#list3-city1').val() != ''
			&& $('#list3-name2').val() != '' &&  $('#list3-city2').val() != ''
		){
			loadPrice();
			$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
			$('#popup_info').children('#list3').fadeOut(200, function() {
				$('#popup_info').children('#list5').fadeIn(200);
				$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
			});
		}else{
			$('#list3-name1').removeClass('wrong_form');// != '' &&  $('#list3-city1').val() != ''
			$('#list3-city1').removeClass('wrong_form');
			$('#list3-name2').removeClass('wrong_form');
			$('#list3-city2').removeClass('wrong_form');
			if($('#list3-name1').val() == ''){
				$('#list3-name1').addClass('wrong_form');
			}
			if($('#list3-city1').val() == ''){
				$('#list3-city1').addClass('wrong_form');
			}
			if($('#list3-name2').val() == ''){
				$('#list3-name2').addClass('wrong_form');
			}
			if($('#list3-city2').val() == ''){
				$('#list3-city2').addClass('wrong_form');
			}
		}
	});
	
	$('#list3-name1,#list3-name2,#list3-city1,#list3-city2').keyup(function(){
		$(this).removeClass('wrong_form');
	});
	
	$('.popup_enter4').on('click', function() {
		memory = 4;
		if($('#list4-name').val() != ''){
			loadPrice();
			$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
			$('#popup_info').children('#list4').fadeOut(200, function() {
				$('#popup_info').children('#list5').fadeIn(200);
				$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
			});
			
		}else{
			$('#list4-name').removeClass();
			if($('#list4-name').val() == ''){
				$('#list4-name').addClass('wrong_form');
			}
		}
	});
	$('#list4-name').keyup(function(){
		$(this).removeClass('wrong_form');
	});
	
	function loadPrice(){
		var type = customSelectValue('order-type');
		var subtype = '';
		if(customSelectValue('order-type') == 'Персональный гороскоп'){
			type = 'horoscope';
			subtype = customSelectValue('horoscope-type');
		}else{
			type = 'divination';
			subtype = customSelectValue('divination-type');
		}
		$.post('/index/service-price',
				{
					'type'		: type,
					'subtype' 	: subtype
				},function(data){
					$('.summa').html(data.summ);
				},'json');
	}
	
	$('.popup_enter5').on('click', function() {
		//var r = /^[\w\.\d-_]+@[\w\.\d-_]+\.\w{2,4}$/i;
		var r = /[a-z0-9!$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|ua|ru|jobs|museum)\b/
		if($('#pay-email').val() != '' && r.test($('#pay-email').val()) && $('#agree-check').is(':checked')){
			$('#popup_content').fadeOut(200, function() {
				$('#popup_final').fadeIn(200,function(){
					var type = '';
					var subtype = '';
					var name1 = '';
					var name2 = '';
					var year1 = '';
					var year2 = '';
					var month1 = '';
					var month2 = '';
					var day1 = '';
					var day2 = '';
					var city1 = '';
					var city2 = '';
					var hour1 = '';
					var hour2 = '';
					var minute1 = '';
					var minute2 = '';
					var dontknow1 = '';
					var dontknow2 = '';
					var question = '';
					var email = '';
					var payment_type = '';
					var order_comments = '';
					var question = '';
					if(customSelectValue('order-type') == 'Персональный гороскоп'){
						type = 'horoscope';
					}else{
						type = 'divination';
					}
					if(type == 'horoscope'){
						subtype = customSelectValue('horoscope-type');//$('#horoscope-type').val();
						if(subtype == 'Гороскоп любовной совместимости' || subtype == 'Гороскоп партнерской совместимости'){
							name1 = $('#list3-name1').val();
							name2 = $('#list3-name2').val();
							city1 = $('#list3-city1').val();
							city2 = $('#list3-city2').val();
							
							year1 = customSelectValue('me-year2');
							month1 = customSelectValue('me-month2');
							day1 = customSelectValue('me-day2');
							
							hour1 = customSelectValue('me-hour2');
							minute1 = customSelectValue('me-minute2');
							
							dontknow1 = ($('#me-dontknow2').is(':checked'))?'on':'off';
							
							year2 = customSelectValue('partner-year');
							month2 = customSelectValue('partner-month');
							day2 = customSelectValue('partner-day');
							
							hour2 = customSelectValue('partner-hour');
							minute2 = customSelectValue('partner-minute');
							
							dontknow2 = ($('#partner-dontknow').is(':checked'))?'on':'off';
							
						}else{
							name1 = $('#list2-name').val();
							city1 = $('#list2-city').val();
							
							year1 = customSelectValue('me-year1');
							month1 = customSelectValue('me-month1');
							day1 = customSelectValue('me-day1');
							
							hour1 = customSelectValue('me-hour1');
							minute1 = customSelectValue('me-minute1');
							
							dontknow1 = ($('#me-dontknow1').is(':checked'))?'on':'off';
						}
						//name1 = $('#').val();
					}
					if(type == 'divination'){
						subtype = customSelectValue('divination-type');//$('#divination-type').val();
						
						name1 = $('#list4-name').val();
						city1 = $('#list4-city').val();
						
						year1 = customSelectValue('me-year3');
						month1 = customSelectValue('me-month3');
						day1 = customSelectValue('me-day3');
						question = $('#question').val();
					}
					
					email = $('#pay-email').val();
					payment_type = customSelectValue('payment-type');
					order_comments = $('#order-comments').val();
					
					$.post('/index/service',
							{
								'type' 				: type,
								'subtype'			: subtype,
								'name1' 			: name1,
								'name2'				: name2,
								'city1'				: city1,
								'city2'				: city2,
								'year1'				: year1,
								'year2'				: year2,
								'month1'			: month1,
								'month2'			: month2,
								'day1'				: day1,
								'day2'				: day2,
								'hour1'				: hour1,
								'hour2'				: hour2,
								'minute1'			: minute1,
								'minute2'			: minute2,
								'dontknow1'			: dontknow1,
								'dontknow2'			: dontknow2,
								'question'			: question,
								'email'				: email,
								'payment_type'		: payment_type,
								'order-comments'	: order_comments
							},
							function(data){
								
							},'json');
				});
			});
		}else{
			$('#agree-error').hide();
			$('#pay-email').removeClass('wrong_form');
			if($('#pay-email').val() == ''){
				$('#pay-email').addClass('wrong_form');
			}
			if(!$('#agree-check').is(':checked')){
				$('#agree-error').show();
			}
			if(!r.test($('#pay-email').val())){
				$('#pay-email').addClass('wrong_form');
			}
		}
	});
	
	function customSelectValue(id){
		var value = $('#'+id+' option:selected').val();
		var text = $('#'+id+'').prev().find('div.text').text();
		$('#'+id+' option').each(function(){
			if($(this).text() == text){
				value= $(this).val();
			}
		});
		return value;
	}
	
	$('#pay-email').keyup(function(){
		$(this).removeClass('wrong_form');
	});
	
	$('.popup_exit2').on('click', function() {
		$('#popup_nav').children('.popup_nav_item2_active').first().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
		$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
		$('#popup_info').children('#list2').fadeOut(200, function() {
			$('#popup_info').children('#list1').fadeIn(200);
		});
	});
	
	$('.popup_exit3').on('click', function() {
		$('#popup_nav').children('.popup_nav_item2_active').first().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
		$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
		$('#popup_info').children('#list3').fadeOut(200, function() {
			$('#popup_info').children('#list1').fadeIn(200);
		});
	});
	
	$('.popup_exit4').on('click', function() {
		$('#popup_nav').children('.popup_nav_item2_active').first().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
		$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
		$('#popup_info').children('#list4').fadeOut(200, function() {
			$('#popup_info').children('#list1').fadeIn(200);
		});
	});
	
	$('.popup_exit5').on('click', function() {
		if (memory == 2) {
			$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
			$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
			$('#popup_info').children('#list5').fadeOut(200, function() {
				$('#popup_info').children('#list2').fadeIn(200);
			});
			}
		else {
			if (memory == 3) {
				$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
				$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
				$('#popup_info').children('#list5').fadeOut(200, function() {
					$('#popup_info').children('#list3').fadeIn(200);
				});
				}
			else {
				$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
				$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
				$('#popup_info').children('#list5').fadeOut(200, function() {
					$('#popup_info').children('#list4').fadeIn(200);
				});
				}
			}
		});
		
	$('#popup_nav').on('click', '.popup_nav_item2_active', function() {
		if ($(this).index() == 0) {
			$('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
			$('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
			$('.popup_nav_item2_active').first().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active').next('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
			$('#popup_info').children('div:visible').fadeOut(200, function() {
				$('#popup_info').children('#list1').fadeIn(200);
			});
		}
		else {
			if (memory == 2) {
			$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
			$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
			$('#popup_info').children('#list5').fadeOut(200, function() {
				$('#popup_info').children('#list2').fadeIn(200);
			});
			}
		else {
			if (memory == 3) {
				$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
				$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
				$('#popup_info').children('#list5').fadeOut(200, function() {
					$('#popup_info').children('#list3').fadeIn(200);
				});
				}
			else {
				$('#popup_nav').children('.popup_nav_item2_active').last().removeClass('popup_nav_item2_active').addClass('popup_nav_item_active');
				$('#popup_nav').children('.popup_nav_item_active').last().removeClass('popup_nav_item_active').addClass('popup_nav_item');
				$('#popup_info').children('#list5').fadeOut(200, function() {
					$('#popup_info').children('#list4').fadeIn(200);
				});
				}
			}
		}
	});
	
	$('#list2-name').keyup(function(){
		$(this).removeClass('wrong_form');
	});
	$('#list2-city').keyup(function(){
		$(this).removeClass('wrong_form');
	});
		
	var signNum = 0;
	var signTemp = 0;
	$('.sign_small_item').eq(signNum).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
	$('.sign_item').eq(signNum).css({"display" : "block"});
	$('.sign_small_item').on('click', function() {
		signTemp = signNum;
		signNum = $(this).index();
		
		//$('#sign-all-horoscoper').attr('href','/horoscope/' + $(this).find('input').val() + '/today');
		
		if (signNum == signTemp) {}
		else {
			$('.sign_small_item').eq(signTemp).css({"background" : "none"});
			$(this).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
			$('.sign_item').eq(signTemp).fadeOut(200, function() {
				$('.sign_item').eq(signNum).fadeIn(200);
			});
		};
	});
	
	$('.sign_small_item').on('mouseover', function() {
		if (signNum == $(this).index()) {}
		else {
			$(this).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
		};
	});	
	$('.sign_small_item').on('mouseleave', function() {
		if (signNum == $(this).index()) {}
		else {
			$(this).css({"background" : "none"});
		};
	});
	
	$('#horo_arrow div.arrow_right').on('click', function() {
		signTemp = signNum;
		signNum++;
		if (signNum >= 12) {signNum = 0}
		$('.sign_small_item').eq(signTemp).css({"background" : "none"});
		$('.sign_small_item').eq(signNum).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
		$('.sign_item').eq(signTemp).fadeOut(200, function() {
			$('.sign_item').eq(signNum).fadeIn(200);
		});
	});
	
	$('#horo_arrow div.arrow_left').on('click', function() {
		signTemp = signNum;
		signNum--;
		if (signNum <= -1) {signNum = 11}
		$('.sign_small_item').eq(signTemp).css({"background" : "none"});
		$('.sign_small_item').eq(signNum).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
		$('.sign_item').eq(signTemp).fadeOut(200, function() {
			$('.sign_item').eq(signNum).fadeIn(200);
		});
	});
	
	var pultNum = 0;
	var pultTemp = 0;
	$('.pult_item').eq(pultNum).css({"background-color" : "#fff", "color" : "#000"});
	$('.zak_item').eq(pultNum).css({"display" : "block"});
	$('.pult_item').on('click', function() {
		pultTemp = pultNum;
		pultNum = $(this).index();
		if (pultNum == pultTemp) {}
		else {
			$('.pult_item').eq(pultTemp).css({"background-color" : "#ff6601", "color" : "#fff"});
			$(this).css({"background-color" : "#fff", "color" : "#000"});
			$('.zak_item').eq(pultTemp).fadeOut(200, function() {
				$('.zak_item').eq(pultNum).fadeIn(200);
			});
		};
	});
	
	var regNum = 0;
	var regTemp = 0;
	$('.vklad_reg_pult_item').eq(regNum).css({"background-color" : "#eeeaf1", "color" : "#434c5d"});
	$('.vklad_reg_item').eq(regNum).css({"display" : "block"});
	$('.vklad_reg_pult_item').on('click', function() {
		$('.vipadalki .selectbox').css({"float" : "left"});
		$('.vipadalki .selectbox').eq(1).css({"margin" : "0 42px"});
		regTemp = regNum;
		regNum = $(this).index();
		if (regNum == regTemp) {}
		else {
			$('.vklad_reg_pult_item').eq(regTemp).css({"background-color" : "#ff6601", "color" : "#fff"});
			$(this).css({"background-color" : "#eeeaf1", "color" : "#434c5d"});
			$('.vklad_reg_item').eq(regTemp).fadeOut(200, function() {
				$('.vklad_reg_item').eq(regNum).fadeIn(200);
			});
		};
	});
	
	$("#up").hide();
	$(window).scroll(function (){
		if ($(this).scrollTop() > 1400){
			$("#up").fadeIn();
		} else{
			$("#up").fadeOut();
		};
	});
	$('#up').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
    });
	
	$('.article_news_item, .button, .magic_item, .all, .orange_sign_item, .orange_sign_item2, .orange_sign_item3, .orange_sign_item4, .vklad_item, .hrskp_item, .popular_item, .article_mini_item, .upr_item, .moon_row_item').on('click', function() {
		if(undefined != $(this).find('a').attr('href')){
			window.location = $(this).find('a').attr('href');
		}
		
	});
	
	var valueTemp = '';
	$('input').on('focusin', function() {
		valueTemp = $(this).attr('value');
		if($(this).attr('id') != 'poll' && $(this).attr('id') != 'word-search-button') {
			$(this).attr('value', '');
		}
	});
	$('input').on('focusout', function() {
		if (valueTemp = 'undefined') {
			if($(this).attr('value') == ''){
				$(this).css('background-color','background-color: rgb(255, 253, 214)');
			}else{
				$(this).removeClass('sure');
			}
                }
		else {
			$(this).attr('value', valueTemp);
		};
	});
	
	var textRoll = 0;
	$('.open_text').on('click', function() {
		if (textRoll == 0) {
			$(this).html('<span>свернуть</span> ↑');
			$('.text_roll').slideDown(200);
			textRoll = 1;
		}
		else {
			$(this).html('<span>развернуть</span> ↓');
			$('.text_roll').slideUp(200);
			textRoll = 0;	
		}
	});
	
	var cornerPresent = 0;
	$('.lilac_item').on('mouseenter', function() {
		if ($(this).hasClass('corner_vip') || $(this).hasClass('corner_vip_white')) {
			cornerPresent = 1;
			if ($('#top').next().attr('id') == "content") {
				$(this).children('div').stop().fadeIn(200);
			}
			else {
				if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
					$(this).children('div').stop().fadeIn(200);
					}
				else {
					$(this).children('div').stop().fadeIn(200);
				};
			};
		}
		else {
			cornerPresent = 0;
			if ($('#top').next().attr('id') == "content") {
				$(this).addClass('corner_vip').children('div').stop().fadeIn(200);
			}
			else {
				/*
				if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
					$(this).addClass('corner_vip').children('div').stop().fadeIn(200);
					}
				else {
				*/
					$(this).addClass('corner_vip_white').children('div').stop().fadeIn(200);
				//};
			};
		};
	});
	$('.lilac_item').on('mouseleave', function() {
		if (cornerPresent == 0) {
			if ($('#top').next().attr('id') == "content") {
				$(this).removeClass('corner_vip').children('div').stop().fadeOut(200);
			}
			else {
				/*
				if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
					$(this).removeClass('corner_vip').children('div').stop().fadeOut(200);
					}
					
				else {
				*/
					$(this).removeClass('corner_vip_white').children('div').stop().fadeOut(200);
				//};
			};
		}
		else {
			if ($('#top').next().attr('id') == "content") {
				$(this).children('div').stop().fadeOut(200);
			}
			else {
				
				if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
					$(this).children('div').stop().fadeOut(200);
					}
				else {
					$(this).children('div').stop().fadeOut(200);
				};
			};
		};
		
	});
	
	initLeftMenu();
	
	$('.menu_header').on('click', function() {
		$(this).next('.menu_content').slideToggle(300,function(){
			saveLeftMenuState(this);
		});
		
	});
		
	$('.make_unvis_vis').on('click', function() {
		$(this).prev('.links_unvis').stop().slideToggle(200, function() {
			if ($(this).is(':hidden')) {
				$(this).next('.make_unvis_vis').html('<span>все гороскопы</span> ↓');
			} else {
				$(this).next('.make_unvis_vis').html('<span>свернуть</span> ↑');
			}
		});
	});
	
	$('.make_unvis_vis2').on('click', function() {
		$(this).prev('.links_unvis').stop().slideToggle(200, function() {
			if ($(this).is(':hidden')) {
				$(this).next('.make_unvis_vis2').html('<span>все мои числа</span> ↓');
			} else {
				$(this).next('.make_unvis_vis2').html('<span>свернуть</span> ↑');
			}
		});
	});
	
	$('.make_unvis_vis3').on('click', function() {
		$(this).prev('.links_unvis').stop().slideToggle(200, function() {
			if ($(this).is(':hidden')) {
				$(this).next('.make_unvis_vis3').html('<span>все расклады</span> ↓');
			} else {
				$(this).next('.make_unvis_vis3').html('<span>свернуть</span> ↑');
			}
		});
	});
	
	
	$('.why_need').on('click', function() {
	if ($('.detail').is(':hidden')) {
		$('.detail').slideDown(150);
		}
	else {
		$('.detail').slideUp(150);
		};		
	});
	$('.why_need, .detail').on('click', function(y) {
		y.stopImmediatePropagation();
	});
	$('#top, #content, #content_white, #footer, .windows_closw').on('click', function() {
	if ($('.detail').is(':hidden')) {
		}
	else {
		$('.detail').slideUp(150);
		};		
	});
	
	$('input:not(#input, .check, #poll, .sonnik_input, #word-search-button), textarea').on('focusin', function() {
		$(this).css({"background-color" : "#fffdd6"});
		//$(this).val();
	});
	$('input:not(#input, .check, #poll, .sonnik_input, #word-search-button), textarea').on('focusout', function() {
	        if($(this).val() == ''){
			$(this).css({"background-color" : "#fffdd6"});
		}else{
			//$(this).css({"background-color" : "#ffffff"});
		}
	});
	$('#comment_field,#payservice_comment_field').click(function(e){
		if($(this).val() == 'Ваш отзыв'){
			$(this).val('');
		}
	}).blur(function(){
		if($(this).val() == ''){
			$(this).val('Ваш отзыв');
		}
	});
	var day = 0;
	$('#day1').on('click', function() {
		if (day == 0) {
			}
		else {
			$('#day1').removeClass().addClass('moon_day_click_full');
			$('#day2').removeClass().addClass('moon_day_click').css({"margin-left" : "0"});
			$('#moon_half_day2').fadeOut(200, function() {
				$('#moon_half_day1').fadeIn(200);
				});
			day = 0;
			};
		});
	$('#day2').on('click', function() {
		if (day == 0) {
			$('#day1').removeClass().addClass('moon_day_click');
			$('#day2').removeClass().addClass('moon_day_click_full').css({"margin-left" : "10px"});
			day = 1;
			$('#moon_half_day1').fadeOut(200, function() {
				$('#moon_half_day2').fadeIn(200);
				});
			}
		else {
			};
	});
	
	function footerBottom() {
		var windowHeight = $(window).height();
		var whiteHeight = $('#white_space').height();
		var footerHeight = $('#footer').height();
		var topHeight = $('#top, #top_info').height();
		if(whiteHeight + footerHeight < windowHeight) {
			$('#white_space').height(windowHeight - topHeight - footerHeight)
		}
	}
	footerBottom();
	$(window).resize(function() {
		footerBottom();
	});
});

function initLeftMenu(){
	$('.menu_header').each(function(){
		var setting_name = $(this).attr('id');
		if($.cookie(setting_name) != null){
			if($.cookie(setting_name) == 'collapsed'){
				$(this).next().hide();
			}
		}
	});
}

function saveLeftMenuState(element){
	if($(element).is(':visible')){
		$.cookie($(element).prev().attr('id'),'expanded',{ expires: 7, path: '/' });
	}else{
		$.cookie($(element).prev().attr('id'),'collapsed',{ expires: 7, path: '/' });
	}
}