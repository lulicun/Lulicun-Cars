<?php
//Root Class, extended by all other classes talking with DB
class Lulicun_Dbo {

	private static $_db;
	private static $_collection;
	
	//return a instance of DB
	public static function getDb(){
		if(!self::$_db){
			self::$_db = self::init();
		}
		return self::$_db;
	}

	//Initialize DB, setup configs, create DB instance
	private static function init(){
		//Load conigs from application.ini
		$config = Zend_Registry::get('config')->get('mongodb');
		$mongo = new MongoClient($config->uri);
		$db = $mongo->selectDB($config->database);
		return $db;
	}

	/**
	 * creat a new document
	 *
	 * @param array|object $params the data to insert 
	 * @return bool
	 */

	public static function create($params){
		$db = self::getDb();
		$result = $db->{static::$_collection}->insert($params);
		return $result;
	}

	/**
	 * find documents by query
	 * 
	 * @param array $query the filtering query
	 * @param array fields fields to return 
	 * @return array list of found documents
	 */
	public static function find($query, $fields = array()){
		$db = self::getDb();
		return $db->{static::$_collection}->find($query,$fields);
	}

	/**
	 * find one document by query
	 *
	 * @param array $query the filtering query
	 * @param array fields fields to return
	 * @return array the found document 
	 */
	public static function findOne($query, $fields = array()){
		$db = self::getDb();
		return $db->{static::$_collection}->findOne($query,$fields);
	}

	public static function getById($id, $fields = array()) {
		$db = self::getDb();
		if (!$id instanceof MongoId) {
			$id = new MongoId($id);
		}
		return $db->{static::$_collection}->findOne(array('_id' => $id), $fields);
	}
}