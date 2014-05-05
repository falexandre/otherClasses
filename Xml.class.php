<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relatório</title>
</head>

<body>

<?php

$xml = "relatorio.xml";
$rel = simplexml_load_file($xml);


 ?>
<table width="100%" border="1" align="center" cellpadding="1" cellspacing="1" style="font-size:14px">
<tr>
<td width="16%" align="center" bgcolor="#CCCCCC"><strong>Endereço</strong></td>
<td width="15%" align="center" bgcolor="#CCCCCC"><strong>Cnpj</strong></td>
<td width="4%" align="center" bgcolor="#CCCCCC"><strong>Matricula</strong></td>
<td width="9%" align="center" bgcolor="#CCCCCC"><strong>Nome</strong></td>
<td width="10%" align="center" bgcolor="#CCCCCC"><strong>Cidade</strong></td>
<td width="4%" align="center" bgcolor="#CCCCCC"><strong>Uf</strong></td>
<td width="6%" align="center" bgcolor="#CCCCCC"><strong>Cep</strong></td>
<td width="13%" align="center" bgcolor="#CCCCCC"><strong>E-mail</strong></td>
<td width="19%" align="center" bgcolor="#CCCCCC"><strong>Telefone</strong></td>
<td width="4%" align="center" bgcolor="#CCCCCC"><strong>Contato</strong></td>
</tr>
<tr>
<?php


?>
</table>
</body>
</html>