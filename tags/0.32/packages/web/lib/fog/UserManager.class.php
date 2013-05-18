<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2010  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */
 
class UserManager
{
	const ORDERBY_USERNAME = 1;

	private $db;

	function __construct( $db )
	{
		$this->db = $db;
	}
	
	public function isValidPassword( $password1, $password2, $minLen, $validChars )
	{
		if ( $password1 == $password2 )
		{
			if ( strlen($password1) >= $minLen )
			{
				for( $i = 0; $i < strlen( $password1 ); $i ++ )
				{
					$blFound = false;
					for( $z = 0; $z < strlen( $validChars ); $z++ )
					{
						if ( $validChars[$z] == $password1[$i] )
						{
							$blFound = true;
							break;
						}
					}
					
					if ( ! $blFound ) return false;
				}
				return true;
			}
		}
		return false;
	}
	
	public function isValidUsername( $username )
	{
		return preg_match("/^[[:alnum:]]*$/", $username );
	}
	
	public function attemptLogin( $username, $password )
	{
		if ( $username != null && $this->isValidUsername( $username ) && $password != null )
		{
			$sql = "SELECT 
					uId 
				FROM 
					users 
				WHERE 
					uName = '" . $this->db->escape($username) . "' and 
					uPass = '" . md5( $password ) . "'";
					
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					return $this->getUserById( $ar["uId"] );
				}
			}					
		}
		return null;
	}
	
	public function getAllUsers($ordering=self::ORDERBY_USERNAME)
	{
		if ( $this->db != null && $ordering !== null && is_numeric( $ordering ) && $ordering >= 0 )
		{
			$orderby = "";
			if ( $ordering = self::ORDERBY_USERNAME )
				$orderby = " order by uName";
				
			$sql = "SELECT 
					uId
				FROM 
					users 
				" . $orderby;

			if ( $this->db->executeQuery($sql) )
			{
				$arUserIds = array();
				while( $ar = $this->db->getNext() )
				{
					$arUserIds[] = $ar["uId"];
				}
				
				$arOut = array();
				for( $i = 0; $i < count( $arUserIds ); $i++ )
				{
					$uid = $arUserIds[$i];
					if ( $uid !== null && is_numeric($uid) )
					{
						$tmpUser = $this->getUserById($uid);
						if ( $tmpUser != null )
							$arOut[] = $tmpUser;
					}
				}	
				return $arOut;
			}
		}
		return null;
	}

	public function deleteUser( $user )
	{
		if ( $this->db != null && $user != null && $user->getID() >= 0 )
		{
			$sql = "DELETE FROM users WHERE uId = '" . $this->db->escape( $user->getID() ) . "'";
			return $this->db->executeUpdate( $sql ) == 1;
		}
		return false;
	}

	public function updateUser( $user )
	{
		if ( $this->db != null && $user != null && $user->getID() >= 0 )
		{
			if ( ! $this->doesUserExist( $user->getUserName(), $user->getID() ) )
			{
				$sql = "UPDATE 
						users 
					SET 
						uName = '" . $this->db->escape( $user->getUserName() ) . "', 
						" . (($user->getPassword() != null &&  strlen(trim( $user->getPassword() )) > 0) ? "uPass = MD5('" . $this->db->escape( $user->getPassword() ) . "'), " : "" ) . "
						uType = '" . $this->db->escape($user->getType() ) . "' 
					WHERE 
						uId = " . $user->getID();
						
				return $this->db->executeUpdate( $sql ) == 1;
			}
			else
				throw new Exception( _("A user with this name already exists!") );
		}
		return false;
	}

	public function addUser( $user, $strCreator )
	{
		if ( $this->db != null && $user != null )
		{
			if ( ! $this->doesUserExist( $user->getUserName() ) )
			{
				$sql = "INSERT INTO 
						users( uName, uPass, uCreateDate, uCreateBy, uType ) 
						values( '" . $this->db->escape($user->getUserName()) . "', MD5('" . $this->db->escape($user->getPassword()) . "'), NOW(), '" . $this->db->escape($strCreator) . "', '" . (($user->getType() == User::TYPE_MOBILE) ? '1' : '0') . "')";	
				return $this->db->executeUpdate( $sql ) == 1;
			}
			else
				throw new Exeption(_("User already exists!"));
		}
		
		return false;	
	}
	
	public function doesUserExist($username, $exclude=-1)
	{
		if ( $this->db != null && $username != null )
		{
			$sql = "SELECT count(*) as cnt from users where uName = '" . $this->db->escape( $username ) . "' and uId <> $exclude";
			
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					if ( $ar["cnt"] > 0 )
						return true;
				}
			}		
		}
		return false;
		
	}
	
	public function getUserById( $id )
	{
		if ( $this->db != null && $id !== null && is_numeric( $id ) && $id >= 0 )
		{
			$sql = "SELECT 
					*
				FROM 
					users 
				WHERE 
					uId = '" . $this->db->escape($id) . "'";
					
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					return new User( $ar["uId"], $ar["uName"], null, null, ($ar["uType"]==User::TYPE_MOBILE) ? User::TYPE_MOBILE : User::TYPE_ADMIN );
				}		
			}		
		}
		return null;
	}
}

?>
