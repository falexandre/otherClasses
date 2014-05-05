<?php
/*********************************************************************
 * Exemplo de utilização:
 
 
		$quantidade 	 = 8; // total de conteudo exibir por página
		$num_link		 = 10; // links 1 2 3 4 quantidade
	
		//$pagina vem da ultimo parametro da URL SEO
 
		$paginaAtual = ($pagina > 0) ? (int)$pagina : 1;
		$inicio     = ($quantidade * $paginaAtual) - $quantidade;  
		
		
		$totalSQL 	= "SELECT * FROM  galeria_foto WHERE id_galeria = $cod ORDER BY id DESC";
		$sql->query($totalSQL);
		$total = $sql->num_rows();
		
		
 
		//Instancia a paginacao
		$paginacao = new Paginacao($paginaAtual, $total , $quantidade , $num_link);
		//informa a url ou pagina que terá a paginacao
		$paginacao->setUrl('fotos_interna'); //seta o nome da página ou nome do arquivo
		$gets_paginacao = array("$cod"); // parametros $_GET para paginacao
		//adiciona os parametros, com array
		$paginacao->setParametros($gets_paginacao);
		//mostra os links da paginacao
		
		//$total vem do resultado da SQL
		if($total > $quantidade){$paginacao->gerarPaginacao();}


*****************************************************************/


class Paginacao{

    private $pagina;
    private $total;
    private $quantidade;
    private $url;
    private $parametros;
    private $parametrosGET;
    private $busca;
    private $exibir;
 
    public function __construct($pagina, $total_registros, $quantidade, $exibir){
        
		$this->pagina 			= $pagina;
        $this->total  			= $total_registros;
        $this->quantidade 		= $quantidade;
        $this->exibir			= $exibir;
		$this->TotalPorPagina	= ceil($this->total/$this->quantidade);
		$this->posterior		= (($this->pagina + 1) >= $this->TotalPorPagina ) ? $this->TotalPorPagina : $this->pagina + 1 ;
		$this->anterior			= (($this->pagina - 1) == 0) ? 1 : $this->pagina - 1;
		$this->links_laterais 	= ceil($this->exibir / 2);
		$this->inicio 			= $this->pagina - $this->links_laterais;
		$this->limite			= $this->pagina + $this->links_laterais;

    }
	
    public function setUrl($url){
        $this->url = $url;
    }
	
    public function setBusca($param){
        
		$this->busca = $param;
	
    }
	
	/* Transforma array recebida em Query String array(  parametro => valor ) */
    public function setParametrosGET( $param = array() ){
        
    	$this->parametrosGET = http_build_query($param);
			
    }
	
	/* Transforma array recebida em URL SEO array( valor , valor ) */
    public function setParametros($param){
        
		foreach( $param as $valor ):
			if(!empty($valor)):
				$this->parametros .= $valor . "/";
			endif;
		endforeach;
			
    }
 
    function gerarPaginacao(){

        $param = (count($this->parametros) > 0) ? $this->parametros : '';
        $result =   null;
		
	   if ($this->pagina == 1){
	   $result .= 'Primeiro | ';
	   } else{
			$result .= sprintf('<a href="%s%s%s%s" class="link_pgn"  >Primeiro</a> | '	, $this->url,$param , "pagina/" , 1 				, $this->busca	);
			$result .= sprintf('<a href="%s%s%s%s" class="link_pgn" >Anterior</a> | '	, $this->url,$param , "pagina/" , $this->anterior	, $this->busca 	);
	   }
	   //links do centro
		for ($i = $this->inicio; $i <= $this->limite; $i++){
            if($this->pagina == $i){
                $result .=   sprintf('<a href="javascript:;" class="link_pgn" ><strong>%s</strong></a> | ', $i );
            }elseif($i >= 1 && $i <= $this->TotalPorPagina){
                $result .= sprintf('<a href="%s%s%s%s%s" class="link_pgn" >%s</a> | '	, $this->url , $param , "pagina/" , $i , $this->busca , $i  		);
            }
        }
		
		if($this->pagina == $this->TotalPorPagina){
		
		 $result .= 'Pr&oacute;ximo';
		
		}else{
		$result .= sprintf('<a href="%s%s%s%s%s" class="link_pgn" >Pr&oacute;ximo</a> | '	, $this->url , $param , "pagina/" , $this->posterior 		, $this->busca );
		$result .= sprintf('<a href="%s%s%s%s%s" class="link_pgn"  >&Uacute;ltimo</a>'		, $this->url , $param , "pagina/" , $this->TotalPorPagina 	, $this->busca );
		}

        if( $this->total > $this->quantidade ){
           
        	return $result;

        }
       
    }
 
}
 

?>