<?php
session_start();


if( isset( $_SESSION['lang'] ) ){
	$idioma = $_SESSION['lang'];
}else{
	$idioma = 'es';
}

$archivo = file_exists( "idiomas/$idioma.json" ) ?
			"idiomas/$idioma.json" :
			"idiomas/es.json";

$contenido = file_get_contents( $archivo );
$palabras = json_decode( $contenido, true );
///////////////////////////////////////////////////////////////
//ESTA VARIABLE LA UTILIZO PARA DAR UNA RUTA RELATIVA EN LOS ENLACES
$rutarela= strtok($_SERVER["REQUEST_URI"], "?");

//controlador de vistas, este depende del status de la url
	
$vista=@$_GET["status"];

if (isset($vista)){
	
	$vista=$vista;
	
}else {
		
	$vista=2;
}
?>

<!-- Funcion que pregunta si estas seguro a la hora de deshabilitar un proyecto -->
<script type="text/javascript">

	function ConfirmDelete(idproyecto)
	{

		var confirmar = "<?php echo $palabras['vista8']['confirmardelete']; ?>";
		var eliminar = "<?php echo $palabras['vista8']['cancelardelete']; ?>";
		
		var respuesta = confirm(confirmar);
		
		if (respuesta == true ){
				
				window.location.href = "<?php echo strtok($_SERVER["REQUEST_URI"], "?"); ?>?status=5&id="+idproyecto;
		}else{
				alert(eliminar);
				window.location.href = "<?php echo strtok($_SERVER["REQUEST_URI"], "?"); ?>";
		}
	}
	
	function cambiaridioma(l)
	{

		window.location.href = "<?php echo strtok($_SERVER["REQUEST_URI"], "?"); ?>?status=7&l="+l;

	}
	
	function EditProyect(idproyecto)
	{
		window.location.href = "<?php echo strtok($_SERVER["REQUEST_URI"], "?"); ?>?status=8&id="+idproyecto;

	}
	
	function comentarios()
	{
		window.location.href = "./chat/";

	}

</script>

<!DOCTYPE html>
<html style="background-color: #e2dbb0;" lang="en">
	<head>
		<meta name="viewport" content="width=device-width" initial-scale=1>
		<meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>kanban</title>
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/kanban.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>
		<link rel="shortcut icon" type="image/x-icon" href="./images/icono.png">
	</head>
	<body>
<?php 
if ( $vista == 1) 
{
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);

?>	
		
		<img class="imglogo" src="./images/logo.png"/>
		<!-- Imagen de visualizar los proyectos deshabilitados -->
		<div style="float:right;margin-top:20px;margin-right:20px;">
			<a href="javascript:cambiaridioma('es')"><img style="width:50px; height:30px;background-color: transparent;"src="./images/es.svg"></a>
			<a href="javascript:cambiaridioma('en')"><img style="width:50px; height:30px; background-color: transparent;"src="./images/en.svg"></a>
			<a href="javascript:cambiaridioma('fr')"><img style="width:50px; height:30px; background-color: transparent;"src="./images/fr.svg"></a>
		</div>
		<div class="centered" id="title">
			<h1>KANBAN</h1>
			<p class="h3"><?php echo $palabras['vista1']['seleccion_proyecto']; ?></p>
		</div>
		<div style="display: grid;justify-content: center; align-items: center;">
					<!-- Bucle para el recorrido de proyectos, si el estado esta activo pinta el div
					Este div esta compuesto de:
					-Boton de enlace a la vista 2 para el kanban
					-Boton de que llama a la funcion que pregunta para deshabilitar el proyecto, (si dices que si lo deshabilita)
					-->
					<?php
					for($i=0;$i<count($proyectos["proyectos"]);$i++) 
					{
						$idproyecto = $proyectos["proyectos"][$i]["id"];
						$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
						$descproyecto = $proyectos["proyectos"][$i]["description"];
						$estado = $proyectos["proyectos"][$i]["estado"];
						
						if ( $estado == "activo" ) {
							$totalestado=1;
							$_SESSION['totalestado'] = $totalestado;
							echo "
							<div style=\"display: flex;\">
							<a style=\"margin-bottom:10px;width: 250px;margin-right:10px;\" class=\"btn btn-danger btn-sm\" href=\"./index.php?status=2&idproyecto=$idproyecto\">$nombreproyecto - $descproyecto</a>
							<button onClick='EditProyect($idproyecto)' class=\"btn btn-primary btn-sm\" style=\"margin-bottom:10px; margin-right: 6px;\" ><img style=\"height: 16px;width: 16px; \"title='".$palabras['vista1']['editar']."' src='./images/edit.png'></button>
							</div>";
							
						}
					}
					
					@$totalestado = $_SESSION['totalestado'];
					
					if ( $totalestado != 1 ) {
						
						echo "<div style=\"display: flex;\">
							<p class=\"nohayproyectos\">".$palabras['vista1']['nohayproyectosactivos']."<p>
							</div>";
						
					}
					$_SESSION['totalestado'] = null;
					?>
		</div>
		<!-- Boton con enlace a la vista 3 para crear un proyecto -->
		<div style="display: flex;justify-content: center; align-items: center;">
				<a style="margin-right:230px; margin-bottom:10px; margin-top:7px;" class="btn btn-info btn-sm" href="./index.php?status=3"><?php echo $palabras['vista1']['crearproyecto']; ?></a><br />
				<a href="<?php echo $rutarela ;?>?status=6"><img class="imgvisibility" title="<?php echo $palabras['vista1']['visualizartodoslosproyectos']; ?>" src="./images/visibility.png"/></a>
		</div>

