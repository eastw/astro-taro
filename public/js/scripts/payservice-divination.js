$(document).ready(function(){
	$('#year,#month').change(function(){
		var byear = $('#year option:selected').val();
		var text = $('#year').prev().find('div.text').text();
		$('#year option').each(function(){
			if($(this).text() == text){
				byear = $(this).val();
			}
		});
		var bmonth = $('#month option:selected').val();
		var text = $('#month').prev().find('div.text').text();
		$('#month option').each(function(){
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
						$('#day').html(html);
						$('#day').prev().remove();
						$('#day').removeAttr('style');
						$('#day').selectbox();
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
		
		var byear = $('#year option:selected').val();
		var text = $('#year').prev().find('div.text').text();
		$('#year option').each(function(){
			if($(this).text() == text){
				formData['year'] = $(this).val();
			}
		});
		
		var bmonth = $('#month option:selected').val();
		var text = $('#month').prev().find('div.text').text();
		$('#month option').each(function(){
			if($(this).text() == text){
				formData['month'] = $(this).val();
			}
		});
		var bday = $('#day option:selected').val();
		var text = $('#day').prev().find('div.text').text();
		$('#day option').each(function(){
			if($(this).text() == text){
				formData['day'] = $(this).val();
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
						//console.log('valid!!');
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
	//$('#auth-form').serialize(),
});