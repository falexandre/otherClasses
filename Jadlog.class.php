<?php

/**
* Função que calcula o o valor do frete na Transportadora JadLog.
*
* @param Parâmetros;
* @param vModalidade     A(1)        Modalidade do frete. Deve conter apenas números (tabela anexa) 
* @param Password        A(8)        Senha de acesso à área de Serviços on-line do site da JADLOG 
* @param vSeguro         A(1)        Tipo do Seguro ―N‖ normal ―A‖ apólice própria
* @param vVlDec          A(18,2)     Valor da Nota fiscal Ex: 100,00
* @param vVlColeta       A(10,0      Valor da coleta negociado com a unidade JADLOG. Ex. 10,00 
* @param vCepOrig        N(8)        CEP de origem Ex.:02714020
* @param vCepDest        N(8)        CEP de destino Ex.:02714020 
* @param vPeso           A(13,2)     Peso Real em quilos Ex.: 13,23
* @param vFrap           A(1)        Frete a pagar no destino, ―S‖ = sim ―N‖ = não.
* @param vEntrega        A(1)        Tipo de entrega ―R‖ retira unidade JADLOG, ―D‖ domicilio.
* @param vCnpj           N(14)       CNPJ do contratante
*
*
* http://www.jadlog.com.br:8080/JadlogEdiWs/services/ValorFreteBean?method=valorar
* &vModalidade=5&Password=C2o0E1m3&vSeguro=N&vVlDec=100,00&vVlColeta=10,00&vCepOrig=89062080&vCepDest=89062080&vPeso=30,30&vFrap=N&vEntrega=D&vCnpj=17977285000118
*
*/

class Jadlog{

    private  $vModalidade;
    private  $Password;
    private  $vSeguro;
    private  $vVlDec;
    private  $vVlColeta;
    private  $vCepOrig;
    private  $vCepDest;
    private  $vPeso;
    private  $vFrap;
    private  $vEntrega;
    private  $vCnpj;
    
    
    function __construct( $args = array() ) {
	
        // Dados necessários para calcular o valor do frete
        $default = array('cep_origem'			=> '89062080',
                         'cep_destino'			=> '89062080',
                         'peso'					=> '30',
						 'valor'				=> '10,00',
                         'modalidade'			=> '5',
                         'cnpj'					=> '17977285000118',
                         'password'				=> 'C2o0E1m3',
                         'seguro'	            => 'N',
                         'coleta'               => '',
                         'acobrar'              => 'N',
                         'entrega'			    => 'D',
                         );
        
        $data_jadlog = array_merge( $default , $args );
        
        $this->vModalidade          =    $default['modalidade'];
        $this->Password             =    $default['password'];
        $this->vSeguro              =    $default['seguro'];
        $this->vVlDec               =    $default['valor'];
        $this->vVlColeta            =    $default['coleta'];
        $this->vCepOrig             =    $default['cep_origem'];
        $this->vCepDest             =    $default['cep_destino'];
        $this->vPeso                =    $default['peso'];
        $this->vFrap                =    $default['acobrar'];
        $this->vEntrega             =    $default['entrega'];
        $this->vCnpj                =    $default['cnpj'];

        
    }
    
    public function calcular_frete(){

    try {

    	$LinkCalcFrete	= "http://www.jadlog.com.br:8080/JadlogEdiWs/services/ValorFreteBean?method=valorar";
        $LinkCalcFrete  .= "&vModalidade=".$this->vModalidade."&Password=".$this->Password."&vSeguro=".$this->vSeguro."&vVlDec=".$this->vVlDec."&vVlColeta=".$this->vVlColeta;
        $LinkCalcFrete  .= "&vCepOrig=".$this->vCepOrig."&vCepDest=".$this->vCepDest."&vPeso=".$this->vPeso."&vFrap=".$this->vFrap."&vEntrega=".$this->vEntrega."&vCnpj=".$this->vCnpj;
    	$calculo 		= simplexml_load_file($LinkCalcFrete);

       if ( $calculo->Retorno == "-1" ) :
            throw new Exception("Acesso negado ou senha incorreta!");
       elseif($calculo->Retorno == "-2"):
            throw new Exception("Não existe tarifa para paramêtros solicitados.");
       elseif($calculo->Retorno == "-3"):
            throw new Exception("Erro do Banco de Dados.");
       elseif($calculo->Retorno == "-99"):
            throw new Exception("Erro indeterminado! Favor entrar em contato com o Suporte através do email helpdesk@jadlog.com.br .");
       else:
           return $calculo->Retorno;
       endif;
       
            
        } catch (Exception $e) {
            
            echo $e->getMessage();

        }


	
    }
    

    

    
}