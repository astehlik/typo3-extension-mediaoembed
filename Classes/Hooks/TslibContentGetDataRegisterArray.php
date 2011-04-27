<?php

class Tx_Mediaoembed_Hooks_TslibContentGetDataRegisterArray implements tslib_content_getDataHook {

	/**
	 * Extends the getData()-Method of tslib_cObj to process more/other commands
	 *
	 * @param	string		full content of getData-request e.g. "TSFE:id // field:title // field:uid"
	 * @param	array		current field-array
	 * @param	string		currently examined section value of the getData request e.g. "field:title"
	 * @param	string		current returnValue that was processed so far by getData
	 * @param	tslib_cObj	parent content object
	 * @return	string		get data result
	 */
	public function getDataExtension($getDataString, array $fields, $sectionValue, $returnValue, tslib_cObj &$parentObject) {

		$parts = explode(':', $sectionValue, 2);

		$key = trim($parts[1]);
		$type = strtolower(trim($parts[0]));

		if ((string) $type !== 'tx_oembed') {
			return $returnValue;
		}

		if (empty($key)) {
			return $returnValue;
		}

		$returnValue = $parentObject->getGlobal($key, $GLOBALS['TSFE']->register);
		return $returnValue;
	}

	/**
	 * Return response data the input string $keyString defines array keys / getter methods separated by "|"
	 * Example: $var = "response|authorName" will return the value $data["reponse"]->getAuthorName() value
	 *
	 * @param string $keyString var key, eg. "response|authorName" to get the Parameter $data["reponse"]->getAuthorName() back.
	 * @param object|array $data the object or array where the data is read from
	 * @return mixed Whatever value. If none, then blank string.
	 * @see tslib_cObj::getGlobal()
	 */
	protected function getResponseDataFromRegister($keyString, $data) {
		$keys = explode('|', $keyString);
		$numberOfLevels = count($keys);
		$rootKey = trim($keys[0]);
		$value = $this->getArrayOrObjectValue($rootKey, $data);

		for ($i = 1; $i < $numberOfLevels && isset($value); $i++) {
			$currentKey = trim($keys[$i]);
			$value = $this->getArrayOrObjectValue($currentKey, $value);
		}

		if (!is_scalar($value)) {
			$value = '';
		}
		return $value;
	}

	/**
	 * Tries to fetch a value from the $data object / array
	 * with the given $key. If $data is an object we try to call
	 * a getter function for the given $key. If $data is an array
	 * we try to read the $key from the $data array. If both fails,
	 * NULL will be returned.
	 *
	 * @param string $key
	 * @param object|array $data
	 */
	protected function getArrayOrObjectValue($key, $data) {

		if (is_object($data)) {
			$getter = 'get' . ucfirst($key);
			if (method_exists($data, $getter)) {
				return $data->$getter;
			}
		} elseif (is_array($data)) {
			if (array_key_exists($key, $data)) {
				return $data[$key];
			}
		}

		return NULL;
	}
}

?>