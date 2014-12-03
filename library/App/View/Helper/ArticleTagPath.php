<?php
class App_View_Helper_ArticleTagPath extends Zend_View_Helper_Abstract{
	
	const DELIMITER = ';';
	
	public function ArticleTagPath($tagsStr,$tags,$startTag=null){
		if(null === $startTag){
			if(!empty($tagsStr)){
				$tagsIds = explode(self::DELIMITER,$tagsStr);
				$firstTag = $tagsIds[0];   
				if(!empty($firstTag)){
					foreach($tags as $tag){
						if($tag['id'] == $firstTag){
							return $tag['alias'];  
						}
					}
				}
			}else{
				return 'no-tag';
			}
		}else{
			return $startTag['alias'];  
		}
	}
}