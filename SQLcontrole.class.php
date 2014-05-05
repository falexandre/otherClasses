<?php
class SQLcontrole {

	public function __construct(){}
/*
Função para listar uma SQL que é recebida por parametro e retorna uma Array
*/	
	public static function listar($SQL){
        try{
        	if( empty($SQL) ){ throw new Exception( "SQL não foi informado!" , 1 ); }               
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){              
            echo 'Não foi possível listar informações ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/*
*	Lista todas as informações da tabela pre definido o SQL
*/
	public static function listAll( $table ){
        try{
            if( empty($table) 		){ throw new Exception( "Nome da tabela não foi informado!" , 1	); }
			$SQL   = "SELECT * FROM ".$table;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){    
            echo 'Não foi possível listar informações ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/* 
*	Gera as condições para SELECT, recebe uma array e monta a condição WHERE com AND
*/
	public function condicional( $where = array() ){
		$terms = null;
		if(!empty($where)){
	    	$terms = " WHERE ";
	    	foreach ($where as $value) { $terms .= $value . " AND ";}
	    	$terms = rtrim( $terms , " AND " );
	    }
	    return $terms;
	}
/*
*	Lista informações onde pode ser adicionado condições e campos pre definido o SQL
*/
	public static function find( $table , $where = array() , $fields = "*" ){
        try{
        	$terms = null;
            if( 	empty($table) 		){ throw new Exception( "Nome da tabela não foi informado!"					); }
            elseif( !is_array($where) 	){ throw new Exception( "Condições não foram informadas como ARRAY!"		); }
            elseif( empty($fields) 		){ throw new Exception( "Campos de seleção não foi informado!"				); }
			$terms = self::condicional($where);
			$SQL   = "SELECT ".$fields." FROM ".$table." ".$terms;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){
            echo 'Não foi possível listar informações ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/* 
*	Lista com configuração de paramentros em forma de array, com parametros já predefinidos
*/
	public static function findAll( $config = array() ){
	 	try {
	 		if( !is_array($config) 	){ throw new Exception( "Configurações não foram informadas como ARRAY!"		); }
	 		elseif( empty($config) 	){ throw new Exception( "Configurações não foram informadas!"					); }
	 		$default = array(

				'tabela'   => '',
				'condicao' => array(),
				'campo'    => '*'	,
				'ordena'   => ''	,
				'agrupa'   => ''	,
				'limite'   => ''

	 		);
			$instrucoes = array_merge( $default, $config );
			$table  	= $instrucoes[ 'tabela'		];
			$where  	= $instrucoes[ 'condicao'	];
			$fields 	= $instrucoes[ 'campo'		];
			$order  	= $instrucoes[ 'ordena'		];
			$groupby  	= $instrucoes[ 'agrupa'		];
			$limit  	= $instrucoes[ 'limite'		];
			if( 	empty($table) 		){ throw new Exception( "Nome da tabela não foi informado!"					); }
			elseif( !is_array($where) 	){ throw new Exception( "Condições não foram informadas como ARRAY!"		); }
			elseif( empty($fields) 		){ throw new Exception( "Campos de seleção não foi informado!"				); }
			$terms   	= ( empty($where) 	) 	? null : self::condicional($where);
			$groupby 	= ( empty($groupby)	)	? null :  ' GROUP BY('.$groupby.') ';
			$order   	= ( empty($order)	)	? null :  ' ORDER BY '.$order.' ';
			$limit   	= ( empty($limit)	)	? null :  ' LIMIT '.$limit.' ';
			$SQL     	= "SELECT ".$fields." FROM " . $table . $terms .  $groupby . $order  . $limit;
			$stmt    	= BD::getConn()->prepare($SQL);
			$stmt->execute();     
			$total   	= $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
	 		
	 	} catch (Exception $e) {
	 		 echo 'Não foi possível listar informações ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Função para inser dados no banco que recebe NOME DA TABELA, CAMPOS EM UMA ARRAY, VALORES EM UMA ARRAY,
* Retorna ultimo ID registrado
*/	
	public static function inserir( $tabela , $campos  , $valores ){
		try{
			if( 	empty($tabela)		){ throw new Exception( "Nome da tabela não foi informado!"					); }
			elseif( !is_array($campos)	){ throw new Exception( "Campos não foram informadas como ARRAY!"			); }
			elseif( empty($campos)		){ throw new Exception( "Campos não foram informado!"						); }
			elseif( !is_array($valores)	){ throw new Exception( "Valores não foram informadas como ARRAY!"			); }
			foreach($campos as $nome_campo){ $complemento .= $nome_campo . " = ? , "; }
			$complemento = rtrim( $complemento , " , " );
			$strSQL      = "INSERT INTO $tabela SET $complemento ";
			$stmt        = BD::getConn()->prepare($strSQL);
			$stmt->execute($valores);
			$total       = $stmt->rowCount();
	        if( $total > 0 ){
				$last_id = BD::getConn()->lastInsertId();
				return $last_id;
	        }else{  return false; }
	    }catch (Exception $e) {
	 		 echo 'Não foi possível inserir informações ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Função para alterar dados no banco que recebe NOME DA TABELA, CAMPOS EM UMA ARRAY, VALORES EM UMA ARRAY,
* CONDICIONAL EM STRING
*/
	public static function alterar( $tabela , $campos  , $valores , $condicao ){
		try{
			if( 	empty($tabela)			){ throw new Exception( "Nome da tabela não foi informado!"					); }
			elseif( !is_array($campos)		){ throw new Exception( "Campos não foram informadas como ARRAY!"			); }
			elseif( empty($campos)			){ throw new Exception( "Campos não foram informado!"						); }
			elseif( !is_array($valores)		){ throw new Exception( "Valores não foram informadas como ARRAY!"			); }
			elseif( !is_array($condicao)	){ throw new Exception( "Condição não foram informadas como ARRAY!"			); }
			elseif( empty($condicao)		){ throw new Exception( "Condição não foi informada!"						); }
			foreach($campos as $nome_campo){ $complemento .= $nome_campo . " = ? , "; }
			$complemento = rtrim( $complemento , " , " );
			$terms       = self::condicional($condicao);
			$strSQL      = "UPDATE $tabela SET $complemento $terms";
			$stmt        = BD::getConn()->prepare($strSQL);
			$stmt->execute($valores);
			$total       = $stmt->rowCount();
			if( $total > 0 ){  return $stmt; }else{ return false; }
	    }catch (Exception $e) {
 		 	echo 'Não foi possível alterar informações ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Função para excluir uma tabela que é recebida por parametro e condicional em forma de STRING
*/	
	public static function excluir( $tabela ,  $condicao ){
		try{
			if( 	empty($tabela)			){ throw new Exception( "Nome da tabela não foi informado!"				); }
			elseif( empty($condicao)		){ throw new Exception( "Condição não foi informada!"					); }
			elseif( !is_array($condicao)	){ throw new Exception( "Condição não foram informadas como ARRAY!"		); }
			$terms	= self::condicional($condicao);
			$strSQL = "DELETE FROM  $tabela $terms";
			$stmt   = BD::getConn()->query($strSQL);
	        $total  = $stmt->rowCount();
	        if( $total > 0 ){  return true; }else{ return false; }
	    }catch (Exception $e) {
 		 	echo 'Não foi possível excluir informações ERRO: <br />' . nl2br( $e->getMessage() );
	 	}	        
	}
/*
* Função que retorna o total de linhas de uma SQL que é recebido por parametro
*/
	public static function total ($SQL){
		try{
			if( empty($SQL) ){ throw new Exception( "SQL não foi informado!"  ); }
			$stmt  	= BD::getConn()->prepare($SQL);
			$stmt->execute();
			return	$stmt->rowCount();
		}catch (Exception $e) {
 		 	echo 'Não foi possível totalizar informações ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Função que retorna o total de linhas de uma SQL que é recebido por parametro
*/
	public static function totalRow ( $table , $where = array() , $fields = "id" ){
		try{
        	$terms = null;
            if( 	empty($table) 		){ throw new Exception( "Nome da tabela não foi informado!"					); }
            elseif( !is_array($where) 	){ throw new Exception( "Condições não foram informadas como ARRAY!"		); }
            elseif( empty($fields) 		){ throw new Exception( "Campos de seleção não foi informado!"				); }
			$terms = self::condicional($where);
			$SQL   = "SELECT ".$fields." FROM ".$table." ".$terms;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
			return $total;
        }  catch ( PDOException $e ){
            echo 'Não foi possível listar informações ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}

        
        
        
        
        
}