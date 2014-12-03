<?php
class App_View_Helper_RandomTags extends Zend_View_Helper_Abstract{
	
	const NO_TAG_ID = 17;
	
	public function RandomTags($tags,$curtag=null,$type){
		$random = array();
		
		foreach($tags as $index => $tag){
			if($tag['id'] == self::NO_TAG_ID){
				unset($tags[$index]);
				break;
			}
		}
		//var_dump($tags); die;
		$countTags = count($tags);
		if(null !== $curtag){
			foreach($tags as $index => $item){
				if($curtag['id'] == $item['id']){
					$random[] = $index;
					break;
				}
			}
			
			$tmp = array();
			if($countTags > 5){
				$tmp = array_rand($tags,4);
			}else{
				$tmp = array_rand($tags,$countTags);
			}
			shuffle($tmp);
			foreach($tmp as $index => $item){
				if(isset($random[0]) && $random[0] == $item){
					unset($tmp[$index]);
				}
			}
			//var_dump($tmp); die;
			if($tmp){
				$random += $tmp;
			}
		}else{
			if($countTags > 5){
				$random = array_rand($tags,5);
			}else{
				$random = array_rand($tags,$countTags);
			}
			//var_dump($countTags); die;
			shuffle($random);
		}
		//var_dump($random); die;
		$html = '<div id="taging">';
		$html .= '<div class="actual_header">Популярные теги</div>';
		foreach($random as $item){
			if(null !== $curtag && $curtag['id'] == $tags[$item]['id']){
				$html .= '<div class="tag_item_active">'.$curtag['tagname'].'</div>';
			}else{
				if($type == 'article'){
					$type = 'statyi';
				}
				$html .= '<div class="tag_item"><a href="/'.$type.'/tag/'.$tags[$item]['alias'].'">'.$tags[$item]['tagname'].'</a></div>';
			}
		}
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
		return $html;
	}
}