<?php
class Email{

	public	$mensagem;
	public	$email_contato		= EMAIL;	//EMAIL
	public	$from				= EMAIL;	//EMAIL
	public	$titulo_corpo		= "Formul&aacute;rio de contato";
	public	$assunto			= DOMINIO;
	public	$data;
	public	$hora;
	
	private $titulo_cliente		= array();	//informações do titulo Nome:, Telefone: ...
	private $valores_cliente	= array();	//informações do cliente nome, telefone ...

	
	
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

	
	
	
	
	public function geraEmail(){
	
	
		$html_email .="
		<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
		<HTML><HEAD>
		<META http-equiv=Content-Type content=\"text/html; charset=iso-8859-1\">
		<STYLE>TD {
		FONT-SIZE: 10pt; FONT-FAMILY: Arial,Verdana;
		}
		</STYLE>

		<BODY bgColor=#ffffff>
		<TABLE cellSpacing=1 cellPadding=3 width=600 border=0 align=center>
			<TBODY>
				   <TR>
					 <TD align=middle bgColor=#555555 colSpan=2><FONT
								   color=#ffffff><B>". $this->titulo_corpo ." - <FONT
								   color=#ffffff>".DOMINIO."</FONT></B></FONT></TD>
				   </TR>";
				   
		$html_email .= $this->getCampos();		   
				   
		$html_email .="		   
					<TR>
					 <TD bgColor=#555555 colSpan=2 align=\"center\" height=\"10\" >
					 </TD>
				   </TR>
		</TBODY>
			</TABLE>
			<TABLE cellSpacing=1 cellPadding=3 width=600 border=0 align=center >
				<TR align=center  >
				  <TD>Mensagem enviada em:<B>". $this->data . "</B> às <B>". $this->hora ."</B> hrs.</TD>
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
		$mail->From 	= $this->from; 				// Seu e-mail
		$mail->FromName = URL_CURTA; 			// Seu nome
		$mail->AddAddress($this->email_contato);		//destinatário
		$mail->IsHTML(true);					// Define que o e-mail será enviado como HTML
		$mail->Subject  = $this->assunto; 		// Assunto da mensagem
		$mail->Body 	= $html_email;		//html
		$enviado 		= $mail->Send();		//envia e-mail
		$mail->ClearAllRecipients();			//limpa os cach
		$mail->ClearAttachments();				//limpa os cach

		if($enviado):	$this->mensagem = "E-mail enviado com sucesso!"; return true; 
		else: 			$this->mensagem = "Erro ao enviar e-mail, tente novamente!"; return false;
		endif;
		
	
	}
	


}


	
	