<?php
/**
 * This file is part of the "SmartDoko" package.
 * Copyright (C) 2018 Lars Bleckwenn <lars.bleckwenn@web.de>
 *
 * "SmartDoko" is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "SmartDoko" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
function GetParam($ParamName, $Method = "P", $DefaultValue = "") {
	if ($Method == "P") {
		if (isset ( $_POST [$ParamName] ))
			return $_POST [$ParamName];
			else
				return $DefaultValue;
	} else if ($Method == "G") {
		if (isset ( $_GET [$ParamName] ))
			return $_GET [$ParamName];
			else
				return $DefaultValue;
	} else if ($Method == "S") {
		if (isset ( $_SERVER [$ParamName] ))
			return $_SERVER [$ParamName];
			else
				return $DefaultValue;
	} else if ($Method == "Z") {
		if (isset ( $_SESSION [$ParamName] ))
			return $_SESSION [$ParamName];
			else
				return $DefaultValue;
	}
}
function getConfig($configName) {
	global $pdo;
	$sql = "SELECT * FROM config WHERE name = '$configName'";
	$row = $pdo->query ( $sql )->fetch ( PDO::FETCH_OBJ );
	return $row->value;
}

// https://stackoverflow.com/questions/1472250/pdo-working-with-table-prefixes
class MyPDO extends PDO {
	protected $_table_prefix;
	protected $_table_suffix;
	public function __construct($dsn, $user = null, $password = null, $driver_options = array(), $prefix = null, $suffix = null) {
		$this->_table_prefix = $prefix;
		$this->_table_suffix = $suffix;
		parent::__construct ( $dsn, $user, $password, $driver_options );
	}
	public function exec($statement) {
		$statement = $this->_tablePrefixSuffix ( $statement );
		return parent::exec ( $statement );
	}
	public function prepare($statement, $driver_options = array()) {
		$statement = $this->_tablePrefixSuffix ( $statement );
		return parent::prepare ( $statement, $driver_options );
	}
	public function query($statement) {
		$statement = $this->_tablePrefixSuffix ( $statement );
		$args = func_get_args ();
		
		if (count ( $args ) > 1) {
			return call_user_func_array ( array (
					$this,
					'parent::query'
			), $args );
		} else {
			return parent::query ( $statement );
		}
	}
	protected function _tablePrefixSuffix($statement) {
		return sprintf ( $statement, $this->_table_prefix, $this->_table_suffix );
	}
}
class formreload {
	/**
	 * Formular-Reloads verhindern
	 * Quelle: https://www.zdnet.de/20000913/formular-reloads-verhindern/
	 */
	
	/**
	 * In welchem Array werden die Tokens in der Session gespeichert?
	 *
	 * @var string
	 * @access private
	 *
	 */
	var $tokenarray = '__token';
	
	/**
	 * Wie soll das hidden element heißen?
	 *
	 * @var string
	 * @access public
	 *
	 */
	var $tokenname = '__token';
	function get_formtoken() {
		$tok = md5 ( uniqid ( 'foobarmagic' ) );
		return sprintf ( '<input type="hidden" name="%s" value="%s">', $this->tokenname, htmlspecialchars ( $tok ) );
	}
	function easycheck() {
		$tok = GetParam ( $this->tokenname, 'P', htmlspecialchars ( md5 ( uniqid ( 'foobarmagic' ) ) ) );
		if (isset ( $_SESSION [$this->tokenarray] [$tok] )) {
			return false;
		} else {
			$_SESSION [$this->tokenarray] [$tok] = true;
			return true;
		}
	}
}