<?php

/*
 * Classe para calcular o frete no site dos correios
 
 // Argumentos necessários para calcular o frete,
// nesse array é possível informar dados de comprimento, altura e largura
$args = array('cep_origem'  => '73251901','cep_destino' => '79020300','peso' => '1');

$obj = new correios($args);

$sedex	= $obj->calcular_frete('sedex');
$pac 	= $obj->calcular_frete('pac');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Calculo do Frete Online</title>
    </head>
    <body>
        <h2>Calculo do frete online</h2>
        <p><strong>Sedex: </strong><?=$sedex[prazo]?></p>
        <p><strong>Pac: </strong><?=$pac[prazo]?></p>
    </body>
</html>
 
 
 */

class Correios{

    var $cep_origem;
    var $cep_destino;
    var $peso;
    /* 
     * 41106 para Pac
     * 40010 para Sedex
     */
    var $servico;
    var $comprimento;
    var $largura;
    var $altura;
    
    var $msg_error;
    
    function __construct( $args = array() ) {
        // Dados necessários para calcular o valor do frete
        $default = array('cep_origem' => '00000000',
                         'cep_destino' => '00000000',
                         'peso' => '0.3',
                         'comprimento' => '20',
                         'largura' => '11',
                         'altura' => '2');
        
        $data_correios = array_merge($default, $args);
        
        $this->cep_origem   = $this->clean_cep($data_correios['cep_origem']);
        $this->cep_destino  = $this->clean_cep($data_correios['cep_destino']);
        $this->peso         = (float) $data_correios['peso'];
        $this->comprimento  = (float) $data_correios['comprimento'];
        $this->largura      = (float) $data_correios['largura'];
        $this->altura       = (float) $data_correios['altura'];
        
    }
    
    public function calcular_frete($servico = 'pac'){
        $this->servico = ($servico == 'pac' ? 41106 : 40010);
        // Primeiro verifica o site dos correios
	$xml_url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=$this->cep_origem&sCepDestino=$this->cep_destino&nVlPeso=$this->peso&ncdformato=1&nvlcomprimento=$this->comprimento&nvlaltura=$this->altura&nvllargura=$this->largura&scdmaopropria=n&nvlvalordeclarado=0&scdavisorecebimento=n&sCdAvisoRecebimento=n&nCdServico=$this->servico&nvldiametro=0&StrRetorno=xml";
        $correios = @simplexml_load_file($xml_url);
        $valor_frete = str_replace(",", ".", $correios->cServico->Valor);
        $prazo_frete = $correios->cServico->PrazoEntrega;
		$info =  array(
            "prazo" => $prazo_frete,
            "valor" => $valor_frete
        );
        
        if ( $valor_frete <= 0 || empty($valor_frete) )
        {
            $this->msg_error = $correios->cServico->MsgErro;
            $resultado = $this->frete_pagseguro();
        }else{
            $resultado = $info;
        }
        
        return $resultado;
    }
    
    private function frete_pagseguro(){
        $peso = str_replace('.', ',', $this->peso);
        $ch = curl_init();
	$url = "https://pagseguro.uol.com.br/desenvolvedor/simulador_de_frete_calcular.jhtml?postalCodeFrom=$this->cep_origem&weight=$peso&value=0,00&postalCodeTo=$this->cep_destino";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$resp = curl_exec($ch);
	curl_close($ch);

	$pieces = explode("|", $resp);
        
        if ( $pieces[0] == 'ok' ){
            $valor_sedex 	= $pieces[3];
            $valor_pac 		= $pieces[4];
			
			$pac =  array("prazo" => 10 ,"valor" => $valor_pac);
			$sedex =  array("prazo" => 6 ,"valor" => $valor_sedex);
            
            $resultado = ( $this->servico == 41106 ? $pac : $sedex );
            
        }else{
            // Mostra o erro vindo do site dos Correios
            if ( !empty($this->msg_error) )
            {
                $resultado = $this->msg_error;
            }else{
            // Ou mostra o erro que vem do PagSeguro
                $resultado = $pieces[1];
            }
        }
        
        return $resultado;
    }
    
    // Tratamento do cep para o WebService do PagSeguro que aceita somente números
    private function clean_cep($cep){
        $caracteres = array(".", "-", " ");
        $cep = trim($cep);
        $cep = str_replace($caracteres, "", $cep);
        return $cep;
    }
    
}

?>
