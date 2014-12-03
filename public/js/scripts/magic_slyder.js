$(document).ready(function() {
	var count = 0;
	var countTemp = 0;
	var kolvo = $('#magic_content > div.magic_item').length;
	$('#magic_content > div.magic_item').css({"visibility" : "hidden"});
	$('#magic_content > div.magic_item').first().css({"visibility" : "visible"}).next().css({"visibility" : "visible", "left" : "260px"}).next().css({"visibility" : "visible", "left" : "520px"});
	
	function left() {
		$('#magic_content > div.magic_item:eq(' + count + ')').stop().animate({left:"-260px"}, 500, function() {
			$(this).css({"visibility" : "hidden"});
			});
		count++;
		if (count == kolvo) {count = 0};
		$('#magic_content > div.magic_item:eq(' + count + ')').stop().animate({left:"0px"}, 500);
		countTemp = count + 1;
		if (countTemp == kolvo) {countTemp = 0};
		$('#magic_content > div.magic_item:eq(' + countTemp + ')').stop().animate({left:"260px"}, 500);
		countTemp++;
		if (countTemp == kolvo) {countTemp = 0};
		$('#magic_content > div.magic_item:eq(' + countTemp + ')').css({"visibility" : "visible", "left" : "780px"}).stop().animate({left:"520px"}, 500);
		};
	//ssetInterval(left, 8000);
	$('.magic_arrow_left').on('click', left);
	$('.magic_arrow_right').on('click', function() {
		$('#magic_content > div.magic_item:eq(' + count + ')').stop().animate({left:"260px"}, 500);
		countTemp = count + 1;
		if (countTemp == kolvo) {countTemp = 0};
		$('#magic_content > div.magic_item:eq(' + countTemp + ')').stop().animate({left:"520px"}, 500);
		countTemp++;
		if (countTemp == kolvo) {countTemp = 0};
		$('#magic_content > div.magic_item:eq(' + countTemp + ')').stop().animate({left:"780px"}, 500, function() {
			$(this).css({"visibility" : "hidden"});
			});
		count--;
		if (count == -1) {count = kolvo - 1};
		$('#magic_content > div.magic_item:eq(' + count + ')').css({"visibility" : "visible", "left" : "-260px"}).stop().animate({left:"0px"}, 500);
		});
});
		