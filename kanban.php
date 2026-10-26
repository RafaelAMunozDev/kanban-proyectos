<?php
 // include ('session.php');
ob_start();
session_start();

@$idioma = $_SESSION['lang'];

$archivo = file_exists( "idiomas/$idioma.json" ) ?
			"idiomas/$idioma.json" :
			"idiomas/es.json";

$contenido = file_get_contents( $archivo );
$palabras = json_decode( $contenido, true );

//Cargamos las variables de session que necesitemos
$file = './json/'.$_SESSION['kanbanjson'];
$idproyectoseleccionado = $_SESSION['idproyectoseleccionado'];

//Funcion de añadir
function addItem ($id,$table,$title,$description,$date_string) {
    
	$file = './json/'.$_SESSION['kanbanjson'];
	
    global $data;
    $index=count($data[$table]);

    $data[$table][$index]['id']=$id;
    $data[$table][$index]['title']=$title;
    $data[$table][$index]['description']=$description;
    $data[$table][$index]['date_string']=$date_string;

    $newJsonString = json_encode($data);
    file_put_contents($file,$newJsonString);
	


}

//Funcion de borrar
function removeItem ($id,$table) {
    
	$file = './json/'.$_SESSION['kanbanjson'];
	
    global $data;
    $arrayNr= searchKey($id,$table);

    unset($data[$table][$arrayNr]);
		
    // ESTA LINEA ES MUY IMPORTANTE, vuelve a ordenar los indices del array para que al guardarlo siga siendo un array en el json y no un objeto
    $data[$table] = array_values($data[$table]);

    $newJsonString = json_encode(($data));
    file_put_contents($file,$newJsonString);

}

//Funcion de mover que es una combinacion de borrar y añadir, ya que primero se borrar y luego se añade con los datos anteriores pero en distinta columna
function moveItem ($id,$table_old,$table_new,$arrayIds) {

    global $data;
    $data_new = $data;
    $arrayIdsLength = count($arrayIds);
    $arrayNr= searchKey($id,$table_old);

    // Contamos las tablas que hay en la columna nueva
    $ar=count($data[$table_new]);

    // Vaciamos la columna nueva
    for ($x = 0; $x < $ar; $x++) {

      unset($data[$table_new][$x]);

    }

    // Rellenamos la columna nueva con las tablas en el orden en el que han quedado
    for ($x = 0; $x < $arrayIdsLength; $x++) {

      if ($arrayIds[$x] == $id) {

        $title = $data_new[$table_old][$arrayNr]['title'];
        $description = $data_new[$table_old][$arrayNr]['description'];
        $date_string = $data_new[$table_old][$arrayNr]['date_string'];

        addItem($arrayIds[$x],$table_new,$title,$description,$date_string);

      } else {

        $title = $data_new[$table_new][searchKeyCustom($arrayIds[$x],$table_new,$data_new)]['title'];
        $description = $data_new[$table_new][searchKeyCustom($arrayIds[$x],$table_new,$data_new)]['description'];
        $date_string = $data_new[$table_new][searchKeyCustom($arrayIds[$x],$table_new,$data_new)]['date_string'];

        addItem($arrayIds[$x],$table_new,$title,$description,$date_string);

      }

    }

    // Si la tabla se ha movido a otra columna la borramos de la columna antigua
    if ($table_new != $table_old)
      removeItem($id,$table_old);

}

function searchKey ($id,$table) {

	global $data;

	foreach ($data[$table] as $key => $entry) {

	  if ($entry['id'] == $id)
	   return $key;

	}

}

  // Igual que searchKey pero buscando en el array que le pasemos (aqui una copia de $data)
  function searchKeyCustom ($id,$table,$data) {

    foreach ($data[$table] as $key => $entry) {

      if ($entry['id'] == $id)
       return $key;

    }

  }
  
///////////////////////////////////////////////////////////////
//ESTA VARIABLE LA UTILIZO PARA DAR UNA RUTA RELATIVA EN LOS ENLACES
$rutarela= strtok($_SERVER["REQUEST_URI"], "?");

//controlador dependiendo del status de la url
	
$vista=$_GET["status"];

if (isset($vista)){
	
	$vista=$vista;
	
}else {
		
	$vista=1;
}
?>

<!D0CTYPE html>
<html>
	<head>
		<title>KANBAN</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="./images/icono.png">
		<link rel="stylesheet" href="css/bootstrap.css"><link rel="stylesheet" href="css/kanban.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
		<link rel="stylesheet" href="./quilljs-textarea-master/quill.snow.css" />		
	</head>
	<body>
			<img class="imglogo" src="./images/logo.png">

