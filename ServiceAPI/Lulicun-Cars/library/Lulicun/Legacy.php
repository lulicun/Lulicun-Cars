<?php

/**
*TODO: This class is used to do statistics of Lulicun.
**/
Class Lulicun_Legacy extends Lulicun_Dbo{

	public static function incrementCounter($counter_id) {
		$db = self::getDb();
		$value = $db->counters->findAndModify(
            array('_id' => $counter_id), // update this counter
            array('$inc' => array('c' => 1)), // increment c by one
            array('c' => 1), // return field 'c' with the modified document
            array('new' => true) // return the modified document
        );
        error_log("legacy: " . json_encode($value));
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