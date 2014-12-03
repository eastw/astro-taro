<?php
class App_View_Helper_ArticleTags extends Zend_View_Helper_Abstract{
	
	protected $tagsIds;
	
	const DELIMITER = ';';
	
	public function ArticleTags($tagsStr,$tags){
		if(!empty($tagsStr)){
			$this->tagsIds = explode(self::DELIMITER,$tagsStr);
			$tagsList = '';
			$tagsHml = array();
			foreach($this->tagsIds as $id){
				if(!empty($id)){
					foreach($tags as $index => $tag){
						if($tag['id'] == $id){
							$tagsHml[] = '<a href="/statyi/tag/'.$tag['alias'].'">'.$tag['tagname'].'</a>';
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