$(document).ready(function() {
	var num = 1;
	$('#slyder > div').addClass('invis');
	$('#slyde' + num).removeClass().addClass('vis');
	$('div[num = ' + num +']').removeClass('slyder_dot').addClass('slyder_dot_on');
    setInterval(function() {
		num++;
		if (num > $('#slyder > div').length) {num = 1;}
		$('#slyder_element > div').removeClass('slyder_dot_on').addClass('slyder_dot');
		$('div[num = ' + num +']').removeClass('slyder_dot').addClass('slyder_dot_on');
		$('.vis').removeClass().addClass('vis2').stop().animate({"left" : "-1016px"}, 800, function() {
			$('.vis2').removeClass().addClass('invis').css({"left" : "0"});
			});
		$('#slyde' + num).css({"left" : "1016px"}).animate({"left" : "0px"}, 800).removeClass().addClass('vis');
		}, 16000);
	$('#slyder_element > div').on('click', function() {
		if ($(this).attr('num') != num) {
			num = $(this).attr('num');
			console.log(num);
			$('#slyder_element > div').removeClass('slyder_dot_on').addClass('slyder_dot');
			$('div[num = ' + num +']').removeClass('slyder_dot').addClass('slyder_dot_on');
			$('.vis').removeClass().addClass('vis2').stop().animate({"left" : "-1016px"}, 800, function() {
				$('.vis2').removeClass().addClass('invis').css({"left" : "0"});
				});
			$('#slyde' + num).css({"left" : "1016px"}).animate({"left" : "0px"}, 800).removeClass().addClass('vis');
		};
	});
});