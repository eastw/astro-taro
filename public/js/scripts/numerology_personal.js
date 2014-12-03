$(document).ready(function(){
		//loadDay();
		$('#byear,#bmonth').change(function(){
			loadDay();
		});
		$('#send-personal-data').click(function(){
			
			$('form div.wrong_text').remove();
			
			var year = $('#byear option:selected').val();
			if(year == ''){
				var text = $('#byear').prev().find('div.text').text();
				$('#byear option').each(function(){
					if($(this).text() == text){
						year = $(this).val();
					}
				});
			}
			
			var month = $('#bmonth option:selected').val();
			if(month == ''){
				var text = $('#bmonth').prev().find('div.text').text();
				$('#bmonth option').each(function(){
					if($(this).text() == text){
						month = $(this).val();
					}
				});
			}
			var day = $('#bday option:selected').val();
			if(day == '' || day == '1'){
				var text = $('#bday').prev().find('div.text').text();
				$('#bday option').each(function(){
					if($(this).text() == text){
						day = $(this).val();
					}
				});
			}
			
			var fname = $('#fname').val();
			var mname = $('#mname').val();
			var lname = $('#lname').val();
			
			var error = 'false';
			if(year == '' ){
				error = 'true';
			}
			if(month == '' ){
				error = 'true';
			}
			if(day == '' ){
				error = 'true';
			}
			if(fname == '' ){
				error = 'true';
			}
			if(mname == '' ){
				error = 'true';
			}
			if(lname == '' ){
				error = 'true';
			}
			if(error == 'false'){
				$.post('/numerology/get-description',
					{
						'byear'		: year,
						'bmonth'	: month,
						'bday'		: day,
						'smalltype'	: $('#smalltype').val(),
						'fname'		: fname,
						'mname'		: mname,
						'lname'		: lname,
					},
					function(data){
						//var html = data.description;
						$('.num_what').hide();
						$('#numerology-form').hide();
						$('#description_wrapper').html(data);
					});
			}else{
				//alert('Не все данные заполнены');
				$('#send-personal-data').before('<div class="wrong_text">Не все поля формы заполнены</div>');
			}
			return false;
		});
		
		function loadDay(){
			var year = $('#byear option:selected').val();
			var text = $('#byear').prev().find('div.text').text();
			$('#byear option').each(function(){
				if($(this).text() == text){
					year = $(this).val();
				}
			});
			
			var month = $('#bmonth option:selected').val();
			var text = $('#bmonth').prev().find('div.text').text();
			$('#bmonth option').each(function(){
				if($(this).text() == text){
					month = $(this).val();
				}
			});
			
			if(year != '' && month != ''){
				$.post(
						'/user/days',
						{
							'year' : year,
						 	'month': month
						 },
						 function(data){
							var html = '';
							for(var i in data){
								html += '<option value="'+i+'">' + data[i] + '</option>';
							}
							$('#bday').html(html);
							$('#bday').prev().remove();
							$('#bday').removeAttr('style');
							$('#bday').selectbox();
						},
						'json');
			}
		}
		
		
	});
function showForm(){
	$('.num_what').show();
	$('#numerology-form').show();
	
	$('.num_block').hide();
	$('.num_text_block').hide();
	
	$('html, body').animate({
        scrollTop: $("#numerology-form").offset().top
    }, 2000);
}