<?php	
} 
else if ($vista == 2) 
{
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	@$idproyectoseleccionado=$_GET["idproyecto"];
	
	if(isset($idproyectoseleccionado)){
		$idproyectoseleccionado=$idproyectoseleccionado;
	}else {
		for($i=0;$i<count($proyectos["proyectos"]);$i++) 
		{
			$pordefecto = $proyectos["proyectos"][$i]["pordefecto"];
			
			if ( $pordefecto == "on") {
				$idproyectoseleccionado = $proyectos["proyectos"][$i]["id"];
			}
		}
	}
	
	for($i=0;$i<count($proyectos["proyectos"]);$i++) 
	{
		$idproyecto = $proyectos["proyectos"][$i]["id"];
								
		if ( $idproyectoseleccionado == $idproyecto ) {
			
			$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
			$descproyecto = $proyectos["proyectos"][$i]["description"];
			$kanbanjson = $proyectos["proyectos"][$i]["kanbanjson"];
			$chathtml = $proyectos["proyectos"][$i]["chathtml"];			
			$columnas = $proyectos["proyectos"][$i]["columnas"];
		}
	}

	// Cargo los datos del proyecto que necesito dependiendo del ID del proyecto que hayamos pasado
	
	// Metemos estas variable en sessiones para tener los valores en el archivo "kanban.php"
	$_SESSION['idproyectoseleccionado'] = $idproyectoseleccionado;
	$_SESSION['nombreproyecto'] = $nombreproyecto;
	$_SESSION['kanbanjson'] = $kanbanjson;
	$_SESSION['chathtml'] = $chathtml;
	$columnasdivididas = explode("#", $columnas);
	
?>
	<!-- Cuadro azul situado arriba a la izquierda para ver que proyecto se ha cargado y que fichero se utiliza -->
	<div style="display: flex;">
	<p style="height: 80%; padding: 10px;width: auto; margin-top: 0px;background-color: #dddddd;" class="p-3 mb-2 text-white h5"><?php echo $palabras['vista2']['descripcion']." ".$descproyecto; ?></p>	
	<!--Imagen de volver -->
	<figure style="margin-right: 80px; margin-left: 10px; margin-top: 5px;" class="image is-24x24">						
			<a href="index.php?status=1"><img class="rounded" src="images/volver.png" height ="25" width="25" title="<?php echo $palabras['vista2']['volver']; ?>"/></a> 
	</figure>
	<!-- Boton de añadir tabla-->
	<a href="" id="newitem"><img style="width:80px;height:35px;margin-left:7px; margin-bottom:13px;padding:3px;" src="./images/NuevaTarea.png"/></a>
		<!-- Boton para ir al chat situado en la carpeta ./chat ( EL CHAT CARGARA SU FICHERO .HTML DETERMINADO DEPENDIENDO DEL PROYECTO) -->
	<a href="javascript:comentarios()"><img style="width:80px;height:35px;margin-left:7px; margin-bottom:13px;padding:3px;" src="./images/anotaciones.png"/></a>
	</div>
	
	<div class="centered" id="title">
		<h1 style="margin-right: 50px; font-weight: bold;">Kanban <?php echo $nombreproyecto ;?></h1></div><div style=" display: grid; justify-content: center;align-items: center;" id="wastebucket"><?php echo $palabras['vista2']['arrastraeliminar']; ?></div>
		<div class="grid">
			<div class="container" id="idea"><?php echo $columnasdivididas[0]; ?></div>
			<div class="container" id="todo"><?php echo $columnasdivididas[1]; ?></div>
			<div class="container" id="deliver"><?php echo $columnasdivididas[2]; ?></div>
			<div class="container" id="review"><?php echo $columnasdivididas[3]; ?></div>
		</div>
		
		<!-- Carga el archivo "kanban.js" que es el que llama a las funciones para posteriormente pasarselas al archivo "kanban.php" -->
		<script src="kanban.js"></script>
		<script>
		
		//Esta funcion rellena las tablas con sus respectivos valores para luego pintarlos
			var item = function (id,table,title,description,date_string) {
				
				//Aqui si la palabra es muy grande ( mayor que 42 caracteres ) se divide para meter un intro llamando a la duncion dividirpalabra
				var words = description.split(" ");
				var cadena="";
				for (var i = 0; i <= words.length ; i++) {

					if(i != words.length){
						var palabragrande=words[i]
						if(palabragrande.length > 32){
							cadena += dividirpalabra(words[i]);
						} else {
							cadena += words[i]+" ";
						}
					}
				}	

				var description = cadena;
				var description2 = description;
				var description2 = description2.replaceAll(/\n/g," ");
				var description = description.replaceAll(/\n/g,"<br>");
				var self = {};
				self.id = id;
				self.table = table;
				self.title = title;
				self.description = description.split(/<p>|<\/p>|-/); // array
				self.date_string = date_string;

				self.draw = function () {

				var item_div = $('<div></div>');
				var item_title = $('<h4></h4>');
				var item_date = $('<div></div>');
				var item_button = $('</br><a href="kanban.php?status=2&id='+id+'&table='+table+'&title='+title+'&description='+description2+'&date_string='+date_string+'"><?php echo $palabras['vista2']['editar'] ;?></a>');
				item_div.attr('class','item centered');
				item_div.attr('id',self.id);
				item_title.attr('class','item-h4');
				item_date.attr('class','item-date');
				item_title.html(self.title);
				item_date.html(self.date_string);
				item_div.append(item_title);
				item_div.append(item_date);
				item_button.attr('class','btn btn-xs item-edit');
				item_button.html(self.item_button);
				item_div.append(item_button);
				
				self.description.forEach(function(element) {

				  var item_description = $('<p></p>');
				  item_description.attr('class','item-p');
				  element=element.trim();
				  if (element != "") {
					item_description.html(''+element);
					item_div.append (item_description);

				  }

				});

				$('#'+self.table).append (item_div);

			  }

			  var draw = self.draw();

			  return self;

			}

			// Funciones para pintar y rellenar las tablas
			// Parametros: id unico (generado por la funcion), table es la columna donde se pinta, title, description (se divide por los guiones '-' en varios <p>) y date_string con formato 16/Jun
			function buildGrid () {

			  $.getJSON('./json/<?php echo $kanbanjson; ?>', function (data) {
				   
					  for (i=0;i<data.idea.length;i++){ new item (data.idea[i].id,'idea',data.idea[i].title,data.idea[i].description,data.idea[i].date_string) }
					  for (i=0;i<data.todo.length;i++){ new item (data.todo[i].id,'todo',data.todo[i].title,data.todo[i].description,data.todo[i].date_string) }
					  for (i=0;i<data.deliver.length;i++){ new item (data.deliver[i].id,'deliver',data.deliver[i].title,data.deliver[i].description,data.deliver[i].date_string) }
					  for (i=0;i<data.review.length;i++){ new item (data.review[i].id,'review',data.review[i].title,data.review[i].description,data.review[i].date_string) }
				
			  });

			  colorItems();

			}

			function colorItems () {

			  var colors=["#f2777a","#6699cc","#99cc99","#ffcc66","#cc99cc", "#66cccc", "#f99157", "#aaabbb"];

			  console.log($(".item"));

			  $("div").each(function(j) {

				console.log("item");
				$("#040222525127").css({"background-color": colors[Math.floor(Math.random() * colors.length)]});

			  });

			}

			// Pintamos las tablas una vez al cargar la pagina, despues se vuelven a pintar solas cada vez que se llama por ajax
			buildGrid();
			colorItems();
		</script>
<?php
}
else if ($vista == 3) 
{
?>
	<!-- Cargo la hoja de estilos de la pagina de bulma -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
	<img class="imglogo" src="./images/logo.png"/>
	<div class="label columns is-mobile is-justify-content-center">
		<h2 class="title is-1 is-size-4-mobile has-text-centered mr-6 is-underlined has-text-danger"><?php echo $palabras['vista3']['crearproyecto']; ?></h2>
	</div>
	<!-- Formulario para dar un nombre y una descripcion al nuevo proyecto -->
	<div class="columns is-mobile is-justify-content-center mt-4 mr-5">
		<form action="<?php echo $rutarela ;?>?status=4" method="POST">
			<label class="label" for="proyecto"><?php echo $palabras['vista3']['proyecto']; ?></label>
			<input style="margin-left: 30px; width : 150px; heigth : 150px" maxlength="12" class="input" type="text" name="proyecto" placeholder="<?php echo $palabras['vista3']['placeproyecto']; ?>" required><br>
			<label class="label" for="description"><?php echo $palabras['vista3']['descripcion']; ?></label>&nbsp;
			<input style="margin-right: 5px; width : 200px; heigth : 100px" maxlength="22" class="input" type="text" name="description" placeholder="<?php echo $palabras['vista3']['placedescripcion']; ?>" required><br>
			<!--35-->	
			<label class="label" for="columnas"><?php echo $palabras['vista3']['columnas']; ?><p style="color: red;font-size: 10px;"><?php echo $palabras['vista3']['placecolumnas']; ?></p></label>&nbsp;
			<input style="margin-right: 5px; width : 200px; heigth : 100px"  class="input" type="text" name="columnas" placeholder="Ej: aaa#aaa#aaa#aaa" value="Nueva#URGENTE#En Proceso#Finalizado" pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}" required><br>
			<p style="margin-left: 55px;font-weight:bold;" ><?php echo $palabras['vista3']['pordefecto']; ?></p><input style="margin-left: 103px;width:15px;height:15px;" type="checkbox" name="pordefecto"/><br>
			<button class="button is-danger is-rounded mt-3" style="margin-left:25px" type="Submit"><?php echo $palabras['vista3']['crearproyecto']; ?></button>
		</form> 
	</div>
	<div class="columns is-mobile is-justify-content-center">
		<!-- IMAGEN DE VOLVER -->
		<figure style="margin-right: 80px" class="image is-24x24 is-justify-content-center">						
			<a href="<?php echo $rutarela; ?>?status=1"><img class="rounded" src="images/volver.png" height ="40" width="40" title="<?php echo $palabras['vista3']['volver']; ?>"/></a> 
		</figure>
		&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;	
	</div>

