<?php
class Orcamento{

	public	$mensagem;
	public	$email_contato		= EMAIL;	//EMAIL
	public	$titulo_corpo		= "Formul&aacute;rio de orçamento";
	public	$data;
	public	$hora;
	
	private $titulo_cliente		= array();	//informações do titulo Nome:, Telefone: ...
	private $valores_cliente	= array();	//informações do cliente nome, telefone ...
	private $valores_produto	= array();	//informações dos produtos para orçamento

	
	
	/* Conta a quantidade de produtos preechidos para liberar o orçamento */
	public function setProdutos( $produtos_orcamento = null , $total = null ){
	
		foreach( $produtos_orcamento as $key => $val ):
			if(!empty($val) or $val > 0 ){ $total += $val; }
		endforeach;
		
		$libera	= $total == null ? false : true;
		
		if(!$libera){	
			$this->mensagem = "O preenchimento da quantidade de ao menos 1 produto é obrigatório!"; 
		}else{
			$this->valores_produto  = $produtos_orcamento ;
		}
		return $libera;
		
	}	
	
	
	/* Monta os títulos dos dados do cliente */
	public function setTitulo( $value ){
	
		array_push( $this->titulo_cliente , $value );
	
	}	
	
	
	/* Monta os valores dos dados do cliente*/
	public function setValor( $value ){
	
		array_push( $this->valores_cliente , $value );
	
	}	

	
	
	
	
	/* Monta as linhas dos campos do orçamento */
	private function getCampos(){
	
		for( $i=0; $i < sizeof( $this->titulo_cliente ); $i++ ){

		$nomes_campos 		= $this->titulo_cliente[$i];
		$valores_campos 	= $this->valores_cliente[$i];

		
		$monta_campos .= "<TR>
							 <TD vAlign=top align=right width=150 bgColor=#c0c0c0><B>$nomes_campos: </B></TD>
							 <TD width=450 bgColor=#e0e0e0>$valores_campos</TD>
						</TR>";
			
		}
	return $monta_campos;
	
	}	

	
	
	
	
	/* Monta as linhas dos produtos para o orçamento */
	private function getProdutos(){
	
		foreach( $this->valores_produto as $cod => $qtd  ){
			
			if( !empty($qtd) and $qtd != 0 ){
			$SQL 		= "SELECT titulo , codigo FROM produto WHERE id = ".$cod;
			$consulta	= SQLcontrole::listar($SQL);
			$row		= $consulta->fetch(PDO::FETCH_ASSOC);			
			$titulo 	= $row[titulo] . " - Código: ".$row[codigo];
			
			
			$monta_campos .= "<TR>
								<TD height='25'>$titulo</TD>
								<TD align='center'>$qtd</TD>
							  </TR>
							";
			}
		}
	return $monta_campos;
	
	}
	
	
	
	public function geraOrcamento(){
	
	
		$html_orcamento .="
				<!DOCTYPE HTML PUBLIC \'-//W3C//DTD HTML 4.0 Transitional//EN\'>
				<HTML>
				<HEAD>
				<META http-equiv=Content-Type content=\'text/html; charset=iso-8859-1\'>
				<STYLE>
				TD {
								FONT-SIZE: 10pt;
								FONT-FAMILY: Arial, Verdana;
				}
				</STYLE>

				<BODY bgColor=#ffffff>
				<TABLE cellSpacing=1 cellPadding=3 width=600 border=0 align=center>
				  <TBODY>
					<TR>
					  <TD align=middle bgColor=#555555 colSpan=2><FONT color=#ffffff><B>". $this->titulo_corpo ." - <FONT color=#ffffff>". DOMINIO ."</FONT></B></FONT></TD>
					</TR>";
					
		$html_orcamento .= $this->getCampos();

		$html_orcamento .="<TR>
					  <TD vAlign=top align=right width=150 bgColor=#c0c0c0><B>Or&ccedil;amento: </B></TD>
					  <TD width=450 bgColor=#e0e0e0>
					  <table width='450' border='0' cellspacing='0' cellpadding='0'>
						  <tr>
							<th width='325' align='left'>Nome Do Produto</th>
							<th width='125'  align='center'>Quantidade</th>
						  </tr>";
		
		$html_orcamento .= $this->getProdutos();
	
		$html_orcamento .="</table>
				</TD>
					</TR>
					<TR>
					  <TD bgColor=#555555 colSpan=2 align=\'center\' height=\'10\' ></TD>
					</TR>
				  </TBODY>
				</TABLE>
				<TABLE cellSpacing=1 cellPadding=3 width=600 border=0 align=center >
				  <TR align=center  >
					<TD>Mensagem enviada em:<B>$this->data</B> às <B>$this->hora</B> hrs.</TD>
				  </TR>
				</TABLE>
				</BODY>
				</HTML>
		";
	
	
	
		$mail = new PHPMailer();
		$mail->IsSMTP(); 						// Define que a mensagem será SMTP
		$mail->Host 	= "mail.".URL_CURTA;	// Endereço do servidor SMTP
		$mail->SMTPAuth = true;					// Usa autenticação SMTP? (opcional)
		$mail->Username = EMAIL_SMTP; 			// Usuário do servidor SMTP de teste
		$mail->Password = EMAIL_PASS; 			// Senha do servidor SMTP
		$mail->From 	= EMAIL; 				// Seu e-mail
		$mail->FromName = URL_CURTA; 			// Seu nome
		$mail->AddAddress(EMAIL);				//destinatário
		$mail->IsHTML(true);					// Define que o e-mail será enviado como HTML
		$mail->Subject  = DOMINIO; 				// Assunto da mensagem
		$mail->Body 	= $html_orcamento;		//html
		$enviado 		= $mail->Send();		//envia e-mail
		$mail->ClearAllRecipients();			//limpa os cach
		$mail->ClearAttachments();				//limpa os cach

		if($enviado):	$this->mensagem = "Orçamento enviado com sucesso!"; return true; 
		else: 			$this->mensagem = "Erro ao enviar orçamento, tente novamente!"; return false;
		endif;
		
	
	}
	


}


	
	