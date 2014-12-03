$(document).ready(function(){
	
	
	
	$('#year1,#month1').change(function(){
		var byear = $('#year1 option:selected').val();
		var text = $('#year1').prev().find('div.text').text();
		$('#year1 option').each(function(){
			if($(this).text() == text){
				byear = $(this).val();
			}
		});
		var bmonth = $('#month1 option:selected').val();
		var text = $('#month1').prev().find('div.text').text();
		$('#month1 option').each(function(){
			if($(this).text() == text){
				bmonth = $(this).val();
			}
		});
		if(byear != '' && bmonth != ''){
			$.post(
					'/user/days',
					{
						'year' : byear,
					 	'month': bmonth
					 },
					 function(data){
						var html = '';
						for(var i in data){
							html += '<option value="'+i+'">' + data[i] + '</option>';
						}
						$('#day1').html(html);
						$('#day1').prev().remove();
						$('#day1').removeAttr('style');
						$('#day1').selectbox();
					},
					'json');
		}
	});
	
	$('#year2,#month2').change(function(){
		var byear = $('#year2 option:selected').val();
		var text = $('#year2').prev().find('div.text').text();
		$('#year2 option').each(function(){
			if($(this).text() == text){
				byear = $(this).val();
			}
		});
		var bmonth = $('#month2 option:selected').val();
		var text = $('#month2').prev().find('div.text').text();
		$('#month2 option').each(function(){
			if($(this).text() == text){
				bmonth = $(this).val();
			}
		});
		if(byear != '' && bmonth != ''){
			$.post(
					'/user/days',
					{
						'year' : byear,
					 	'month': bmonth
					 },
					 function(data){
						var html = '';
						for(var i in data){
							html += '<option value="'+i+'">' + data[i] + '</option>';
						}
						$('#day2').html(html);
						$('#day2').prev().remove();
						$('#day2').removeAttr('style');
						$('#day2').selectbox();
					},
					'json');
		}
	});
	
	$('input[type="checkbox"]').change(function(){
		if($(this).is(':checked')){
			$(this).attr('value','1');
		}else{
			$(this).attr('value','');
		}
	});
	
	$('#submit').click(function(){
		
var formData = $('#full').serializeObject();
		
		var byear1 = $('#year1 option:selected').val();
		var text = $('#year1').prev().find('div.text').text();
		$('#year1 option').each(function(){
			if($(this).text() == text){
				formData['year1'] = $(this).val();
			}
		});
		var byear2 = $('#year2 option:selected').val();
		var text = $('#year2').prev().find('div.text').text();
		$('#year2 option').each(function(){
			if($(this).text() == text){
				formData['year2'] = $(this).val();
			}
		});
		
		var bmonth1 = $('#month1 option:selected').val();
		var text = $('#month1').prev().find('div.text').text();
		$('#month1 option').each(function(){
			if($(this).text() == text){
				formData['month1'] = $(this).val();
			}
		});
		
		var bmonth2 = $('#month2 option:selected').val();
		var text = $('#month2').prev().find('div.text').text();
		$('#month2 option').each(function(){
			if($(this).text() == text){
				formData['month2'] = $(this).val();
			}
		});
		
		var bday1 = $('#day1 option:selected').val();
		var text = $('#day1').prev().find('div.text').text();
		$('#day1 option').each(function(){
			if($(this).text() == text){
				formData['day1'] = $(this).val();
			}
		});
		
		var bday2 = $('#day2 option:selected').val();
		var text = $('#day2').prev().find('div.text').text();
		$('#day2 option').each(function(){
			if($(this).text() == text){
				formData['day2'] = $(this).val();
			}
		});
		
		var bhour = $('#hour1 option:selected').val();
		var text = $('#hour1').prev().find('div.text').text();
		$('#hour1 option').each(function(){
			if($(this).text() == text){
				formData['hour1'] = $(this).val();
			}
		});
		
		var bhour2 = $('#hour2 option:selected').val();
		var text = $('#hour2').prev().find('div.text').text();
		$('#hour2 option').each(function(){
			if($(this).text() == text){
				formData['hour2'] = $(this).val();
			}
		});
		
		var bminute1 = $('#minute1 option:selected').val();
		var text = $('#minute1').prev().find('div.text').text();
		$('#minute1 option').each(function(){
			if($(this).text() == text){
				formData['minute1'] = $(this).val();
			}
		});
		
		var bminute2 = $('#minute2 option:selected').val();
		var text = $('#minute2').prev().find('div.text').text();
		$('#minute2 option').each(function(){
			if($(this).text() == text){
				formData['minute2'] = $(this).val();
			}
		});
		
		var ptype = $('#payment_type option:selected').val();
		var text = $('#payment_type').prev().find('div.text').text();
		$('#payment_type option').each(function(){
			if($(this).text() == text){
				formData['payment_type'] = $(this).val();
			}
		});
		
		$.post(
				'/service/order',
				formData,
				function(data){
					$('div.wrong_text').remove();
					if(data.length == 0){
						$('#fill-form').text('Ваш заказ принят');
						$('#full').remove();
						$('#order-info').show();
						$('html, body').animate({
					        scrollTop: $("#fill-form").offset().top
					    }, 2000);
					}else{
						var html = '';
						for(var i in data){
							html = '';
							for(var j in data[i]){
								var style = '';
								html += '<div '+style+' class="wrong_text">' + data[i][j] + '</div>';
							}
							if(i == 'agree'){
								$('#'+i).next().after(html);
							}else{
								$('#'+i).after(html);
							}
						}
					}
				},'json');
		return false;
	});
});