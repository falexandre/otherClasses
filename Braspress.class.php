<?php

/**
* Função que calcula o o valor do frete na Transportadora BrasPress.
*
* @param integer $Cnpj - Cnpj cadastrado na BrasPress
* @param integer $EmpresaTransp - Padrão 2 = BrasPress
* @param integer $CepLocal
* @param integer $CepDestino
* @param integer $CpfDestino
* @param float $Peso // Formato 10.35
* @param float $Valor // Formato 1000.45
* @param integer $QtdeVolumes
* @param integer $TipoFrete - //1 para CIF ou 2 para FOB (cif=frete pago por quem envia | fob=frete pago por quem compra)
* @return array
* @author Frank Darela
* 
* @example print_r(CalcFreteBraspress("digite o cpf","2","3322002","4617000","71612717000132","58","2400.35","10","1"));

http://tracking.braspress.com.br/trk/trkisapi.dll/PgCalcFrete_XML?param=06123867000110,2,89062080,89062080,06123867000110,71612717000132,1,30,10.00,1

*/

class Braspress{

    var $Cnpj;
    var $EmpresaTransp;
    var $CepLocal;
    var $CepDestino;
    var $CpfDestino;
    var $Peso;
    var $Valor;
    var $QtdeVolumes;
    var $TipoFrete;
    
    var $msg_error;
    
    function __construct( $args = array() ) {
	
        // Dados necessários para calcular o valor do frete
        $default = array('cep_origem'			=> '89062080',
                         'cep_destino'			=> '89062080',
                         'peso'					=> '30',
						 'valor'				=> '10.00',
                         'volumes'				=> '1',
                         'cnpj'					=> '06123867000110',
                         'cpf'					=> '71612717000132',
                         'empresa_transporte'	=> '2',
                         'tipo_frete'			=> '1');
        
        $data_braspress = array_merge( $default , $args );
        
        $this->CepLocal  			= $this->clean_cep($data_braspress['cep_origem']);
        $this->CepDestino 			= $this->clean_cep($data_braspress['cep_destino']);
        $this->Peso         		= $data_braspress['peso'];
        $this->Cnpj         		= $data_braspress['cnpj'];
        $this->EmpresaTransp        = $data_braspress['empresa_transporte'];
        $this->CpfDestino        	= $data_braspress['cpf'];
        $this->Valor	        	= $data_braspress['valor'];
        $this->QtdeVolumes        	= $data_braspress['volumes'];
        $this->TipoFrete        	= $data_braspress['tipo_frete'];

        
    }
    
    public function calcular_frete(){

	$LinkCalcFrete	= "http://tracking.braspress.com.br/trk/trkisapi.dll/PgCalcFrete_XML?param=".$this->Cnpj.",".$this->EmpresaTransp.",".$this->CepLocal.",".$this->CepDestino.",".$this->Cnpj.",".$this->CpfDestino.",".$this->TipoFrete.",".$this->Peso.",".$this->Valor.",".$this->QtdeVolumes."";
	$calculo 		= simplexml_load_file($LinkCalcFrete);
	return $calculo;
	
    }
    

    
    // Tratamento do cep somente números
    private function clean_cep($cep){
        $caracteres = array(".", "-", " ");
        $cep = trim($cep);
        $cep = str_replace($caracteres, "", $cep);
        return $cep;
    }
    
}


?>