<?php
}
else if ($vista == 4) 
{
	$nombreproyecto = $_POST['proyecto'];
	$descproyecto = $_POST['description'];
	$columnas = $_POST['columnas'];
	$columnasdivididas = explode("#", $columnas);
	@$pordefecto = $_POST['pordefecto'];
	
	if ( $pordefecto == "on") {
		$pordefecto = $pordefecto;
	}else {
		$pordefecto = "off";
	}

	//Recogemos el nombre y la descripcion, ademas generamos un id con factores aleatorios pero con la funcion microtime, nunca se van a repetir
	$id = rand(5,77777).substr(microtime(true),0,-5);
	//Leemos el fichero proyectos.json
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	$existe="no";
	//Pequeño bucle para asegurar de que el nuevo id no existe (Para comprobar)
	for($i=0;$i<count($proyectos["proyectos"]);$i++) 
	{
		$proyectoexistente = $proyectos["proyectos"][$i]["id"];
		if ( "$id" == $proyectoexistente )
		{
			$existe = "si";
			break;
		}
	}
	
	if ( $existe == "si") {
		//Si existe creamos otra vez el valor del id
		$id = rand(5,77777).substr(microtime(true),0,-5);
	}
	
	//Creamos los ficheros para el json y para el chat a traves del id generado
		
	$kanbanjson = $id.'.json';
	$chathtml = $id.'.html';
	
	//Utilizamos estas variables para insertar en el fichero "proyectos.json" el nuevo proyecto
	
	$index=count($proyectos["proyectos"]);
	
	@$proyectos["proyectos"][$index]['id']=$id;
	@$proyectos["proyectos"][$index]['proyecto']=$nombreproyecto;
	@$proyectos["proyectos"][$index]['description']=$descproyecto;
	@$proyectos["proyectos"][$index]['kanbanjson']=$kanbanjson;
	@$proyectos["proyectos"][$index]['chathtml']=$chathtml;
	@$proyectos["proyectos"][$index]['columnas']=$columnas;
	@$proyectos["proyectos"][$index]['pordefecto']=$pordefecto;
	@$proyectos["proyectos"][$index]['estado']="activo";

	$newJsonString = json_encode($proyectos);
	file_put_contents('./json/proyectos.json',$newJsonString);
	
	//Estas variables y funciones nos permiten crear los ficheros para el kanban y el chat en sus respectivas carpetas
	
	$archivojson = fopen("./json/".$kanbanjson, "w+b");
	
	if( $archivojson == false ) {
		echo "Error al crear el archivo json";
	}
	else
	{
		$escribir = '{"review":[],"done":[],"deliver":[],"todo":[],"idea":[]}';
		fwrite($archivojson,$escribir);
		// Fuerza a que se escriban los datos pendientes en el buffer:
		fflush($archivojson);
	}
	
	fclose($archivojson);
	
	$archivohtml = fopen("./chat/chats/".$chathtml, "w+b");
	
	if( $archivohtml == false ) {
		echo "Error al crear el archivo html";
		die();
	}
	
	fclose($archivohtml);
	
	if ( $pordefecto == "on") {
		echo '<script language="javascript">window.location.href="'.$rutarela.'?status=10&id='.$id.'";</script>';
	}else{
		echo '<script language="javascript">alert("'.$palabras['vista4']['crearproyectocorrecto'].'");window.location.href="'.$rutarela.'?status=1";</script>';
	}
}
else if ($vista == 5) 
{
	//Recogemos el id por la url para asignarselo a la variable y abrimos el fichero "proyectos.json"
	$idproyectoeliminar = $_GET['id'];
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	// Cargo los datos del proyecto que necesito dependiendo del ID del proyecto que hayamos pasado
	
	for($i=0;$i<count($proyectos["proyectos"]);$i++) 
	{
		$idproyecto = $proyectos["proyectos"][$i]["id"];
										
		if ( $idproyectoeliminar == $idproyecto ) {
			// GUARDO LA POSICION DEL ARRAY QUE SE ENCUENTRA EL PROYECTO Y LAS VARIABLES CON SU VALOR CORRESPONDIENTE
			$posicionarray = $i;
			$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
			$descproyecto = $proyectos["proyectos"][$i]["description"];
			$kanbanjson = $proyectos["proyectos"][$i]["kanbanjson"];
			$chathtml = $proyectos["proyectos"][$i]["chathtml"];
			$pordefecto=$proyectos["proyectos"][$i]["pordefecto"];
			$columnas = $proyectos["proyectos"][$i]["columnas"];			
			
		}
	}
	// Elimino el proyecto a traves de la posicion que tenga
    unset($proyectos["proyectos"][$posicionarray]);
	
	$proyectos["proyectos"] = array_values($proyectos["proyectos"]);
	
    $newJsonString = json_encode(($proyectos));
    file_put_contents('./json/proyectos.json',$newJsonString);
	
	// Cargo de nuevo el fichero y añado los datos otra vez pero cambiando el estado a "inactivo"
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	$index=count($proyectos["proyectos"]);
		
		$proyectos["proyectos"][$index]['id']="$idproyectoeliminar";
		$proyectos["proyectos"][$index]['proyecto']=$nombreproyecto;
		$proyectos["proyectos"][$index]['description']=$descproyecto;
		$proyectos["proyectos"][$index]['kanbanjson']=$kanbanjson;
		$proyectos["proyectos"][$index]['chathtml']=$chathtml;
		$proyectos["proyectos"][$index]['columnas']=$columnas;
		$proyectos["proyectos"][$index]['pordefecto']=$pordefecto;
		$proyectos["proyectos"][$index]['estado']="inactivo";
	
	$newJsonString = json_encode($proyectos);
	file_put_contents('./json/proyectos.json',$newJsonString);
	
	echo '<script language="javascript">alert("'.$palabras['vista5']['deshabilitarproyectocorrecto'].'");window.location.href="'.$rutarela.'?status=1";</script>';

}
else if ($vista == 6) 
{
		
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
?>
		
		<img class="imglogo" src="./images/logo.png"/>
		<div class="centered" id="title">
			<h1>KANBAN</h1>
		</div>
		<div style="padding-right:150px;padding-bottom:10px;" class="centered" id="title">
			<p class="h3"><?php echo $palabras['vista6']['todoslosproyectos']; ?></p>
		</div>
		<div style="display: grid;justify-content: center; align-items: center;">
					<!-- Bucle para el recorrido de proyectos, si el estado esta activo pinta el div
					Este div esta compuesto de:
					-Boton de enlace a la vista 2 para el kanban
					-Boton de que llama a la funcion que pregunta para deshabilitar el proyecto, (si dices que si lo deshabilita)
					-->
					<?php
					for($i=0;$i<count($proyectos["proyectos"]);$i++) 
					{
						$idproyecto = $proyectos["proyectos"][$i]["id"];
						$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
						$descproyecto = $proyectos["proyectos"][$i]["description"];
						$estado = $proyectos["proyectos"][$i]["estado"];
						
						if ( $idproyecto > 1 ) {
							
							if ( $estado == "activo" ) {
							
								echo "
								<div style=\"display: flex;\">
								<a style=\"margin-bottom:10px;width: 250px;margin-right:10px;\" class=\"btn btn-danger btn-sm\" href=\"./index.php?status=2&idproyecto=$idproyecto\">$nombreproyecto - $descproyecto</a>
								<img style=\"background-color: green; padding: 1%;border-radius:2em; margin-top:2px; height: 25px;width: 25px;\" title=".$palabras['vista6']['habilitado']." src='./images/activo.png'>
								</div>";
								
							}else{
								
								echo "
								<div style=\"display: flex;\">
								<a style=\"margin-bottom:10px;width: 250px;margin-right:10px;\" class=\"btn btn-danger btn-sm\" href=\"./index.php?status=2&idproyecto=$idproyecto\">$nombreproyecto - $descproyecto</a>
								<img style=\"background-color: red; ; padding: 1%;border-radius:2em; margin-top:2px; height: 25px;width: 25px;\" title=".$palabras['vista6']['deshabilitado']." src='./images/inactivo.png'>
								</div>";
								
							}
						}
					}
					?>
					
		</div>
		<!-- Boton con enlace a la vista 3 para crear un proyecto -->
		<div style="display: grid;justify-content: center; align-items: center;">
			<figure style="margin-right: 220px" class="image is-24x24 is-justify-content-center">						
				<a href="<?php echo $rutarela; ?>?status=1"><img class="rounded" src="images/volver.png" height ="20" width="20" title="<?php echo $palabras['vista6']['volver']; ?>"/></a> 
				&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;	
			</figure>
		</div>
		

<?php
}
else if ($vista == 7) 
{
	$_SESSION['lang'] = $_GET['l'];
	
	switch ($_SESSION['lang']) {
    case "es":
        echo '<script language="javascript">alert("El idioma se ha cambiado al Español");window.location.href="'.$rutarela.'";</script>';
        break;
    case "fr":
        echo '<script language="javascript">alert("La langue a été changée en Français ");window.location.href="'.$rutarela.'";</script>';
        break;
    case "en":
        echo '<script language="javascript">alert("The language has been changed to English");window.location.href="'.$rutarela.'";</script>';
        break;
	}
}
else if ($vista == 8) 
{
	//Recogemos el id por la url para asignarselo a la variable y abrimos el fichero "proyectos.json"
	$idproyectoseleccionado = $_GET['id'];
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
		// Cargo los datos del proyecto que necesito dependiendo del ID del proyecto que hayamos pasado
	
	for($i=0;$i<count($proyectos["proyectos"]);$i++) 
	{
		$idproyecto = $proyectos["proyectos"][$i]["id"];
										
		if ( $idproyectoseleccionado == $idproyecto ) {
			// GUARDO LA POSICION DEL ARRAY QUE SE ENCUENTRA EL PROYECTO Y LAS VARIABLES CON SU VALOR CORRESPONDIENTE
			$posicionarray = $i;
			$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
			$descproyecto = $proyectos["proyectos"][$i]["description"];
			$columnas = $proyectos["proyectos"][$i]["columnas"];
			$estado = $proyectos["proyectos"][$i]["estado"];
			$chathtml = $proyectos["proyectos"][$i]["chathtml"];
			$pordefecto=$proyectos["proyectos"][$i]["pordefecto"];
			$kanbanjson = $proyectos["proyectos"][$i]["kanbanjson"];
		}
	}
	$pordefectoviejo=$pordefecto;
?>
	<!-- Cargo la hoja de estilos de la pagina de bulma -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
	<img class="imglogo" src="./images/logo.png"/>
	<div class="label columns is-mobile is-justify-content-center">
		<h2 class="title is-1 is-size-4-mobile has-text-centered mr-6 is-underlined has-text-danger"><?php echo $palabras['vista8']['editarproyecto']; ?></h2>
	</div>
	<!-- Formulario para dar un nombre y una descripcion al nuevo proyecto -->
	<div class="columns is-mobile is-justify-content-center mt-4 mr-5">
		<form action="<?php echo $rutarela ;?>?status=9" method="POST">
			<input type="hidden" name="idproyecto" value="<?php echo $idproyectoseleccionado;?>" />
			<input type="hidden" name="posicionarray" value="<?php echo $posicionarray;?>" />
			<input type="hidden" name="chathtml" value="<?php echo $chathtml; ?>" />
			<input type="hidden" name="kanbanjson" value="<?php echo $kanbanjson; ?>" />
			<input type="hidden" name="estado" value="<?php echo $estado; ?>" />
			<input type="hidden" name="pordefectoviejo" value="<?php echo $pordefectoviejo; ?>" />
			<label class="label" for="proyecto"><?php echo $palabras['vista8']['proyecto']; ?></label>
			<input style="margin-left: 30px; width : 150px; heigth : 150px" maxlength="12" class="input" type="text" name="proyecto" placeholder="<?php echo $palabras['vista8']['placeproyecto']; ?>" value="<?php echo $nombreproyecto ;?>" required><br>
			<label class="label" for="description"><?php echo $palabras['vista8']['descripcion']; ?></label>&nbsp;
			<input style="margin-right: 5px; width : 200px; heigth : 100px" maxlength="22" class="input" type="text" name="description" placeholder="<?php echo $palabras['vista8']['placedescripcion']; ?>" value="<?php echo $descproyecto ;?>" required><br>
			<!--35-->	
			<label class="label" for="columnas"><?php echo $palabras['vista8']['columnas']; ?><p style="color: red;font-size: 10px;"><?php echo $palabras['vista8']['placecolumnas']; ?></p></label>&nbsp;
			<input style="margin-right: 5px; width : 200px; heigth : 100px"  class="input" type="text" name="columnas" placeholder="Ej: aaa#aaa#aaa#aaa"  value="<?php echo $columnas ;?>" pattern="[a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}[#][a-zA-ZÀ-ÿ\u00f1\u00d1\" "]{1,15}" required><br>
			<?php  
			if ( $pordefecto == "on") {
				echo "
				<div style=\"display: flex;margin-bottom:-7px\">
					<p style='font-weight:bold;margin-right:25px' >".$palabras['vista8']['pordefecto']."</p>
					<p style='font-weight:bold;' >".$palabras['vista8']['deshabilitar']."</p>
				</div>
				<div style=\"display: flex;\">
					<br><input style=';width:15px;height:15px;margin-top:17px;margin-left:40px;margin-right:110px;' checked type='checkbox' name='pordefecto' />
					<a href='javascript:ConfirmDelete(".$idproyectoseleccionado.")' class=\"button is-warning\" style=\"width:30px;height:30px;padding:5px;margin-top:8px;\" ><img style=\"height: 16px;width: 16px; \" src='./images/eliminar.png'></a><br>
				</div>";
			}else{
				echo "
				<div style=\"display: flex;margin-bottom:-7px\">
					<p style='font-weight:bold;margin-right:25px' >".$palabras['vista8']['pordefecto']."</p>
					<p style='font-weight:bold;' >".$palabras['vista8']['deshabilitar']."</p>
				</div>
				<div style=\"display: flex;\">
					<br><input style=';width:15px;height:15px;margin-top:17px;margin-left:40px;margin-right:110px;' type='checkbox' name='pordefecto' />
					<a href='javascript:ConfirmDelete(".$idproyectoseleccionado.")' class=\"button is-warning\" style=\"width:30px;height:30px;padding:5px;margin-top:8px;\" ><img style=\"height: 16px;width: 16px; \" src='./images/eliminar.png'></a><br>
				</div>";
			}
			?>
			<button class="button is-danger is-rounded ml-4 mt-3" type="Submit"><?php echo $palabras['vista8']['editarproyecto']; ?></button>
		</form> 
	</div>
	<div class="columns is-mobile is-justify-content-center">
		<!-- IMAGEN DE VOLVER -->
		<figure style="margin-right: 80px" class="image is-24x24 is-justify-content-center">						
			<a href="<?php echo $rutarela; ?>?status=1"><img class="rounded" src="images/volver.png" height ="40" width="40" title="<?php echo $palabras['vista8']['volver']; ?>"/></a> 
		</figure>
		&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;	
	</div>

