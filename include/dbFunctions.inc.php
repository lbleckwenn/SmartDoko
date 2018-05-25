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

function getUser() {
	global $pdo;
	$statement = $pdo->prepare ( "SELECT * FROM players WHERE user_id = ?" );
	$result = $statement->execute ( array (
			$_SESSION ['userid']
	) );
	if ($statement->rowCount () == 1) {
		$player = $statement->fetch ();
		return $player ['id'];
	}
}
function getUserPlayerID() {
	global $pdo;
	$statement = $pdo->prepare ( "SELECT * FROM players WHERE user_id = ?" );
	$result = $statement->execute ( array (
			$_SESSION ['userid'] 
	) );
	if ($statement->rowCount () == 1) {
		$player = $statement->fetch ();
		return $player ['id'];
	}
}

