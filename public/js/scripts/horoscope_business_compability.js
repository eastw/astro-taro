var global_signs = {
				'1':'aries','2':'taurus','3':'gemini','4':'cancer',
				'5':'leo','6':'virgo','7':'libra','8':'scorpio',
				'9':'sagittarius','10':'capricorn','11':'aquarius','12':'pisces'};

$(document).ready(function(){
		$('#check').click(function(){
			var yourSign = $('#your_sign option:selected').val();
			var text = $('#your_sign').prev().find('div.text').text();
			$('#your_sign option').each(function(){
				if($(this).text() == text){
					yourSign = $(this).val();
				}
			});
			
			var partnerSign = $('#partner_sign option:selected').val();
			var text = $('#partner_sign').prev().find('div.text').text();
			$('#partner_sign option').each(function(){
				if($(this).text() == text){
					partnerSign = $(this).val();
				}
			});
			
			if(yourSign != '' && partnerSign != ''){
				$.post(
					'/horoscope/get-compability',
					{
						'sign1': yourSign,
						'sign2': partnerSign,
						'type': 'business',
					},
					function(data){
						var attributes = '';
						for(var i in data.attributes){
							for(var j in data.attribute_values){
								if(data.attributes[i]['id'] == data.attribute_values[j]['compability_attribute_id']){
									attributes += '<div class="parametrs_item"><span>'+data.attributes[i]['name_ru']+'</span>';
									attributes += ' - '+data.attribute_values[j]['value'];
									attributes += '</div>';
								}
							}
						}
						attributes += '<div class="clear"></div>';
						
						var description = data.description;
						description += '<br />';
						description += '<a id="another" class="another" onclick="another_compability()" style="cursor:pointer">Проверить другую совместимость →</a>';
						description += '<div class="clear"></div>';
						$('#parametrs').html(attributes);
						$('#answer').html(description);
						$('#my-sign').attr('src',$('#your_sign').parent().find('img').attr('src'));
						$('#partner-sign').attr('src',$('#partner_sign').parent().find('img').attr('src'));
						$('#partner').hide();
						$('#comp_answer').show();
					},'json');
			}
			return false;
		});
		
		$('#your_sign,#partner_sign').change(function(){
			setSignImage($(this).attr('id'));
		});
		
		
		
	});
	function another_compability(){
		$('#comp_answer').hide();
		$('#partner').show();
		$('html, body').animate({
	        scrollTop: $("#partner").offset().top
	    }, 2000);
	}
	function setSignImage(id){
		var sign = '';
		var text = $('#' + id).prev().find('div.text').text();
		$('#'+id+' option').each(function(){
			if($(this).text() == text){
				sign = $(this).val();
			}
		});
		for(var i in global_signs){
			if(i == sign){
				$('#'+id).parent().find('img').attr('style','width:60px');
				$('#'+id).parent().find('img').attr('src','/files/images/profile/sun/' + global_signs[i] + '.png');
			}
		}
	}

	function restart(){
		$('#result-wrapper').hide();
		$('#love-form').show();
	}
	
	