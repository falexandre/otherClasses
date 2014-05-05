<?php

class Categoria {



function __construct(){}//fecha o construtor 


/* retorna array com todas as categorias */

private static function sqlCategoria(){

		// array que conterá as categorias
		$categorias = array();
		  
		$SQL = 'SELECT * FROM categoria ORDER BY titulo';
		 
		$total_busca 		= SQLcontrole::total($SQL);
		
		if($total_busca > 0){		
			$i = 1;
			foreach( SQLcontrole::listar($SQL) as $value){
			$categorias[$i][id] 		= $value[id];
			$categorias[$i][id_pai] 	= $value[id_pai];
			$categorias[$i][nome] 		= $value[titulo];
			$i++;
			}
		}
	return	$categorias;
}




/* Monta sub menu com limite */

private static function subMenuLimit( $id_pai , $arrayCats , $link , $limit = 4  ){
	
	$catsSize = count( $arrayCats );
	$cont_limit = 0;
	
	$ln_cat .='<div class="seg_categoria">
		<ul id="menu_lateral">';
	
	for ( $i = 1; $i <= $catsSize; $i++ ){
		
		if ( $arrayCats[ $i ][id_pai] == $id_pai and $cont_limit < $limit ){
		
		$id_cat 		= $arrayCats[ $i ][id];
		$id_pai_cat 	= $arrayCats[ $i ][id_pai];
		$nome_cat 		= $arrayCats[ $i ][nome];
			
			$ln_cat .=	'<li><a href="'. $link . "categoria" . DS . Funcoes::urlSeo( $nome_cat ) .  DS . $id_cat .'" title="'.$nome_cat.'">'.$nome_cat.'</a></li>';
			$cont_limit++;
		}



	}
	$ln_cat .=	'<li><a href="'. $link . "categoria" . DS . Funcoes::urlSeo( $nome_cat ) . DS . $id_cat .'" title="veja mais..." class="link_veja">veja mais...</a></li>
					</ul>
				</div>';
	return $ln_cat;
}








public static function categoria( $link ){

		$arrayCats = self::sqlCategoria();
		
		$catsSize = count( $arrayCats );
				
		if($catsSize > 0){
		
		for ( $i = 1; $i <= $catsSize; $i++ ){

		
			if( $arrayCats[ $i ][id_pai] == 0 ){
			
				$ln_cat .=	'
				<div class="titulo_categoria">
				  <div class="top_categorias"></div>
				  <div class="rep_categorias">
					<div class="al_titulo_cate">'.$arrayCats[ $i ][nome].'</div>
				  </div>
				  <div class="final_categorias"></div>
				</div>';
				
				$ln_cat .=	self::subMenuLimit( $arrayCats[ $i ][id] , $arrayCats , $link );
				
			
			}
		
		}
		
				
				
		}else{

			$ln_cat .=	'
				<div class="titulo_categoria">
				  <div class="top_categorias"></div>
				  <div class="rep_categorias">
					<div class="al_titulo_cate">Sem Categoria</div>
				  </div>
				  <div class="final_categorias"></div>
				</div>';
		
		}
	return $ln_cat;
}











}
?>