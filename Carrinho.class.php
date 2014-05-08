<?php
 
 
 
final class Carrinho{
 
    /**
     *
     * Método construtor
     * Caso não existe a $_SESSION['carrinho_virtual_force'],
     * ele criar atribuindo um array vazio
     *
     */
	 
    public function __construct(){
        if(!isset($_SESSION[SESSAOCARRINHO])){
            $_SESSION[SESSAOCARRINHO] = array();
        }
    }
	/* 
		
		Retorna o valor para carrinho exibido no topo

	*/

    public static function cesta(){
    	$cont 	= self::quantidade();
    	$itens  = $cont > 1 ? 'itens' : 'item';
		return $cont > 0 ? self::quantidade() . " ".$itens : "vazio";
    }	
	/* 
		
		Retorna a quantidade do carrinho

	*/

    public static function quantidade(){
		
		$itens = 0;
		 if(isset($_SESSION[SESSAOCARRINHO]) ){
			$itens =  count( $_SESSION[SESSAOCARRINHO] );
        }
		return $itens;
    }	

	/**
	 * Retorna quantidade em estoque de um produto
	 * @param  $id integer - id do produto a buscar estoque
	 */

    public static function estoque( $id ){
		
		$SQL	= "SELECT estoque FROM produto WHERE id = '$id' AND status = 'S'";
		$total	= SQLcontrole::total($SQL);
		if( $total > 0 ):
			$query	= BD::getConn()->query($SQL);
			$row	= $query->fetch(PDO::FETCH_ASSOC);
			return $row['estoque'];
		else:
			return false;
		endif;
    }
    /**
     *
     * Adiciona um produto ao carrinho
     *
     * @param integer $id é código do produto adicionado
     * @param integer $qtd é  quantidade
     *
     */
    public static function adicionar( $id , $qtd = 1 ){
		
		$estoque = self::estoque($id);
		$qtd	 = ( $qtd < 1 ) ? 1 : Funcoes::soNumeros($qtd); //garantindo que não seja 0 e seja so numeros
		
		if(!$estoque):
			return false;
		elseif( $estoque >= $qtd ):
			//verificar se o valor é nulo
			
			$indice = sprintf('%s', (int)$id );
			
			/**
			 * Se não existir esse indice no carrinho
			 * Atribui ao carrinho com a quantidade
			 */
			if(!isset($_SESSION[SESSAOCARRINHO][$indice])){
				$_SESSION[SESSAOCARRINHO][$indice] = (int)$qtd;
			}
		endif;
    }

    /**
     *
     * Altera a quantidade do carrinho
     * O indice do array é a junção do ID do produto com o ID da cor
     *
     * @param string $indice é a chave do array
     * @param integer $qtd é quantidade para alterar
     */
    public static function alterar($id, $qtd){
        //verifica se existe esse indice
		$qtd	 	= ( $qtd < 1 ) ? 1 : Funcoes::soNumeros($qtd); //garantindo que não seja 0 e seja so numeros
		$estoque 	= self::estoque($id);
		if( $qtd <= $estoque and isset($_SESSION[SESSAOCARRINHO][$id]) ):
			$_SESSION[SESSAOCARRINHO][$id] = (int)$qtd;
		else:
			return false;
		endif;
    }
 
    /**
     *
     * Excluir o produto do carrinho
     * @param string $indice
     */
    public static function excluir($indice){
        //excluir o produto do carrinho
        unset($_SESSION[SESSAOCARRINHO][$indice]);
    }
 
    /**
     *
     * Retorna um array com os dados dos produtos no carrinho
     * @return array $result
     */
    public static function listarProdutos(){
        //inicia a variável com array
        $result 	= array();
        if( isset( $_SESSION[SESSAOCARRINHO] ) AND count( $_SESSION[SESSAOCARRINHO] ) > 0  ){

    		$carrinho 	= $_SESSION[SESSAOCARRINHO];
    	
    			foreach( $carrinho as $id => $qtd ){
    							
    				//busca a foto do produto
    				$foto						=	Produto::foto( $id , "fotop");
    	 
    				//Realizar a busca pelo produto, selecionado apenas o nome do produto e preço
    				$query_product              = BD::getConn()->query("SELECT codigo , titulo, valor , valor_de , peso , frete , marca_id FROM produto WHERE id = '$id'   AND status = 'S' ");
    				
    				//retorna os itens do tabela produtos
    				$row_product 				= $query_product->fetch(PDO::FETCH_ASSOC);
    				
    				
    				
    				/**
    				 * Adicionar ao array o nome do produto, preço , quantidade, subtotal e a cor
    				 * È importante reparar que indice desse array será o mesmo
    				 * do carrinho.
    				 */
    	 
    				$result[$id]['codigo'] 		= $row_product['codigo'];
    				$result[$id]['produto'] 	= $row_product['titulo'];
    				$result[$id]['valor']   	= $row_product['valor'];
    				$result[$id]['valor_de']   	= $row_product['valor_de'];
    				$result[$id]['qtd']     	= $qtd;
    				$result[$id]['subtotal']	= $row_product['valor'] * $qtd;
    				$result[$id]['subtotal_de']	= $row_product['valor_de'] * $qtd;
    				$result[$id]['peso']		= floatval(str_replace(',', '.', $row_product['peso'] )) * $qtd;
    				$result[$id]['frete']		= $row_product['frete'];
    				$result[$id]['marca_id']	= $row_product['marca_id'];
    				$result[$id]['cor']     	= '';
    				$result[$id]['foto']     	= $foto;
    	 
    		
    			}
    		
            return $result;
        }
    }
     
    
 	



