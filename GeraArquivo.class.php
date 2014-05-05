<?php

/***********************************************************************************************************************																								

$string	=	"Adicionou um texto";
$string2	= "Adicionou um texto  2";
$string3	=	"Adicionou um texto  3";

$arquivo = new Txt($string,'gravado.txt');
$arquivo->escreve();
$arquivo->texto = $string2;
$arquivo->escreve();
$arquivo->texto = $string3;
$arquivo->escreve();

$arquivo->ler();

echo $arquivo;

***********************************************************************************************************************/


class GeraArquivo {
##### Atributos #####

	public $texto;
	public $nomeArquivo;
	public $linha;


##### Metodos #######

function __construct($string , $nomeArquivo ){ 
    
	$this->texto 	= $string;
	$this->arquivo 	= $nomeArquivo;
	$this->linha 	= $linha;
	
} 

//****** Metodo para escrever no Arquivo *******

public function escreve (){
	// abre o arquivo, caso não exista tenta crialo
	$aberto = fopen($this->arquivo, "a+");
	//essa é a quebra de linha
	$quebra = chr(13).chr(10);
	//escreve o texto no arquivo
	$escreve = fwrite($aberto, $this->texto.$quebra);
	//fecha o arquivo
	fclose($aberto);

}

//****** Metodo para Reescrever no Arquivo *******

public function reescreve (){
	// abre o arquivo, caso não exista tenta crialo
	$aberto = fopen($this->arquivo, "w");
	//essa é a quebra de linha
	$quebra = chr(13).chr(10);
	//escreve o texto no arquivo
	$escreve = fwrite($aberto, $this->texto.$quebra);
	//fecha o arquivo
	fclose($aberto);

}
//****** Metodo para Ler o Arquivo *******

public function ler (){
	// abre o arquivo, caso não exista tenta crialo
	$aberto 		= fopen($this->arquivo, "r");
	// laço para pegar todas as linhas do txt
	if ($aberto == false) die('Não foi possível abrir o arquivo.');
	// adiciona a variavel linha por linha ate detectar o final
	while(!feof($aberto)) {
		$recuperado 	.= fgets($aberto). '<br />';
	}
	$this->linha 	= $recuperado;
	fclose($aberto);
		
}
// metodo para converter o Objeto em String Usado para função LER
public function __toString (){
	return $this->linha; // retorna em forma de string o Arquivo que foi lido.
}



}
?>