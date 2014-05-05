<?php
 
/**
 
* Classe voltada para tratamento de erros.
 
* Class for handling errors.
 
*
 
* @license Classe livre. Poder ser editada e utilizada como quiser.
 
* @license Class free. Can be edited and used as you wish.
 
* @example require_once "Error.php"; $error = new Error();
 
*
 
* @author tiagobutzke
 
* @version 1.0
 
*
 
* 01/09/2010
 
*/
 
Class Error {
 
private $mailTo         = "tiago.butzke@gmail.com";
 
private $mailFrom       = "no-reply@gmail.com.br";
 
private $mailReplyTo    = "tiago.butzke@gmail.com";
 
private $mailMode       = 1;
 
private $userErrors     = array(
 
E_WARNING,
 
E_USER_ERROR,
 
E_USER_WARNING,
 
E_USER_NOTICE
 
);
 
private $errorType      = array (
 
E_ERROR => "ERRO FATAL",
 
E_WARNING => "ALERTA",
 
E_PARSE => "ERRO DE SINTAXE",
 
E_NOTICE => "AVISO",
 
E_CORE_ERROR => "ERRO DE PROCESSAMENTO",
 
E_CORE_WARNING => "ALERTA DE PROCESSAMENTO",
 
E_COMPILE_ERROR => "ERRO DE COMPIL&Ccedil;&Atilde;O",
 
E_COMPILE_WARNING => "ALERTA DE COMPILA&Ccedil;&Atilde;O",
 
E_USER_ERROR => "ERRO DO USU&Aacute;RIO",
 
E_USER_WARNING => "ALERTA DO USU&Aacute;RIO",
 
E_USER_NOTICE => "AVISO DO USU&Aacute;RIO",
 
E_STRICT => "AVISO ESTRITO"
 
);
 
private $ignoreErrors   = array(E_NOTICE, E_WARNING);
 
private $logMode        = 1;
 
private $logFile        = "error.xml";
 
private $logMsg;
 
private $redirectTo     = "leo.php";
 
private $t = "\t";
 
private $el = "\r\n";
 
private $oldHandler;
 
private $corpMsg;
 
public function Error() {
 
}
 
public function register() {
 
$this->oldHandler    = set_error_handler(array($this, "catchMyErrors"));
 
}
 
public function restore() {
 
set_error_handler($this->oldHandler);
 
return $this;
 
}
 
private function ignoreErrors() {
 
if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) && in_array($errno, $this->getIgnoreErrors())) {
 
return ;
 
}
 
}
 
private function setCorpMsg($var) {
 
$consCorpMsg    = array("CRDT", "CRERRNO", "CRERRORTYPE", "CRERRMSG", "CRFILENAME", "CRLINENUM");
 
$corp           = "<b>Informa&ccedil;&otilde;es sobre o erro:</b>".$this->el;
 
$corp           .= "<ul>".$this->el;
 
$corp           .= $this->t."<li>Data e hora: CRDT</li>".$this->el;
 
$corp           .= $this->t."<li>Tipo: CRERRNO CRERRORTYPE</li>".$this->el;
 
$corp           .= $this->t."<li>Descri&ccedil;&atilde;o: <small> CRERRMSG </small></li>".$this->el;
 
$corp           .= $this->t."<li>Arquivo: CRFILENAME</li>".$this->el;
 
$corp           .= $this->t."<li>Linha: CRLINENUM</li>".$this->el;
 
$corp           .= "</ul>";
 
$this->corpMsg   = str_replace($consCorpMsg, $var, $corp);
 
}
 
private function setLogMsg($var) {
 
$this->logMsg["line"]        = $var[0];
 
$this->logMsg["file"]        = $var[1];
 
$this->logMsg["message"]     = $var[2];
 
$this->logMsg["datetime"]    = $var[3];
 
//      $consLogMsg     = array("LGLINENUM", "LGFILENAME", "LGERRMSG", "LGDT");
 
//      $corp           = $this->t."line='LGLINENUM' file='LGFILENAME' message='LGERRMSG' datetime='LGDT'".$this->el;
 
//      $this->logMsg    = str_replace($consLogMsg, $var, $corp);
 
}
 
private function saveLog($errno) {
 
if (!file_exists($this->logFile))
 
$this->createFileLog();
 
$xml    = simplexml_load_file($this->logFile);
 
$exist  = false;
 
foreach ($xml->error as $errorLine)
 
if ($errorLine["code"] == $errno) {
 
$child  = $errorLine->addChild("details");
 
$this->createLogDetails($child);
 
$exist  = true;
 
break;
 
}
 
if (!$exist) {
 
$error  = $xml->addChild("error");
 
$error->addAttribute("code", $errno);
 
$child  = $error->addChild("details");
 
$this->createLogDetails($child);
 
}
 
file_put_contents($this->logFile, $xml->asXML());
 
}
 
private function createLogDetails($child) {
 
$child->addAttribute("line", $this->logMsg["line"]);
 
$child->addAttribute("file", $this->logMsg["file"]);
 
$child->addAttribute("message", $this->logMsg["message"]);
 
$child->addAttribute("datetime", $this->logMsg["datetime"]);
 
}
 
private function createFileLog() {
 
$corp   = "<?xml version='1.0' encoding='ISO-8859-1'?>".$this->el;
 
$corp   .= $this->t.'<errors>'.$this->el;
 
$corp   .= $this->t.'</errors>';
 
$file   = fopen($this->logFile, "w+");
 
fwrite($file, $corp);
 
fclose($file);
 
}
 
private function sendMail() {
 
$subject    = "Alerta de erro em: ".$_SERVER['REQUEST_URI'];
 
$headers    = "MIME-Version: 1.0".$this->el;
 
$headers    .= "Content-type: text/html; charset=ISO-8859-1".$this->el;
 
$headers    .= 'To: '. $this->mailTo.$this->el;
 
$headers    .= 'From: '. $this->mailFrom .$this->el;
 
mail($this->mailTo, $subject, $this->corpMsg, $headers);
 
}
 
private function redirect() {
 
header('Location:'.$this->redirectTo);
 
}
 
public function catchMyErrors($errno, $errmsg, $filename, $linenum, $vars) {
 
try {
 
$this->ignoreErrors();
 
$dt = date("Y-m-d H:i:s");
 
$errmsg = $str = mb_convert_encoding($errmsg, "ISO-8859-1", "ASCII,JIS,UTF-8,ISO-8859-1");
 
$this->setCorpMsg(array($dt, $errno, $this->errorType[$errno]), $errmsg, $filename, $linenum);
 
if (in_array($errno, $this->userErrors)) {
 
if ($this->logMode == 1) {
 
$this->setLogMsg(array($linenum, $filename, $errmsg, $dt));
 
$this->saveLog($errno);
 
}
 
if ($this->mailMode == 1) {
 
$this->sendMail();
 
}
 
$this->redirect();
 
}
 
}catch (Exception $e) {
 
$this->restore();
 
}
 
}
 
}