<?php 
//Vista principal que no se ve y que se utiliza para borrar y mover
if ( $vista == 1) 
{
  // variables
  $title = $_POST['title'];
  $description = $_POST['description'];
  $id = $_POST['id'];
  $table = $_POST['table'];
  $table_new = $_POST['table_new'];
  $table_old = $_POST['table_old'];
  $function = $_POST['function'];
  $date_string = $_POST['date_string'];
  $arrayIds = $_POST['arrayIds'];

  // El json se carga como array asociativo (por eso el TRUE), si se carga como objeto no funcionan los indices
  
  //Variables que cargan el json del proyecto
  $opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
  $context = stream_context_create($opts);
  $jsonString = file_get_contents($file, FALSE, $context);
  $data = json_decode($jsonString,TRUE);

  //Aqui se elige que funcion se va a utilizar
  if ($function == "removeitem")
    removeItem($id, $table);
  elseif ($function == "moveitem")
    moveItem($id,$table_old,$table_new,$arrayIds);

} 
else if ($vista == 2) 
{

	//Vista utilizada para editar, esta recoge todos los datos por get y pinta el formulario para posteriormente 
	//enviar todos los datos otra vez a la vista 3 pero modificamos solo el titulo y la descripcion
	
	$id = $_GET['id'];
	$table = $_GET['table'];
	$date_string = $_GET['date_string'];
    $title = $_GET['title'];
    $description = $_GET['description'];

?>
	<div class="label columns is-mobile is-justify-content-center">
		<h2 class="title is-1 is-size-4-mobile has-text-centered mr-6 is-underlined has-text-danger"><?php echo $palabras['indexv2']['editartabla']; ?></h2>
	</div>
	<div class="columns is-mobile is-justify-content-center mt-4 mr-5">
		<form action="<?php echo $rutarela ;?>?status=3" method="POST">
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="table" value="<?php echo $table;?>" />
			<input type="hidden" name="date_string" value="<?php echo $date_string;?>" />
			<label class="label" for="title"><?php echo $palabras['indexv2']['titulo']; ?></label>
			<input style="margin-left:120px; width : 150px; heigth : 150px;border: black 0.5px solid;" maxlength="13" class="input is-small" type="text" value="<?php echo $title;?>" name="title" placeholder="<?php echo $palabras['indexv2']['placetitulo']; ?>" required><br>
			<label class="label" for="description"><?php echo $palabras['indexv2']['descripcion']; ?></label>
			<textarea class="quilljs-textarea" "type="text" name="description" required><?php echo $description;?></textarea>
					
			<button class="button is-danger is-rounded" style="width:80px;margin-left:157px;" type="Submit"><?php echo $palabras['indexv2']['editar']; ?></button>
		</form> 
	</div>
	<!-- Script utilizados para tener el textarea con el wyswyg -->
			  <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
    <script src="./quilljs-textarea-master/quill-textarea.js"></script>

    <!-- Initialize Quill editor -->
    <script>
    (function() {
      quilljs_textarea('.quilljs-textarea', {
        theme: 'snow',
		modules: {
				 toolbar: [

            ["bold", "italic", "underline", "strike"],

            [{ "script": "sub" }, { "script": "super" }],


            [{ "list": "ordered" }, { "list": "bullet" }, { "indent": "-1" }, { "indent": "+1" }],

            ["clean"] ]
				}
      });
    })();
    </script>


	<div class="columns is-mobile is-justify-content-center">
		<!-- IMAGEN DE VOLVER -->
	
		<figure style="margin-right: 80px" class="image is-24x24 is-justify-content-center">						
			<a href="index.php?status=2&idproyecto=<?php echo $idproyectoseleccionado;?>"><img class="rounded" src="images/volver.png" height ="40" width="40" title="<?php echo $palabras['indexv2']['volver']; ?>"/></a> 
		</figure>
		&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;	
	</div>
<?php
  
}
else if ($vista == 3) 
{	
	//Recogemos los datos de la vista 2, cargamos el fichero, borramos la tabla y la añadimos con los nuevos datos

	$id = $_POST['id'];
	$table = $_POST['table'];
	$date_string = $_POST['date_string'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	 
	$opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
	$context = stream_context_create($opts);
	$jsonString = file_get_contents($file, FALSE, $context);
	$data = json_decode($jsonString,TRUE);

	removeItem ($id,$table);

	addItem($id,$table,$title,$description,$date_string);
	
	header('Location: index.php?status=2&idproyecto='.$idproyectoseleccionado);

}
else if ($vista == 4) 
{	
	//Vista utilizada para añadir una tabla, esta recoge todos los datos por get y pinta el formulario para posteriormente 
	//enviar todos los datos otra vez a la vista 5 pero modificamos solo el titulo y la descripcion
	//ESTA VISTA ES EXACTAMENTE IGUAL QUE LA ANTERIOR PERO CON LA DIFERENCIA DE QUE VAMOS A AÑADIR UNO NUEVO
	$id = $_GET['id'];
	$table = $_GET['table'];
	$date_string = $_GET['date_string'];
	@$plantillaescogida =$_GET['plantilla'];
	
	$url_json = file_get_contents('./json/plantillas.json');
	$plantillas = json_decode($url_json,true);
?>
	<div style ="float:right; margin-top: 50px; margin-right:20px;" class="label">
	<p style="margin-bottom:10px;"><?php echo $palabras['indexv4']['plantillas']; ?></p>
	<select name="planti" id="planti">
		<option  value="" disabled selected><?php echo $palabras['indexv4']['seleccionaplantilla']; ?></option>
<?php
	for($i=0;$i<count($plantillas["plantillas"]);$i++) 
	{
		$nombre = $plantillas["plantillas"][$i]["nombre"];
		$texto = $plantillas["plantillas"][$i]["texto"];
		echo "<option value='".$texto."'>".$nombre."</option>";
		
		if ( $nombre == $plantillaescogida ) {
			$textoplantilla = $texto;
		}
	}
?>
	</select>
	</div>
	<div class="label columns is-mobile is-justify-content-center">
			<h2 class="title is-1 is-size-4-mobile has-text-centered mr-6 is-underlined has-text-danger"><?php echo $palabras['indexv4']['crear']; ?></h2>
	</div>
	<div class="columns is-mobile is-justify-content-center mt-4 mr-5">
		<form action="<?php echo $rutarela ;?>?status=3" id="quillForm" method="POST">
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="table" value="<?php echo $table;?>" />
			<input type="hidden" name="date_string" value="<?php echo $date_string;?>" />
			<label class="label" for="title"><?php echo $palabras['indexv2']['titulo']; ?></label>
			<input style="margin-left:120px; width : 150px; heigth : 150px;border: black 0.5px solid;" maxlength="13" class="input is-small" type="text" name="title" placeholder="<?php echo $palabras['indexv4']['placetitulo']; ?>" required><br>
			<label class="label" for="description"><?php echo $palabras['indexv4']['descripcion']; ?></label>
			<textarea class="quilljs-textarea" type="text" name="description" required><?php echo @$textoplantilla ; ?></textarea>	
			<button class="button is-danger is-rounded" style="width:80px;margin-left:157px;" type="Submit"><?php echo $palabras['indexv4']['crear2']; ?></button>
		</form>	
	</div>
	<!-- Script utilizados para tener el textarea con el wyswyg -->
			  <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
    <script src="./quilljs-textarea-master/quill-textarea.js"></script>

    <!-- Initialize Quill editor -->
    <script>

	var select = document.getElementById('planti');
	select.addEventListener('change',
	  function(){
		var selectedOption = this.options[select.selectedIndex];
		var texto = selectedOption.text;
		window.location.href = "<?php echo $_SERVER["REQUEST_URI"]; ?>&plantilla="+texto;
		//document.getElementById("place").innerHTML = texto;
	  });

    (function() {
      quilljs_textarea('.quilljs-textarea', {
        theme: 'snow',
		modules: {
				 toolbar: [

            ["bold", "italic", "underline", "strike"],

            [{ "script": "sub" }, { "script": "super" }],


            [{ "list": "ordered" }, { "list": "bullet" }, { "indent": "-1" }, { "indent": "+1" }],

            ["clean"] ]
				},
      });
    })();
	
	
	
    </script>


	<div class="columns is-mobile is-justify-content-center">
		<!-- IMAGEN DE VOLVER -->
	
		<figure style="margin-right: 80px" class="image is-24x24 is-justify-content-center">						
			<a href="index.php?status=2&idproyecto=<?php echo $idproyectoseleccionado;?>"><img class="rounded" src="images/volver.png" height ="40" width="40" title="<?php echo $palabras['indexv4']['volver']; ?>"/></a> 
		</figure>
		&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	&nbsp;	
	</div>
<?php
}
else if ($vista == 5) 
{	

	//Recogemos los datos de la vista 4 y añadimos la tabla
	
	$id = $_POST['id'];
	$table = $_POST['table'];
	$date_string = $_POST['date_string'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	 
	$opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
	$context = stream_context_create($opts);
	$jsonString = file_get_contents($file, FALSE, $context);
	$data = json_decode($jsonString,TRUE);

	addItem($id,$table,$title,$description,$date_string);
	
	header('Location: index.php?status=2&idproyecto='.$idproyectoseleccionado);
	
}
?>
</body>
</html>

