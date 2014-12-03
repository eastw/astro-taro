<?php
class App_View_Helper_MagicTags extends Zend_View_Helper_Abstract{
	
	protected $tagsIds;
	
	const DELIMITER = ';';
	
	public function MagicTags($tagsStr,$tags){
		if(!empty($tagsStr)){
			$this->tagsIds = explode(self::DELIMITER,$tagsStr);
			$tagsList = '';
			$tagsHml = array();
			foreach($this->tagsIds as $id){
				if(!empty($id)){
					foreach($tags as $index => $tag){
						if($tag['id'] == $id){
							$tagsHml[] = '<a href="/magic/tag/'.$tag['alias'].'">'.$tag['tagname'].'</a>';
							break;
						}
					}
				}
			}
			$tagsList = implode(', ',$tagsHml);
			return $tagsList;
		}else{
			return '';
		}
	}
}