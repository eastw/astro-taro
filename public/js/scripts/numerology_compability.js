$(document).ready(function(){
		loadDay2();
		$('#byear1,#bmonth1').change(function(){
			loadDay1();
		});
		$('#byear2,#bmonth2').change(function(){
			loadDay2();
		});
		$('#send-personal-data').click(function(){
			$('form div.wrong_text').remove();
			var year1 = $('#byear1 option:selected').val();
			if(year1 == ''){
				var text = $('#byear1').prev().find('div.text').text();
				$('#byear1 option').each(function(){
					if($(this).text() == text){
						year1 = $(this).val();
					}
				});
			}
			var year2 = $('#byear2 option:selected').val();
			if(year2 == ''){
				var text = $('#byear2').prev().find('div.text').text();
				$('#byear2 option').each(function(){
					if($(this).text() == text){
						year2 = $(this).val();
					}
				});
			}
			var month1 = $('#bmonth1 option:selected').val();
			if(month1 == ''){
				var text = $('#bmonth1').prev().find('div.text').text();
				$('#bmonth1 option').each(function(){
					if($(this).text() == text){
						month1 = $(this).val();
					}
				});
			}
			var month2 = $('#bmonth2 option:selected').val();
			if(month2 == ''){
				var text = $('#bmonth2').prev().find('div.text').text();
				$('#bmonth2 option').each(function(){
					if($(this).text() == text){
						month2 = $(this).val();
					}
				});
			}
			var day1 = $('#bday1 option:selected').val();
			if(day1 == '' || day1 == '1'){
				var text = $('#bday1').prev().find('div.text').text();
				$('#bday1 option').each(function(){
					if($(this).text() == text){
						day1 = $(this).val();
					}
				});
			}
			var day2 = $('#bday2 option:selected').val();
			if(day2 == '' || day2 == '1'){
				var text = $('#bday2').prev().find('div.text').text();
				$('#bday2 option').each(function(){
					if($(this).text() == text){
						day2 = $(this).val();
					}
				});
			}
			
			var fname1 = $('#fname1').val();
			var mname1 = $('#mname1').val();
			var lname1 = $('#lname1').val();
			
			var fname2 = $('#fname2').val();
			var mname2 = $('#mname2').val();
			var lname2 = $('#lname2').val();
			
			var error = 'false';
			if(year1 == '' ){
				error = 'true';
			}
			if(year2 == '' ){
				error = 'true';
			}
			if(month1 == '' ){
				error = 'true';
			}
			if(month2 == '' ){
				error = 'true';
			}
			if(day1 == '' ){
				error = 'true';
			}
			if(day2 == '' ){
				error = 'true';
			}
			if(fname1 == '' ){
				error = 'true';
			}
			if(fname2 == '' ){
				error = 'true';
			}
			if(mname1 == '' ){
				error = 'true';
			}
			if(mname2 == '' ){
				error = 'true';
			}
			if(lname1 == '' ){
				error = 'true';
			}
			if(lname2 == '' ){
				error = 'true';
			}
			if(error == 'false'){
				$.post('/numerology/get-description',
					{
						'byear1'		: year1,
						'byear2'		: year2,
						'bmonth1'		: month1,
						'bmonth2'		: month2,
						'bday1'			: day1,
						'bday2'			: day2,
						'smalltype'		: $('#smalltype').val(),
						'fname1'		: fname1,
						'mname1'		: mname1,
						'lname1'		: lname1,
						'fname2'		: fname2,
						'mname2'		: mname2,
						'lname2'		: lname2,
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
		
		function loadDay1(){
			var year1 = $('#byear1 option:selected').val();
			if(year1 == ''){
				var text = $('#byear1').prev().find('div.text').text();
				$('#byear1 option').each(function(){
					if($(this).text() == text){
						year1 = $(this).val();
					}
				});
			}
			
			var month1 = $('#bmonth1 option:selected').val();
			if(month1 == ''){
				var text = $('#bmonth1').prev().find('div.text').text();
				$('#bmonth1 option').each(function(){
					if($(this).text() == text){
						month1 = $(this).val();
					}
				});
			}
			if(year1 != '' && month1 != ''){
				$.post(
						'/user/days',
						{
							'year' : year1,
						 	'month': month1
						 },
						 function(data){
							var html = '';
							for(var i in data){
								html += '<option value="'+i+'">' + data[i] + '</option>';
							}
							$('#bday1').html(html);
							$('#bday1').prev().remove();
							$('#bday1').removeAttr('style');
							$('#bday1').selectbox();
						},
						'json');
			}
		}
		
		function loadDay2(){
			var year2 = $('#byear2 option:selected').val();
			if(year2 == ''){
				var text = $('#byear2').prev().find('div.text').text();
				$('#byear2 option').each(function(){
					if($(this).text() == text){
						year2 = $(this).val();
					}
				});
			}
			var month2 = $('#bmonth2 option:selected').val();
			if(month2 == ''){
				var text = $('#bmonth2').prev().find('div.text').text();
				$('#bmonth2 option').each(function(){
					if($(this).text() == text){
						month2 = $(this).val();
					}
				});
			}
			if(year2 != '' && month2 != ''){
				$.post(
						'/user/days',
						{
							'year' : year2,
						 	'month': month2
						 },
						 function(data){
							var html = '';
							for(var i in data){
								html += '<option value="'+i+'">' + data[i] + '</option>';
							}
							$('#bday2').html(html);
							$('#bday2').prev().remove();
							$('#bday2').removeAttr('style');
							$('#bday2').selectbox();
						},
						'json');
			}
		}
	});
function showForm(){
	$('.num_what').show();
	$('#numerology-form').show();
	
	$('#total-description').hide();
	
	$('html, body').animate({
        scrollTop: $("#numerology-form").offset().top
    }, 2000);
}