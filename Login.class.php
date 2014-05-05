<?php
class Login {

	public $erro	 = "Login Efetuado com sucesso!";
	private $liberado = false;

	function __construct($tabela = "admin", $nome_campo_usuario = "usuario",  $nome_campo_senha = "senha"  ){ 

		$this->tabela		=	$tabela;
		$this->usuario		=	$nome_campo_usuario;
		$this->senha		=	$nome_campo_senha;
		
	}

	public function validaUsuario($usuario){
		
		$strSQL = "SELECT COUNT($this->usuario) FROM $this->tabela WHERE $this->usuario = ?";
		$stmt  = BD::getConn()->prepare($strSQL);
		$data  = array($usuario);
		$stmt->execute($data);
		return ($stmt->fetchColumn() > 0) ? true : false;

	}
	
	public function validaSenha($senha){
	
		$strSQL = "SELECT COUNT($this->senha) FROM $this->tabela WHERE $this->senha = ?";
		$stmt  = BD::getConn()->prepare($strSQL);
		$data  = array($senha);
		$stmt->execute($data);
		return ($stmt->fetchColumn() > 0) ? true : false;

	}
	
	
	public function validaStatus($usuario){
	
		$strSQL = "SELECT COUNT($this->senha) FROM $this->tabela WHERE status = ? AND $this->usuario = ? ";
		$stmt  = BD::getConn()->prepare($strSQL);
		$data  = array("S",$usuario);
		$stmt->execute($data);
		return ($stmt->fetchColumn() > 0) ? true : false;

	}
	
	public function dadosUsuario($usuario,$senha){

		$strSQL = "SELECT * FROM $this->tabela WHERE $this->usuario = '$usuario' AND $this->senha = '$senha'";
		return BD::getConn()->query($strSQL);
	
	}
	
	public function validaLogin($usuario , $senha){
	
		$validando_usuario 		= $this->validaUsuario($usuario);
		$validando_senha	 	= $this->validaSenha($senha);
		$validando_status	 	= $this->validaStatus($usuario);
		
		if(!$validando_usuario){
			$this->erro = "Usuario não confere!";
			$this->liberado = false;
		}elseif(!$validando_senha){
			$this->erro = "Senha não confere!";
			$this->liberado = false;
		}elseif(!$validando_status){
			$this->erro = "Você ainda não foi liberado para acesso, entre em contato com o administrador!";
			$this->liberado = false;
		}else{
			$dados = $this->dadosUsuario($usuario , $senha);
			
			foreach($dados as $v){
				
				//$dados_clientes = array("usuario" => $v[usuario] , "senha" => $v[senha] , "nome" => $v[nome], "ultimo_log" => $v[ultimo_log]);
				$dados_clientes = array("usuario" => $v[email] , "senha" => $v[senha] , "nome" => $v[nome], "email" => $v[email], "telefone" => $v[telefone], "cnpj" => $v[cnpj]);
			
			}
			session_start();
			$_SESSION[SESSAO] = $dados_clientes;
			$this->liberado = true;
		}

	}
	
	public function deslogar(){
		
		unset($_SESSION[SESSAO]);
		Header("Location: login.php");
	}
	
	public function validar(){
		session_start();
		if(!isset($_SESSION[SESSAO])){ Header("Location: login.php"); }

	}
	
	public function acesso(){
		
		return $this->liberado;

	}

	
	public function menErro(){
		
		return $this->erro;

	}


	
	public function esqueciSenha($usuario){
		
		$validando_usuario 		= $this->validaUsuario($usuario);
		
		if(!$validando_usuario){
		
			$this->erro = "Esse usuário não existe!";
			return false;
			
		}else{
		
		$nova_senha = Funcoes::geraSenha( 4 , "0123456789" );
	
		$strSQLup = "UPDATE $this->tabela SET $this->senha = ? WHERE $this->usuario = '$usuario' ";
		$stmt  = BD::getConn()->prepare($strSQLup);
		$valor = array($nova_senha);
		$stmt->execute($valor);
		
		return $nova_senha;
		
		}		

	}


}
 ?>