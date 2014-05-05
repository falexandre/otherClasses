<?php
/**
* CLasse que vai montar uma pesquisa de informações com paginação
*/
class Loadinfo
{
   

    /* SQL inicial que pode ser adicionado condição pelo metodo getParamentros */
    private $SQL                = "SELECT * FROM intrvirt_db.tarefa"; 
    private $total;
    /* total de itens mostrados antes de acionar paginação */
    private $total_mostrar      = 8; 
    /* links 1 2 3 4 quantidade */
    private $num_link           = 5; 
    private $pagina; 
    private $paginaAtual; 
    private $inicio;   
    private $LINK_DESPUBLICADO;            
    private $LINK;              
             

    function __construct()
    {
        $this->pagina               = Url::getURLpag();
        $this->paginaAtual          = ( $this->pagina > 0 ) ? (int)$this->pagina : 1;
        $this->inicio               = ( $this->total_mostrar * $this->paginaAtual ) - $this->total_mostrar;
        $this->LINK_DESPUBLICADO    = ( defined('PUBLICADO') AND PUBLICADO == false ) ? "site_projeto/" : "";
        $this->LINK                 = DOMINIO . DS . $this->LINK_DESPUBLICADO;
        $this->total                = SQLcontrole::total( $this->SQL );
    }


    /* função que adiciona parametros na busca */
    private function getParamentros( $parm = null )
    {
        return ( !empty( $parm ) ) ? $this->SQL .= " ". $parm : $this->SQL;
    }


    public function Listar()
    {

        $SQLstrT        = $this->getParamentros( " LIMIT ". $this->inicio ." , ".$this->total_mostrar );
        $consulta       = SQLcontrole::listar( $SQLstrT );                 
        $fetchAll       = $consulta->fetchAll(PDO::FETCH_OBJ);
        /* Variavel que vai conter todo o conteudo que irá retornar */
        $result    = null;

        if( $fetchAll ){

            foreach ( $fetchAll as $value ) {
                $result .= "ID - " . $value->id . "<br>";
            }

        }else{

            $result .= '<p align="center" >Não foram encontradas informações cadastradas!</p>';

        }


        return $result;
    }


    public function geraPaginacao()
    {

        $paginacao = new Paginacao( $this->paginaAtual, $this->total , $this->total_mostrar , $this->num_link );
        /* Transforma array recebida em URL SEO array( valor , valor ) */
        $paginacao->setParametros(array( 'teste' ));
        /* Transforma array recebida em URL SEO array( valor , valor ) */
        # $paginacao->setParametrosGET(array( 'busca' ,  $_POST['busca'] ));
        /* informa a url ou pagina que terá a paginacao */
        $paginacao->setUrl( $this->LINK ); //seta o nome da página ou nome do arquivo
        return $paginacao->gerarPaginacao();
    }



}