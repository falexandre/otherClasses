<?php
 

 /* 

******************	Usando a classe ********************************

//Inicia a Session
session_start();
 
//inclui a conexão
require('config/conexao.php');
 
//Include a classe Carrinho.php
require('classes/Carrinho.php');
 
//Instancia o Carrinho
$cart = new Carrinho();
 
//Verifica se irá adicionar produto ao carrinho
if(isset($_POST['acao']) && $_POST['acao'] == 'add'){
    /**
     *
     * Resgata o ID do produto
     * Resgata o ID da cor
     * Resgata a Quantidade
     *
     */
/*  $id     = $_POST['id'];
    $cor_id     = (isset($_POST['cor_id'])) ?$_POST['cor_id'] : null;
    $qtd        = $_POST['qtd'];
    //Adicionar o produto no carrinho
    $cart->adicionar($id, $qtd, $cor_id);
}
 
//Verifica se vai alterar o produto
if(isset($_POST['acao']) && $_POST['acao'] == 'alterar'){
    //Percorre o array da quantidade para resgatar
    // o indice e valor da quantidade
    foreach($_POST['qtd'] as $indice => $valor){
        $cart->alterar($indice, $valor);
    }
}
 
//Verifica para excluir o produto
if(isset($_GET['apagar'])){
    $cart->excluir($_GET['apagar']);
}
 
//retorna todos produtos do carrinho
$produtos = $cart->listarProdutos();
 
//retorna valor do total do carrinho
$total    = $cart->valorTotal(); 


<table>
    <thead>
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Preço</th>
            <th>SubTotal</th>
            <th>Excluir</th>
        </tr>
    </thead>
 
    <form action="" method="post">
        <tfoot>
            <tr>
                <td colspan="5">
                                 <input type="submit" value="Alterar o Carrinho" />
                                 <input type="hidden" name="acao" value="alterar" />
                </td>
            </tr>
        </tfoot>
 
        <tbody>
        <?php foreach($produtos as $indice => $valor) <img src="http://www.davidchc.com.br/wp-includes/images/smilies/icon_confused.gif" alt=":?" class="wp-smiley"> >
            <tr>
                <td><?php echo $row['produto']. ' - '.$row['cor']?></td>
                <td><input type="text" name="qtd[<?php echo $indice?>]"  value="<?php echo $valor['qtd']?>" /></td>
                <td>R$ <?php echo number_format($valor['preco'], 2, ',', '.')?></td>
                <td>R$ <?php echo number_format($valor['subtotal'], 2, ',', '.')?></td>
                <td><a href="carrinho.php?apagar=<?php echo $indice?>">Excluir</a></td>
            </tr>
        <?php endforeach;?>
        </tbody>
 
    </form>
 
 
</table>


****************** FIM Usando a classe ********************************

 */
 
 
 
final class Carrinho{
 
    /**
     *
     * Método construtor
     * Caso não existe a $_SESSION['carrinho_virtual_force'],
     * ele criar atribuindo um array vazio
     *
     */
	 
    public function __construct(){
        if(!isset($_SESSION['carrinho_virtual_force'])){
            $_SESSION['carrinho_virtual_force'] = array();
        }
    }
 
    /**
     *
     * Adiciona um produto ao carrinho
     *
     * Veja que criar uma chave, tendo a composição de 2 items:
     * o ID do produto e o ID da cor do produto
     * Essa maneira não irá sobreescrever quando quiser
     * vários produtos com cores diferentes.
     *
     * Um detalhe, se não tiver cor, ele atribui o valor zero,
     * facilitando na hora de verificar se tem cor ou não
     *
     * @param integer $id é código do produto adicionado
     * @param integer $qtd é  quantidade
     * @param integer $cor_id é a cor do produto
     *
     */
    public function adicionar($id, $qtd = 1, $cor_id=null){
        //verificar se o valor é nulo
        if(is_null($cor_id)){
            //Se for, monta o indice, sendo o valor para cor como 0
            $indice = sprintf('%s:%s', (int)$id, 0);
        }else{
            //Se existe um valor, atribui ao indice
            $indice = sprintf('%s:%s', (int)$id, (int)$cor_id);
        }
        /**
         * Se não existir esse indice no carrinho
         * Atribui ao carrinho com a quantidade
         */
        if(!isset($_SESSION['carrinho_virtual_force'][$indice])){
            $_SESSION['carrinho_virtual_force'][$indice] = (int)$qtd;
        }
    }
 
    /**
     *
     * Altera a quantidade do carrinho
     * O indice do array é a junção do ID do produto com o ID da cor
     *
     * @param string $indice é a chave do array
     * @param integer $qtd é quantidade para alterar
     */
    public function alterar($indice, $qtd){
        //verifica se existe esse indice
        if(isset($_SESSION['carrinho_virtual_force'][$indice])){
            //se o quantidade for maior que zero
            if($qtd > 0){
                //realiza a alteração
                $_SESSION['carrinho_virtual_force'][$indice] = (int)$qtd;
            }
        }
    }
 
    /**
     *
     * Excluir o produto do carrinho
     * @param string $indice
     */
    public function excluir($indice){
        //excluir o produto do carrinho
        unset($_SESSION['carrinho_virtual_force'][$indice]);
    }
 
    /**
     *
     * Retorna um array com os dados dos produtos no carrinho
     * @return array $result
     */
    public function listarProdutos(){
        //inicia a variável com array
        $result = array();
        foreach( $_SESSION['carrinho_virtual_force'] as $indice => $qtd ){
            //Separa o ID do produto do ID da cor
            list( $id , $cor_id )      = explode( ':', $indice );
 
            //Realizar a busca pelo produto, selecionado apenas o nome do produto e preço
            //$query_product              = mysql_query("SELECT produto, preco FROM produtos WHERE id = '$id'");
            $query_product              = BD::getConn()->query("SELECT titulo, preco FROM produtos WHERE id = '$id'");
			
            //retorna os itens do tabela produtos
            //$row_product                = mysql_fetch_assoc($query_product);
			$row_product 				= $query_product->fetch(PDO::FETCH_ASSOC);
            /**
             * Adicionar ao array o nome do produto, preço , quantidade, subtotal e a cor
             * È importante reparar que indice desse array será o mesmo
             * do carrinho.
             */
 
            $result[$indice]['produto'] = $row_product['titulo'];
            $result[$indice]['preco']   = $row_product['preco'];
            $result[$indice]['qtd']     = $qtd
            $result[$indice]['subtotal']= $row_product['preco'] * $qtd;
            $result[$indice]['cor']     = '';
 
            //Verifica se existe cor para o produto
            if($cor_id > 0) {
                //Faz a busca pela cor , seleciona apenas o nome da cor
                $query_cor              = BD::getConn()->query("SELECT cor FROM cores WHERE id = '$cor_id'");
 
                //retorna os itens do tabela cores
                $row_cor                = $query_cor->fetch(PDO::FETCH_ASSOC);
                //Adiciona o nome da cor
                $result[$indice]['cor'] = $row_cor['cor'];
            }
        }
 
        return $result;
    }
     
    /**
     *
     * Retorna o valor total do carrinho
     * Aqui apenas listaremos o produtos
     * somaremos o subtotal, que é
     * o preço vezes a quantidade
     * Isso já foi calculado no método listarProdutos
     */
    public function valorTotal(){
        //listar todos os produtos
        $produtos = $this->listarProdutos();
        //inicia a variável
        $total    = 0;
        //listar os produtos, para resgatar o subtotal
         
        foreach($produtos as $indice => $row){
            //realiza a soma
            $total += $row['subtotal'];
        }
         
        return $total;
         
    }
}
?>