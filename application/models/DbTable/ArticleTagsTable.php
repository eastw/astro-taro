<?php
class Application_Model_DbTable_ArticleTagsTable extends Zend_Db_Table_Abstract{
	protected $_name = 'article_tags';
	
	protected $_referenceMap = array(
		'Article' => array(
			'columns' 				=>	array('article_id'),
			'refTableClass' 	=>	'Application_Model_DbTable_ArticleTable',
			'refColumns' 			=>	array('id')
		),
		'Tags' => array(
			'columns' 				=>	array('tag_id'),
			'refTableClass' 	=>	'Application_Model_DbTable_TagsTable',
			'refColumns' 			=>	array('id')
		),
	);
}

