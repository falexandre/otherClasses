<?php

/*
* ######    MODO DE USAR A FUNÇÃO	 #######

  
	try {
	
		
	
		//se existir um erro
		if(  ) { throw new MenssagemErro(MenssagemErro::mensagem()); }
	 
	} catch (MenssagemErro $e) {
	  //mostra a mensagem do erro
	  echo $e->errorMessage();
	}
	

  
  atalho try do zencoding
  

*/




class MenssagemErro extends Exception  {
  
  
/***********************************************************************************************************************																								
	
	Função que retorna a mensagem completa
	
***********************************************************************************************************************/
  
  
 
  
  public function errorMessage() {
    
    $errorMsg = 'ERRO na linha '. $this->getLine() . ' do arquivo <b>'.$this->getFile().'</b><br/> 
	<b style="color:#400000" >TIPO ERRO: '. $this->getMessage().'</b>';
    return $errorMsg;
    
	}
	
	
	
	
	
	
	
	
/***********************************************************************************************************************																								
	
	Função que retorna a string da mensagem
	
***********************************************************************************************************************/



	
	public static function mensagem($men = "") {

	switch ($men){
	case  1		: $string_men  	= 'E-mail não existente';	break;
	case  2		: $string_men  	= 'E-mail inválido'; 		break;
	default 	: $string_men	= 'Padrão desconhecido';	break;

	
	}
	return $string_men;
	
	}
 
	
	

	
	
	
	
	
}
	
	
 
?>
