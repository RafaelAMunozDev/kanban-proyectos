<?php session_start(); ?>
<script>

function copiarAlPortapapeles(id_elemento) {

  // Crea un campo de texto "oculto", este por un textarea

  var aux = document.createElement("textarea");

  // Asigna el contenido del elemento especificado al valor del campo
  // este para vaciar el contenido

  aux.innerHTML = document.getElementById(id_elemento).innerHTML

  // Añade el campo a la página
  document.body.appendChild(aux);

  // Selecciona el contenido del campo
  aux.select();

  // Copia el texto seleccionado
  document.execCommand("copy");

  // Elimina el campo de la página
  document.body.removeChild(aux);

}

</script>




<?php

@$idioma = $_SESSION['lang'];
$archivo = file_exists( "../idiomas/$idioma.json" ) ?
			"../idiomas/$idioma.json" :
			"../idiomas/es.json";

$contenido = file_get_contents( $archivo );
$palabras = json_decode( $contenido, true );

$chathtml = './chats/'.$_SESSION['chathtml'];
$idproyectoseleccionado = $_SESSION['idproyectoseleccionado'];
$nombreproyecto = $_SESSION['nombreproyecto'];
if(isset($_POST['enter'])){

    if($_POST['name'] != ""){

        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));

    }

    else{

        echo '<span class="error">Please type in a name</span>';

    }

}


?>

<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8" />
		<link rel="shortcut icon" type="image/x-icon" href="../images/icono.png">
        <title>Chat Application</title>

        <meta name="description" content="Tuts+ Chat Application" />
        <link rel="stylesheet" href="style.css"/>



    </head>

    <body>
		
		<img style="padding: 0;margin-top: -70px;margin-left: -10px;overflow: hidden;height: 200px;width: 260px;margin-bottom:-35px; " src="../images/logo.png">
		
		<p class="logout"><a id="exit" href="#"><?php echo $palabras['chat']['salir']; ?></a></p>
        <div id="wrapper">
			
            <div id="menu">

                <p class="welcome"><?php echo $palabras['chat']['notas']; ?> <?php echo $nombreproyecto; ?></b></p>

            </div>

            <div id="chatbox">

            <?php

			
            //if(file_exists("log.html") && filesize("log.html") > 0){

            $contents = file_get_contents($chathtml,FILE_USE_INCLUDE_PATH);          

            echo $contents;
					
            //}

            ?>

            </div>

            <form name="message" action="">

                <textarea class="small-textarea" name="usermsg" type="text" id="usermsg" /></textarea>
				<input type="hidden" id="searchInput" name="searchInput" value="<?php echo $chathtml;?>" />
                <input id="submitmsg" name="submitmsg" type="submit"  value="<?php echo $palabras['chat']['enviar']; ?>" />

            </form>

        </div>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script type="text/javascript">



            // jQuery Document

            $(document).ready(function () {

                $("#submitmsg").click(function () {

                    var clientmsg = $("#usermsg").val();
					
					var clientmsg = clientmsg.replaceAll(/\n/g,"<br>");
					
                    $.post("post.php", { text: clientmsg });
		
                    $("#usermsg").val("");

                    return false;

                });

				
                function loadLog() {
					
					var ruta = document.getElementById('searchInput').value;
					
                    var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;//Scroll height before the request
					
					
                    $.ajax({

                        url: ruta,

                        cache: false,

                        success: function (html) {

                            $("#chatbox").html(html); //Insert chat log into the #chatbox div

                            //Auto-scroll

                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request

                            if(newscrollHeight > oldscrollHeight){

                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
								
                            }
	
                        }

                    });


                }


                setInterval (loadLog, 2500);
				
                $("#exit").click(function () {

                    window.location = "../index.php?status=2&idproyecto="+<?php echo $idproyectoseleccionado; ?>;


                });

            });

        </script>

    </body>

</html>

