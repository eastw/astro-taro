<?php
class App_View_Helper_AlsoTags extends Zend_View_Helper_Abstract{
	public function AlsoTags($paginator,$tags,$type){
		$pageTags = array();
		foreach($paginator as $article){
			$tmp = explode(';',$article['quicktag']);
			if(count($tmp)){
				foreach($tmp as $item){
					if(!empty($item)){
						$pageTags[] = $item;
					}
				}
			}
		}
		$pageTags = array_unique($pageTags);
		$tagsStrArray = array();
		
		if(count($pageTags)){
			foreach($tags as $tag){
				if(in_array($tag['id'],$pageTags)){
					$tagsStrArray[] = '<a href="/'.$type.'/tag/'.$tag['alias'].'">'.$tag['tagname'].'</a>';
				}
			}
		}
		
		//echo '<pre>';
		//var_dump($tagsStr); die;
		return implode(', ',$tagsStrArray);
	}
}