<?php
}
else if ($vista == 9) 
{
	$posicionarray = $_POST['posicionarray'];
	$nombreproyecto = $_POST['proyecto'];
	$descproyecto = $_POST['description'];
	$columnas = $_POST['columnas'];
	$idproyecto = $_POST['idproyecto'];
	$chathtml = $_POST['chathtml'];
	$kanbanjson = $_POST['kanbanjson'];
	$estado = $_POST['estado'];
	@$pordefecto = $_POST['pordefecto'];
	@$pordefectoviejo = $_POST['pordefectoviejo'];

	if ( $pordefecto == "on") {
		$pordefecto = $pordefecto;
	}else {
		$pordefecto = "off";
	}
	
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	// Elimino el proyecto a traves de la posicion que tenga
    unset($proyectos["proyectos"][$posicionarray]);
	
	$proyectos["proyectos"] = array_values($proyectos["proyectos"]);
	
	$newJsonString = json_encode(($proyectos));
    file_put_contents('./json/proyectos.json',$newJsonString);
	
	//Leemos el fichero proyectos.json
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
		
	//Utilizamos estas variables para insertar en el fichero "proyectos.json" el nuevo proyecto
	
	$index=count($proyectos["proyectos"]);
	
	@$proyectos["proyectos"][$index]['id']=$idproyecto;
	@$proyectos["proyectos"][$index]['proyecto']=$nombreproyecto;
	@$proyectos["proyectos"][$index]['description']=$descproyecto;
	@$proyectos["proyectos"][$index]['kanbanjson']=$kanbanjson;
	@$proyectos["proyectos"][$index]['chathtml']=$chathtml;
	@$proyectos["proyectos"][$index]['columnas']=$columnas;
	@$proyectos["proyectos"][$index]['pordefecto']=$pordefecto;
	@$proyectos["proyectos"][$index]['estado']=$estado;

	$newJsonString = json_encode($proyectos);
	file_put_contents('./json/proyectos.json',$newJsonString);
	
	
	if ( ( $pordefecto == "on" ) and ( $pordefectoviejo != "on" ) ) {
		
		echo '<script language="javascript">window.location.href="'.$rutarela.'?status=10&id='.$idproyecto.'";</script>';
	}else{
		echo '<script language="javascript">alert("'.$palabras['vista9']['editarproyectocorrecto'].'");window.location.href="'.$rutarela.'?status=1";</script>';;
	}
}
else if ($vista == 10) 
{
	$idproyectoseleccionado = $_GET['id'];
	
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);

	for($i=0;$i<count($proyectos["proyectos"]);$i++) 
	{
		$pordefecto = $proyectos["proyectos"][$i]["pordefecto"];
		$idproyecto = $proyectos["proyectos"][$i]["id"];	
		
		if ( ( $pordefecto == "on" ) and ( $idproyecto != $idproyectoseleccionado ) ) {
			// GUARDO LA POSICION DEL ARRAY QUE SE ENCUENTRA EL PROYECTO Y LAS VARIABLES CON SU VALOR CORRESPONDIENTE
			$posicionarray = $i;
			$idproyectoeliminado = $proyectos["proyectos"][$i]["id"];
			$nombreproyecto = $proyectos["proyectos"][$i]["proyecto"];
			$descproyecto = $proyectos["proyectos"][$i]["description"];
			$columnas = $proyectos["proyectos"][$i]["columnas"];
			$estado = $proyectos["proyectos"][$i]["estado"];
			$chathtml = $proyectos["proyectos"][$i]["chathtml"];
			$kanbanjson = $proyectos["proyectos"][$i]["kanbanjson"];
		}
	}
	
	// Elimino el proyecto a traves de la posicion que tenga
    unset($proyectos["proyectos"][$posicionarray]);
	
	$proyectos["proyectos"] = array_values($proyectos["proyectos"]);
	
	$newJsonString = json_encode(($proyectos));
    file_put_contents('./json/proyectos.json',$newJsonString);
	
	//Leemos el fichero proyectos.json
	$url_json = file_get_contents('./json/proyectos.json');
	$proyectos = json_decode($url_json,true);
	
	$index=count($proyectos["proyectos"]);
	
	$proyectos["proyectos"][$index]['id']=$idproyectoeliminado;
	$proyectos["proyectos"][$index]['proyecto']=$nombreproyecto;
	$proyectos["proyectos"][$index]['description']=$descproyecto;
	$proyectos["proyectos"][$index]['kanbanjson']=$kanbanjson;
	$proyectos["proyectos"][$index]['chathtml']=$chathtml;
	$proyectos["proyectos"][$index]['columnas']=$columnas;
	$proyectos["proyectos"][$index]['pordefecto']="off";
	$proyectos["proyectos"][$index]['estado']=$estado;

	$newJsonString = json_encode($proyectos);
	file_put_contents('./json/proyectos.json',$newJsonString);	
	//Utilizamos estas variables para insertar en el fichero "proyectos.json" el proyecto eliminado

	echo '<script language="javascript">alert("'.$palabras['vista9']['editarproyectocorrecto'].'");window.location.href="'.$rutarela.'?status=1";</script>';

}
?>
	</body>
</html>