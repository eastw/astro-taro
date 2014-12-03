$(document).ready(function(){
	$('#month-year').change(function(){
		var text = $('#month-year').prev().find('div.text').text();
		$('#month-year option').each(function(){
			if($(this).text() == text){
				monthYear = $(this).val();
			}
		});
		
		if(monthYear != ''){
			$.post(
				'/moon/get-days',
				{
					'month-year' : monthYear  
				},
				function(data){
					$('#month-data').html(data);
				});
		}
	});
});