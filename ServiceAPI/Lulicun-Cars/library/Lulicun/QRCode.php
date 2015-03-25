<?php

class Lulicun_QRCode extends Lulicun_Dbo {

	protected static $_collection = 'qrcode';

	public static function getByContact($contact) {
		$db = self::getDb();
		return $db->{static::$_collection}->findOne(array('contact' => $contact));
	}

	public static function getByEncryptedContact($encryptedContact) {
		$db = self::getDb();
		return $db->{static::$_collection}->findOne(array('encryptedContact' => $encryptedContact));
	}

	public static function create($params) {
		if (!isset($params['qrcodeid'])) {
            $params['qrcodeid'] = Lulicun_Legacy::NewQID();
        }
        return parent::create($params);
	}
}