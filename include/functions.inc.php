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