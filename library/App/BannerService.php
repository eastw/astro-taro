<?php
class App_BannerService{
	
	protected $banner;
	protected $bannerPosition;
	
	const BANNER_TYPE_SLIDER = 's';
	const BANNER_TYPE_ADS = 'a';
	
	public function __construct(){
		$this->banner = new Application_Model_DbTable_BannerTable();
		$this->bannerPosition = new Application_Model_DbTable_BannerPositionTable();
	}
	
	public function buildBannerQuery($type){
		$query = $this->banner->getAdapter()->select();
		return $query->from('banner')->where($this->banner->getAdapter()->quoteInto('type=?',$type))->order('order ASC');
	}
	
	public function getBannerById($id){
		$banner = $this->banner->fetchRow($this->banner->getAdapter()->quoteInto('id=?',$id));//->toArray();
		if($banner){
			$banner = $banner->toArray();
			$banner['positions'] = array();
			if($banner['type'] == 'a'){
				$banner['positions'] = $this->getSavedBannersPositionsByBannerId($id);
			}
		}
		return $banner; 
	}
	
	public function getSavedBannersPositionsByBannerId($id){
		$positions = $this->bannerPosition->fetchAll($this->bannerPosition->getAdapter()->quoteInto('banner_id=?',$id));
		if($positions){
			$positions = $positions->toArray();
		}
		return $positions;
	}
	
	public function addAd($data){
		//var_dump($data); die;
		$insertData = array();
		$insertData['type'] = 'a';
		$insertData['through'] = $data['through'];
		$insertData['banner'] = $data['banner'];
		$insertData['order'] = new Zend_Db_Expr('NULL');
		if($data['type'] == 'order'){
			$insertData['date_started'] = $data['startdate'];
			$insertData['date_ended'] = $data['enddate'];
			$insertData['filename'] = $data['image'];
			$insertData['link'] = $data['link'];
			$insertData['outer_type'] = 'order';
		}
		if($data['type'] == 'partner'){
			$insertData['date_started'] = new Zend_Db_Expr('NULL');
			$insertData['date_ended'] = new Zend_Db_Expr('NULL');
			$insertData['filename'] = new Zend_Db_Expr('NULL');
			$insertData['outer_type'] = 'partner';
		}
		$id = $this->banner->insert($insertData); 
		if($data['through'] != 'y'){
			$position = array();
			foreach($data['positions'] as $item){
				$position = array(
					'banner_id' => $id,
					'position' => $item
				);
				//var_dump($item); die;
				$this->bannerPosition->insert($position);
			}
		}
	}
	
	public function saveAd($data,$id){
		//$banner = $this->getBannerById($id);
		//var_dump($data); die;
		$updateData = array();
		$updateData['through'] = $data['through'];
		$updateData['banner'] = $data['banner'];
		if($data['type'] == 'order'){
			$updateData['date_started'] = $data['startdate'];
			$updateData['date_ended'] = $data['enddate'];
			if(isset($data['image']) && !empty($data['image'])){
				$updateData['filename'] = $data['image'];
			}
			$updateData['link'] = $data['link'];
			$updateData['outer_type'] = 'order';
		}
		if($data['type'] == 'partner'){
			$updateData['date_started'] = new Zend_Db_Expr('NULL');
			$updateData['date_ended'] = new Zend_Db_Expr('NULL');
			$updateData['filename'] = new Zend_Db_Expr('NULL');
			$updateData['outer_type'] = 'partner';
		}
		$this->banner->update($updateData,$this->banner->getAdapter()->quoteInto('id=?',$id));
		$this->bannerPosition->delete($this->banner->getAdapter()->quoteInto('banner_id=?',$id));
		if($data['through'] != 'y'){
			$position = array();
			foreach($data['positions'] as $item){
				$position = array(
					'banner_id' => $id,
					'position' => $item
				);
				$this->bannerPosition->insert($position);
			}
		} 
	}
	
