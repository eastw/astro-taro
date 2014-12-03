<?php
class App_PayserviceService{
	
	protected $theme;
	protected $gate;
	protected $email;
	
	const HOROSCOPE_TYPE = 'horoscope';
	const DIVINATION_TYPE = 'divination';
	
	public function __construct(){
		$this->gate = new Application_Model_DbTable_PaygateTable();
		$this->theme = new Application_Model_DbTable_PaythemeTable();
		$this->email = new Application_Model_DbTable_PayemailTable();
	}
	
	public function listThemes(){
		$query = $this->theme->select(false);
		$query->from('payservice_themes')->order('id desc');
		$stm = $query->query();
		return $stm->fetchAll();
	}
	
	public function addTheme($data){
		$insertData = array(
			'theme_type' => $data['type'],
			'theme_name' => $data['theme'],
			'theme_smalltype' => App_UtilsService::generateTranslit($data['theme']),
			'double_form' => $data['double_form'],
			'cost' => $data['cost'],
			'description' => $data['description'],
			'seo-title' => $data['seotitle'],
		 	'seo-keywords' => $data['seokeywords'],
			'seo-description' => $data['seodescription'],
			'image' => $data['image']
		);
		$this->theme->insert($insertData);
	}
	
	public function saveTheme($data,$id){
		$updateData = array(
			'theme_type' => $data['type'],
			'theme_name' => $data['theme'],
			'theme_smalltype' => App_UtilsService::generateTranslit($data['theme']),
			'double_form' => $data['double_form'],
			'cost' => $data['cost'],
			'description' => $data['description'],
			'seo-title' => $data['seotitle'],
		 	'seo-keywords' => $data['seokeywords'],
			'seo-description' => $data['seodescription'],
		);
		
		if(!empty($data['image'])){
			$updateData['image'] = $data['image'];
		}
		$this->theme->update($updateData,$this->theme->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function getThemeById($id){
		$theme = $this->theme->fetchRow($this->theme->getAdapter()->quoteInto('id=?',$id));
		return ($theme)?$theme->toArray():$theme;
	}
	
	public function deleteTheme($id){
		$this->theme->delete($this->theme->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function getThemesArray(){
		$query = $this->theme->select();
		$query->setIntegrityCheck(false);
		$query->from('payservice_themes','theme_smalltype')->order('id desc');
		$stm = $query->query();
		$data = $stm->fetchAll();
		$returnData = array();
		foreach($data as $key => $value){
			$returnData[] = $value['theme_smalltype'];
		}
		return $returnData; 
	}
	
	public function getThemeByAlias($theme){
		$data = $this->theme->fetchRow($this->theme->getAdapter()->quoteInto('theme_smalltype=?',$theme));
		if($data){
			$data = $data->toArray();
		}
		return $data;
	}
	
	public function getThemesByType($type){
		$query = $this->theme->select(false);
		$query->from('payservice_themes')
		->where($this->theme->getAdapter()->quoteInto('theme_type=?',$type))
		->order('id desc');
		$stm = $query->query();
		return $stm->fetchAll();
	}
	
	public function listGates(){
		$gates = $this->gate->fetchAll();
		return ($gates)?$gates->toArray():$gates;
	}
	
	public function getGateById($id){
		$gate = $this->gate->fetchRow($this->gate->getAdapter()->quoteInto('id=?',$id));
		return ($gate)?$gate->toArray():$gate;
	}
	
	public function addGate($data){
		$insertData = array(
			'gate' => $data['gate'],
			'details' => $data['details'],
		);
		$this->gate->insert($insertData);
	}
	
	public function saveGate($data,$id){
		$updateData = array(
			'gate' => $data['gate'],
			'details' => $data['details'],
		);
		$this->gate->update($updateData,$this->gate->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function deleteGate($id){
		$this->gate->delete($this->gate->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function getThemePrice($data){
		if(in_array($data['form_type'],array('horoscope','divination'))){
			$subtype = preg_replace('/<\/?[^>]+>/ims','',$data['alias']);
			$theme = $this->theme->fetchRow($this->theme->getAdapter()->quoteInto('theme_smalltype=?', $subtype));
			$theme = ($theme)?$theme->toArray():$theme;
			return array('summ' => ($theme)?$theme['cost']:0); 
		}
		return array('summ' => 0);
	}
	
	public function getGateDetails($data){
		$gates = $this->listGates();
		foreach ($gates as $gate){
			if($gate['gate'] == $data['payment_type']){
				return $gate['details'];
			}
		}
		 return '';
	}
	
	public static function getPayTypes(){
		return array(
			self::HOROSCOPE_TYPE => 'Персональный гороскоп',
			self::DIVINATION_TYPE => 'Персональный расклад на Таро',
		);
	}
	
	public function getEmail(){
		$email = $this->email->fetchRow('id=1');
		return ($email)?$email->toArray():$email;
	}
	
	public function saveEmail($data,$id){
		$updateData = array(
			'email' => $data['email'],
		);
		$this->email->update($updateData,$this->email->getAdapter()->quoteInto('id=?', $id));
	}
	
}