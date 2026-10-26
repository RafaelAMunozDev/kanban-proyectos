<?php

session_start();
$idioma = $_SESSION['lang'];

$archivo = file_exists( "../idiomas/$idioma.json" ) ?
			"../idiomas/$idioma.json" :
			"../idiomas/es.json";

$contenido = file_get_contents( $archivo );
$palabras = json_decode( $contenido, true );
///////////////////////////////////////////////////////////////
//ESTA VARIABLE LA UTILIZO PARA DAR UNA RUTA RELATIVA EN LOS ENLACES
$rutarela= strtok($_SERVER["REQUEST_URI"], "?");
//controlador
$chathtml = './chats/'.$_SESSION['chathtml'];
$vista=$_GET["status"];

if (isset($vista)){
	
	$vista=$vista;
	
}else {
		
	$vista=1;
}

if ( $vista == 1) 
{
    $text = $_POST['text'];
	
	if ( $text != " " ){

	
	$aleatorio=rand(5,77777).substr(microtime(true),0,-5);
	$aleatorio2=rand(5,77777).substr(microtime(true),0,-5);
	
	$text_message = "
	<div class='msgln' id='$aleatorio2'><span class='chat-time'>".date("d/m g:i A ")."</span><br><button onclick='copiarAlPortapapeles($aleatorio)'><img class='imgcopy' title='".$palabras['chat']['copiar']."' src='../images/copy.png'></button><span id='$aleatorio'> ".stripslashes($text)."</span><a href='post.php?status=2&id=$aleatorio2'><img class='imgeliminar' title='".$palabras['chat']['eliminar']."' src='../images/eliminar.png'></a></div>";

    file_put_contents($chathtml, $text_message, FILE_APPEND | LOCK_EX);

	}
} 
else if ($vista == 2) 
{
	$id=$_GET["id"];
	
	$lineas = file($chathtml);
	
	for ($i=0;$i<count($lineas);$i++) {
		$codigo = htmlspecialchars($lineas[$i]);
		$pos = strpos($codigo, $id);
		
		if ($pos == false)
		{
			$entero = $entero.$lineas[$i];
			$_SESSION["ficherobueno"]=$entero;
		}
	}
	
	$entero = $_SESSION["ficherobueno"];
	file_put_contents($chathtml,$entero);
	unset($_SESSION["ficherobueno"]);
	
	echo'<script type="text/javascript">alert("'.$palabras['chat']['eliminadocorrectamente'].'");window.location.href="index.php";</script>';

}

?>
