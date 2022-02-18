<?php
class ConnexionBDD
{
	private $_bdd;

	public function __construct($_CONST_PDO_HOSTNAME,$_CONST_PDO_USERNAME,$_CONST_PDO_PASSWORD,$_CONST_PDO_DATABASE) {
		try
		{
			$bdd = new PDO("mysql:host=$_CONST_PDO_HOSTNAME;dbname=$_CONST_PDO_DATABASE", $_CONST_PDO_USERNAME, $_CONST_PDO_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			$this->_bdd = $bdd;
		}
		catch(Exception $e)
		{
			die('Erreur : '.$e->getMessage());
		}
    }

	public function getConn(){return $this->_bdd;}

	public function querySQL($_query) {
		return $this->getConn()->query($_query);
	}
}