    /**
     *
     * Retorna o peso total do carrinho
     * Aqui apenas listaremos os pesos
     * somaremos o total, que é
     * o peso vezes a quantidade
     * retiramos os pesos com frete grátis
     * separamos sem frete caso deseja cobrar o frete
     * onde o cliente opita por sedex
     * @return  Array  - Chave PAGO peso total Chave GRATIS Peso subtraido os grátis
     * 
     */
    public static function pesoTotal(){
        //listar todos os produtos
        $produtos = self::listarProdutos();
        //inicia as variáveis
        $SEDEX    		= 0;
        $PAC			= 0;
        //listar os produtos, para resgatar o peso total sedex
         
        foreach($produtos as $indice => $row){
            //realiza a soma
            $SEDEX += floatval(str_replace(',', '.', $row['peso'] ));
        } 
		  //listar os produtos, para resgatar o peso total PAC com gratis
        foreach($produtos as $indice => $row){
            //realiza a soma
			$frete	= $row['frete'];
			if( $frete == "N" ):
            $PAC	+= floatval(str_replace(',', '.', $row['peso'] ));
			endif;
        }
         
        return array( 'PAGO' => $SEDEX , 'GRATIS' => $PAC );
         
    } 




    /**
     *
     * Retorna o valor total do carrinho
     * Aqui apenas listaremos o produtos
     * somaremos o subtotal, que é
     * o preço vezes a quantidade
     * Isso já foi calculado no método listarProdutos
     */
    public static function valorTotal(){

            //inicia a variável
            $total    = 0;

        if( isset( $_SESSION[SESSAOCARRINHO] ) AND count( $_SESSION[SESSAOCARRINHO] ) > 0  ){

            //listar todos os produtos
            $produtos = self::listarProdutos();
            //listar os produtos, para resgatar o subtotal
           
            foreach($produtos as $indice => $row){
                //realiza a soma
                $total += $row['subtotal'];
            }
            //verifica se existe cupom de desconto e efetua o mesmo
            if( isset($_SESSION[SESSAOCUPOMDESCONTO]) AND !empty($_SESSION[SESSAOCUPOMDESCONTO])  ){
                $valor_cupom = $_SESSION[SESSAOCUPOMDESCONTO]['valor'];
                $total_desco = $total - $valor_cupom;
                $total       = $total_desco;
            }



        }
         
        return $total;
         
    }



    /**
     *
     * Retorna o valor total do carrinho somando frete
     * Chama o valor total e soma com valor do frete
     */
    public static function valorTotalFrete( $tipo_frete , $cep ){

        $tipo_frete = $tipo_frete == 2 ? 'pac'      : 'sedex';
        $peso_tipo  = $tipo_frete == 2 ? 'GRATIS'   : 'PAGO';

        /* Conta os números digitados */
        $total_numero   = strlen($cep);
        $peso           = self::pesoTotal();

            if($total_numero == 8 ){

                if( $peso[$peso_tipo] >= 30 ){

                    $total_compra = self::valorTotal();
                    $total_frete  = $total_compra + FRETE_PADRAO;
                    return array( 'FRETE' => FRETE_PADRAO , 'TOTAL' => $total_compra , 'TOTAL_FRETE' => $total_frete );

                }else{

                    $dados_calc = array( 'cep_destino'=> $cep , 'peso' => $peso[$peso_tipo] );
                    $calc_frete = new   Correios( $dados_calc );
                    $calculo    = $calc_frete->calcular_frete(  $tipo_frete   );
                    $ERRO       = $calc_frete->msg_error;

                    if( empty($ERRO) ){

                        $total_compra = self::valorTotal();
                        $valor_frete  = $calculo['TOTALFRETE'];
                        $total_frete  = $total_compra + $valor_frete;
                        return array( 'FRETE' => $valor_frete , 'TOTAL' => $total_compra , 'TOTAL_FRETE' => $total_frete );
            
                    }else{
                        return '<span class="resultado_frete">N&atilde;o foi poss&iacute;vel calcular o frete! tente novamente.</span>';
                    }

                }


            }else{

                return '<span class="resultado_frete">N&uacute;mero do cep inv&aacute;lido!</span>';

            }
   
         
    }







	
	
}
?>