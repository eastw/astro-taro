<?php
class App_MailService{
	
	protected $config;
	
	protected $transport;
	
	protected $mail;
	
	public function __construct(){
		$this->config = array(
					    'ssl' => 'tls',
					    'port' => 587,
					    //'port' => 465,
					    'auth' => 'login',
					    //'username' => 'eastwarrior@gmail.com',
					    'username' => 'info.astrotarot@gmail.com',
					    //'username' => 'zakaz@astrotarot.ru',
						//'password' => '123456'
					   	//'password' => 'rhbgnjutybrf'
					    'password' => '22012315tsirus'
					    
				);
		$this->transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $this->config);
		//$this->transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $this->config);
	}
	
	public function sendRegistrationMail($data){
		/*
		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyText('Спасибо за регистрацию на портале Astrotarot, данные вашего аккаунта:
				логин: '.$data['email'].'
				пароль: '.$data['pass'].' ');
		$mail->setFrom('noreply@astrotarot.ru', 'AstroTarot');
		$mail->addTo($data['email'], '');
		$mail->setSubject('Данные акаунта');
		$mail->send($this->transport);
		*/
		$subject = 'Данные акаунта';
		
		$text = array();
		$text[] = 'Спасибо за регистрацию на портале Astrotarot, данные вашего аккаунта:';
		$text[] = 'логин: '.$data['email'];
		$text[] = 'пароль: '.$data['pass'];
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: noreply@astrotarot.ru\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
		mail($data['email'],$subject,implode("<br/>",$text),$headers);
	}
	
	public function sendFeedback($data){
		
		$subject = 'Feedback';
		
		$text = array();
		$text[] = 'Запрос на обратную связь';
		$text[] = 'Пользователь '.$data['name'].' с электронной почтой '.$data['email'].' пишет следующее:';
		$text[] = $data['content'];
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: webmaster@astrotarot.ru\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
		mail('info.astrotarot@gmail.com',$subject,implode("<br/>",$text),$headers);
		//mail('info@astrotarot.ru',$subject,implode("<br/>",$text),$headers);
		//info.astrotarot@gmail.com
		 
		/*
		$mail = new Zend_Mail('UTF-8');
		
		$text = array();
		$text[] = 'Запрос на обратную связь';
		$text[] = 'Пользователь '.$data['name'].' с электронной почтой '.$data['email'].' пишет следующее:';
		$text[] = $data['content'];
		
		$mail->setBodyText(implode("\n",$text));
		$mail->setFrom('noreply@astrotarot.ru', 'AstroTarot');
		$mail->addTo('info.astrotarot@gmail.com', '');
		$mail->setSubject('Feedback');
		$mail->send($this->transport);
		*/
	}
	
	public function sendTypo($data){
		
		$subject = 'Опечатка на сайте Astrotarot.ru';
		
		$text = array();
		$text[] = 'На странице '.$data['location'];
		$text[] = ' найдена опечатка в тексте: ';
		$text[] = '"'.$data['typo'].'"';
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: webmaster@astrotarot.ru\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
		mail('info.astrotarot@gmail.com',$subject,implode("<br/>",$text),$headers);
		//mail('info@astrotarot.ru',$subject,implode("<br/>",$text),$headers);
		//mail('east_bbk@inbox.ru',$subject,implode("<br/>",$text),$headers);
		//info.astrotarot@gmail.com
		 
		/*
		$mail = new Zend_Mail('UTF-8');
		
		$text = array();
		$text[] = 'На странице '.$data['location'];
		$text[] = ' найдена опечатка в тексте: ';
		$text[] = '"'.$data['typo'].'"';
		
		$mail->setBodyText(implode("\n",$text));
		$mail->setFrom('noreply@astrotarot.ru', 'AstroTarot');
		$mail->addTo('info@astrotarot.ru', '');
		$mail->setSubject('Опечатка на сайте Astrotarot.ru');
		$mail->send($this->transport);
		*/
	}
	
	public function sendPayServiceMail($data,$theme,$email){
		$subject = '';
		$content = array();
		
		if($theme['theme_type'] == 'horoscope'){
			if($theme['double_form'] == 'y'){
				$subject = 'Заказ индивидуального гороскопа, тип гороскопа - "'.$theme['theme_name'].'"';
				$content[] = '<b>На сайте сделан заказ индивидуального гороскопа, тип гороскопа "'.$theme['theme_name'].'"</b>';
				$content[] = '<b>Данные клиента</b>:';
				
				$content[] = '<b>Полное имя клиента</b>:'.$data['name1'];
				$content[] = '<b>Город клиента</b>:'.$data['city1'];
				$date = new Zend_Date($data['year1'].'-'.$data['month1'].'-'.$data['day1']);
				$content[] = '<b>Дата рождения клиента</b>:'.$date->toString(Zend_Date::DATE_LONG);
				if(isset($data['dontknow1']) && $data['dontknow1'] == '1'){
					$content[]= '<b>Время рождения</b>: не известно';
				}elseif(isset($data['dontknow1']) && $data['dontknow1'] == ''){
					$content[]= '<b>Время рождения</b>: '.$data['hour1'].':'.$data['minute1'];
				}
				
				//partner
				$content []= '<b>Полное имя партнера клиента</b>:'.$data['name2'];
				$content []= '<b>Город партнера</b>:'.$data['city2'];
				$date = new Zend_Date($data['year2'].'-'.$data['month2'].'-'.$data['day2']);
				$content []= '<b>Дата рождения клиента</b>:'.$date->toString(Zend_Date::DATE_LONG);
				if(isset($data['dontknow2']) && $data['dontknow2'] == '1'){
					$content []= '<b>Время рождения</b>: не известно';
				}elseif(isset($data['dontknow2']) && $data['dontknow2'] == ''){
					$content []= '<b>Время рождения</b>: '.$data['hour2'].':'.$data['minute2'];
				}
				
			}else{
				$subject = 'Заказ индивидуального гороскопа, тип гороскопа - "'.$theme['theme_name'].'"';
				$content[] = '<b>На сайте сделан заказ индивидуального гороскопа, тип гороскопа "'.$theme['theme_name'].'"</b>';
				$content[] = '<b>Данные клиента</b>:';
				
				$content[] = '<b>Полное имя клиента</b>:'.$data['name'];
				$content[] = '<b>Город клиента</b>:'.$data['city'];
				$date = new Zend_Date($data['year'].'-'.$data['month'].'-'.$data['day']);
				$content[] = '<b>Дата рождения клиента</b>:'.$date->toString(Zend_Date::DATE_LONG);
				if(isset($data['dontknow']) && $data['dontknow'] == '1'){
					$content[]= '<b>Время рождения</b>: не известно';
				}elseif(isset($data['dontknow']) && $data['dontknow'] == ''){
					$content[]= '<b>Время рождения</b>: '.$data['hour'].':'.$data['minute'];
				}
			}
		}
		if($theme['theme_type'] == 'divination'){
			$subject = 'Заказ индивидуального гадания, тип гадания - "'.$theme['theme_name'].'"';
			$content[] = '<b>На сайте сделан заказ индивидуального гадания, тип гадания "'.$theme['theme_name'].'"</b>';
			
			$content[]= '<b>Полное имя клиента</b>:'.$data['name'];
			//$content[]= '<b>Город клиента</b>:'.$data['city1'];
			$date = new Zend_Date($data['year'].'-'.$data['month'].'-'.$data['day']);
			$content []= '<b>Дата рождения клиента</b>:'.$date->toString(Zend_Date::DATE_LONG);
		}
		
		$content []= '<b>Почта клиента</b>:'.$data['email'];
		$content []= '<b>Выбранная платежная система</b>:'.$data['payment_type'];
		if($theme['theme_type'] == 'horoscope'){
			$content []= '<b>Комментарий к заказу</b>:'.$data['detail'];
		}
		if($theme['theme_type'] == 'divination'){
			$content []= '<b>Вопрос клиента</b>: '.$data['quest'];
		}
		$content[] = '<b>Стоимость заказа:</b>'.$theme['cost'];
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
		$headers .= 'From: webmaster@astrotarot.ru' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
		
		//var_dump($email['email']); die;
		mail($email['email'],$subject,implode("<br/>",$content),$headers);
		//mail('zakaz@astrotarot.ru',$subject,implode("<br/>",$content),$headers);
		//mail('east_bbk@inbox.ru',$subject,implode("<br/>",$content),$headers);
		
		$payService = new App_PayserviceService();
		$price = $payService->getThemePrice($data);
		$details = $payService->getGateDetails($data);
		
		$mailer = new App_HtmlMailer();
		$mailer->setSubject($subject)
				->setViewParam('orderType',$theme['theme_type'])
				->setViewParam('orderDetailName',$theme['theme_name'])
				->setViewParam('email',$data['email'])
				->setViewParam('details',$details)
				->setViewParam('price',$price['summ'])
				->addTo($data['email']);
				
		if($theme['theme_type'] == 'horoscope' &&  $theme['double_form'] == 'y'){
			$mailer->setViewParam('name',$data['name1']);	
		}else{
			$mailer->setViewParam('name',$data['name']);
		}
		$mailer->sendHtmlTemplate('order.phtml');
		//$mailer = new App_HtmlMailer();
		//$mailer->setSubject()->addTo($data['email'])->sendHtmlTemplate('order.phtml');
		//mail($adminMail,$subject,implode("<br/>",$content),$headers);
	}
	
	
	
	public function testMail(){
		mail('east_bbk@inbox.ru','test subject','hello world');
	}
	
}