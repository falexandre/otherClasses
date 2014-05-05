<?php
class BD{
	private static $conn;
	
	private function __construct(){}
	
	public static function getConn(){
		try {
		if(is_null(self::$conn)){
			self::$conn = new PDO(DSN, USER, PASS);
		}
		return self::$conn;
		} catch (PDOException $e) {
		echo 'Não conectou ao banco de dados ERRO: ' . $e->getMessage();
		}
	}
}


	
	