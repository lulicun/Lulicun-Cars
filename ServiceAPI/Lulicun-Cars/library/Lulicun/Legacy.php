<?php

/**
 *This class is used to do statistics of Lulicun.
**/
Class Lulicun_Legacy extends Lulicun_Dbo {

	protected static $_collection = 'counter';

	private static function _create($counter_id) {
		$counter = array('_id' => $counter_id, 'c' => 0);
		parent::create($counter);
	}

	public static function incrementCounter($counter_id) {
		$db = self::getDb();
		$isInitialized = $db->{static::$_collection}->findOne(array('_id' => $counter_id), array('c' => 1));
		if (!$isInitialized) {
			self::_create($counter_id);
		}
		$value = $db->{static::$_collection}->findAndModify(
            array('_id' => $counter_id), // update this counter
            array('$inc' => array('c' => 1)), // increment c by one
            array('c' => 1), // return field 'c' with the modified document
            array('new' => true) // return the modified document
        );
        // return the "auto-incremented" id
        return $value['c'];
	}

	/**
     * generate an "auto-incremented" qrcode id because legacy code
     */
	public static function NewQID($db = null) {
		return self::incrementCounter('qid');
	}

	/**
     * generate an "auto-incremented" user id because legacy code
     */
	public static function NewUID($db = null) {
		return self::incrementCounter('uid');
	}
}