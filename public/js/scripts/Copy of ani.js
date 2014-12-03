$(document).ready(function() {	
	$('#podstava').on('click', function() {
		var podstavaText = $(this).text();
		$('#input').attr('value', podstavaText);
	});
	
	$('#enter, .profile_block').on('click', function() {
		$('#enter_form').stop().fadeToggle(200);
	});
	$('#ident-enter').click(function(){
		$('#enter').click();
		//$('#enter').click();
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
	
	$('.banner_top_970x90').on('click', function() {
		$('#popup').css({"display" : "block"});
		});
	$('#popup, .popup_pult_item2, .close').on('click', function() {
		$('#popup').css({"display" : "none"});
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
	
	$('.popup_vip').on('click', function() {
		var param = $('.popup_vip .selected').text();
		if (param == "Персональный гороскоп") {
			$('.popup_theme').css({"display" : "block"});
			$('.popup_theme2').css({"display" : "none"});
			}
		else {
			$('.popup_theme').css({"display" : "none"});
			$('.popup_theme2').css({"display" : "block"});
			};
		});
	
	$('.popup_enter').on('click', function() {
		$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
		var param = $('.popup_vip .selected').text();
		if (param == "Персональный гороскоп") {
			var param2 = $('.popup_theme .selected').text();
			}
		else {
			var param2 = $('.popup_theme2 .selected').text();
			};
		if (param2 == "Гороскоп на год" || param2 == "Общий гороскоп" || param2 == "Гороскоп здоровья" || param2 == "Гороскоп ребенка" || param2 == "Дата свадьбы" || param2 == "Бизнес гороскоп" || param2 == "Кармический гороскоп") {
			$('#popup_info').children('#list1').fadeOut(200, function() {
				$('#popup_info').children('#list2').fadeIn(200);
				$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
				});
			}
		else {
			if (param2 == "Гороскоп любовной совместимости" || param2 == "Гороскоп партнерской совместимости") {
				$('#popup_info').children('#list1').fadeOut(200, function() {
					$('#popup_info').children('#list3').fadeIn(200);
					$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
					});
				}
			else {
				$('#popup_info').children('#list1').fadeOut(200, function() {
					$('#popup_info').children('#list4').fadeIn(200);
					$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
					});
				};
			};
		});
		
	var memory = 0;	
	$('.popup_enter2').on('click', function() {
		memory = 2;
		$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
		$('#popup_info').children('#list2').fadeOut(200, function() {
			$('#popup_info').children('#list5').fadeIn(200);
			$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
		});
	});
	
	$('.popup_enter3').on('click', function() {
		memory = 3;
		$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
		$('#popup_info').children('#list3').fadeOut(200, function() {
			$('#popup_info').children('#list5').fadeIn(200);
			$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
		});
	});
	
	$('.popup_enter4').on('click', function() {
		memory = 4;
		$('#popup_nav').children('.popup_nav_item').first().removeClass('popup_nav_item').addClass('popup_nav_item_active');
		$('#popup_info').children('#list4').fadeOut(200, function() {
			$('#popup_info').children('#list5').fadeIn(200);
			$('#popup_nav').children('.popup_nav_item_active').first().removeClass('popup_nav_item_active').addClass('popup_nav_item2_active');
		});
	});
	
	$('.popup_enter5').on('click', function() {
		$('#popup_content').fadeOut(200, function() {
			$('#popup_final').fadeIn(200);
		});
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
		
	var signNum = 0;
	var signTemp = 0;
	$('.sign_small_item').eq(signNum).css({"background" : "url(/files/images/horoscope_sign_back.png)"});
	$('.sign_item').eq(signNum).css({"display" : "block"});
	$('.sign_small_item').on('click', function() {
		signTemp = signNum;
		signNum = $(this).index();
		if (signNum == signTemp) {}
		else {
			$('.sign_small_item').eq(signTemp).css({"background" : "none"});
			$(this).css({"background" : "url(files/images/horoscope_sign_back.png)"});
			$('.sign_item').eq(signTemp).fadeOut(200, function() {
				$('.sign_item').eq(signNum).fadeIn(200);
			});
		};
	});
	
	$('.sign_small_item').on('mouseover', function() {
		if (signNum == $(this).index()) {}
		else {
			$(this).css({"background" : "url(files/images/horoscope_sign_back.png)"});
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
		$('.sign_small_item').eq(signNum).css({"background" : "url(files/images/horoscope_sign_back.png)"});
		$('.sign_item').eq(signTemp).fadeOut(200, function() {
			$('.sign_item').eq(signNum).fadeIn(200);
		});
	});
	
	$('#horo_arrow div.arrow_left').on('click', function() {
		signTemp = signNum;
		signNum--;
		if (signNum <= -1) {signNum = 11}
		$('.sign_small_item').eq(signTemp).css({"background" : "none"});
		$('.sign_small_item').eq(signNum).css({"background" : "url(files/images/horoscope_sign_back.png)"});
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
		window.location = $(this).find('a').attr('href');
	});
	/*
	$('#feedback form input:eq(1)').click(function(){
		if($(this).val() == 'используйте действующий адрес'){
			$(this).val('');
		}
	}).blur(function(){
		if($(this).val() == ''){
			$(this).val('используйте действующий адрес');
		}
	});
	*/
	
	var valueTemp = '';
	$('input').on('focusin', function() {
		valueTemp = $(this).attr('value');
		if(valueTemp != ''){
			$(this).attr('value', '');
		}
	});
	$('input').on('focusout', function() {
		if (valueTemp = 'undifined' ) {
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
	
	$('.lilac_item').on('mouseenter', function() {
		if ($('#top').next().attr('id') == "content") {
			$(this).addClass('corner_vip').children('div').stop().fadeIn(200);
		}
		else {
			if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
				$(this).addClass('corner_vip').children('div').stop().fadeIn(200);
				}
			else {
				$(this).addClass('corner_vip_white').children('div').stop().fadeIn(200);
			};
		};
	});
	$('.lilac_item').on('mouseleave', function() {
		if ($('#top').next().attr('id') == "content") {
			$(this).removeClass('corner_vip').children('div').stop().fadeOut(200);
		}
		else {
			if ($(this).index() == 0 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 1 && $(this).parent().attr('id') == "menu_left" || $(this).index() == 2 && $(this).parent().attr('id') == "menu_left") {
				$(this).removeClass('corner_vip').children('div').stop().fadeOut(200);
				}
			else {
				$(this).removeClass('corner_vip_white').children('div').stop().fadeOut(200);
			};
		};
	});
		
	$('.menu_header').on('click', function() {
		$(this).next('.menu_content').slideToggle(300);
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
	
	$('input:not(#input, .check), textarea').on('focusin', function() {
		$(this).css({"background-color" : "#fffdd6"});
	});
	$('input:not(#input, .check), textarea').on('focusout', function() {
		if($(this).val() == ''){
			$(this).css({"background-color" : "#fffdd6"});
		}else{
			$(this).css({"background-color" : "#ffffff"});
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
});