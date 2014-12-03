$(document).ready(function(){
		//loadDay();
		$('#byear,#bmonth').change(function(){
			loadDay();
		});
		$('#send-data').click(function(){
			
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
			if(error == 'false'){
				$('#errors').html('');
				$.post('/horoscope/get-karma-description',
					{
						'byear'		: year,
						'bmonth'	: month,
						'bday'		: day
					},
					function(data){
						var html = data.description;
						//$('#description_wrapper').html(html);
						//console.log(data.description);
						$('#sign-image').attr('src','/files/images/profile/karma/'+data.sign+'.png');
						$('.my_cab_item').find('.item_header').text(data.sign_ru);
						$('#data-text').text('На момент вашего рождения Сатурн был в знаке ' + data.sign_ru + ((data.is_retrograd == 'y')?' (ретроградный период)':''));
						var description = data.description;
						$('#karma-desc').html(description);
						//description += '<a id="another" class="another" onclick="another_karma()" style="cursor:pointer">Проверить другую дату →</a>';
						//description += '<div class="clear"></div>';
						//$('#answer').html(description);
						$('#partner2').hide();
						$('.comp_answer').show();
						
				},'json');
			}else{
				$('#errors').html('Не все поля заполнены');
			}
			return false;
		});
		
		function loadDay(){
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
function another_karma(){
	$('.comp_answer').hide();
	$('#partner2').show();
	$('html, body').animate({
        scrollTop: $("#partner2").offset().top
    }, 2000);
}