	public function addSlider($data){
		$query = $this->banner->getAdapter()->select()->from(array('b' =>'banner'),array('max_order' => 'MAX(b.order)') );
		$stmt = $query->query();
		$result = $stmt->fetch();
		
		if(null === $result['max_order']){
			$data['order'] = 1;
		}else{
			$data['order'] = $result['max_order'] + 1;
		}
		
		$insertData = array(
			'date_started' => new Zend_Db_Expr('NULL'),
			'date_ended' => new Zend_Db_Expr('NULL'),
			'type' => 's',
			'filename' => $data['image'],
			'banner' => new Zend_Db_Expr('NULL'),
			'order' => $data['order'],
			'pay_service' => $data['type'],
			'link' => $data['link']
		);
		$this->banner->insert($insertData);
	}
	
	public function saveSlider($data,$id){
		$updateData = array(
			'filename' => $data['image'],
			'pay_service' => $data['type'],
			'link' => $data['link']
		);
		$this->banner->update($updateData, $this->banner->getAdapter()->quoteInto('id=?',$id));
	}
	
	public function deleteBanner($id){
		$this->banner->delete($this->banner->getAdapter()->quoteInto('id=?', $id));
	}
	
	public function reorderSlider($id,$old_order,$direction){
		if($old_order == 1 && $direction == 'up'){
			return;
		}
		$query = $this->banner->getAdapter()->select()->from(array('b' =>'banner'),array('max_order' => 'MAX(b.order)') );
		$stmt = $query->query();
		$max_order = $stmt->fetch();
		$max_order = $max_order['max_order'];
		
		if($old_order == $max_order && $direction == 'down'){
			return;
		}
		
		$query = $this->banner->getAdapter()->select()->from(array('b' =>'banner'),array( 'id' => 'b.id') );
		if($direction == 'up'){
			$query->where('b.order = ? ',$old_order - 1 );
		}elseif($direction == 'down'){
			$query->where('b.order = ? ',$old_order + 1 );
		}
		
		$stmt = $query->query();
		$change_id = $stmt->fetch();
		$change_id = $change_id['id'];
		
		$data = array('order' => $old_order);
		
		if($direction == 'up'){
			$data['order'] = $old_order - 1;
			$this->banner->update($data, $this->banner->getAdapter()->quoteInto('id=?',$id));
			$data['order'] = $old_order;
			$this->banner->update($data, $this->banner->getAdapter()->quoteInto('id=?',$change_id));
		}elseif($direction == 'down'){
			$data['order'] = $old_order + 1;
			$this->banner->update($data, $this->banner->getAdapter()->quoteInto('id=?',$id));
			$data['order'] = $old_order;
			$this->banner->update($data, $this->banner->getAdapter()->quoteInto('id=?',$change_id));
		}
	}
	
	public function getSliderData(){
		$query = $this->banner->getAdapter()->select()->from(array('b' =>'banner') )->where('type="s"')->order('b.order ASC');
		$stm = $query->query();
		return $stm->fetchAll();
	}
	
	public function getSavedBannersPositions(){
		$positions = $this->bannerPosition->fetchAll(true);
		if($positions){
			$positions = $positions->toArray();
		}
		return $positions;
	}
	
	public function getEtalonBannerPositions(){
		return array(
			array('label' => 'Главная','value' => 'index'),
			array('label' => 'Статьи','value' => 'article'),
			array('label' => 'Новости','value' => 'news'),
			array('label' => 'Магия','value' => 'magic'),
			array('label' => 'Гадания','value' => 'divination'),
			array('label' => 'Нумерология','value' => 'numerology'),
			array('label' => 'Лунный календарь','value' => 'moon'),
			array('label' => 'Гороскопы','value' => 'horoscope'),
			array('label' => 'Профиль','value' => 'profile'),
			array('label' => 'Поиск','value' => 'search'),
			array('label' => 'Регистрация','value' => 'user'),
		);
	}
	
	public function getBannersByController($position){
		$query = $this->banner->select();
		$query->from(array('b' => 'banner'))
			->setIntegrityCheck(FALSE)
			->joinLeft(array('p' => 'banner_position'), 'b.id = p.banner_id',array('position'))
			->where('p.position = ?',$position)
			->orWhere('b.through = "y" ');
		if($position == 'article'){
			$query->orWhere('p.position = ?','news');
		}
		//var_dump($query->assemble()); die;
		$stm = $query->query();
		$result = $stm->fetchAll();
		return $result;
	}
}