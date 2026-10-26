$.ajaxSetup({ cache: false }); 

// Variables globales
var d = new Date();

function dividirpalabra(palabra) {
	var tamMax=42
	if(palabra.length < tamMax){
		return palabra;
	} else {
		return palabra.substring(0, tamMax) + '\n' + dividirpalabra(palabra.substring(tamMax, palabra.length));
	}	
}



//Funcion para generar un id aleatorio, esta funcion se utiliza en la funcion addItem
function generateID () {

  var secondCounter = d.getSeconds ();
  if (secondCounter < 10)
    secondCounter = '0'+secondCounter;
  var minuteCounter = d.getMinutes ();
  if (minuteCounter < 10)
    minuteCounter = '0'+minuteCounter;
  var hourCounter = d.getHours ();
  if (hourCounter < 10)
    hourCounter = '0'+hourCounter;
  var dayCounter = d.getDate ();  
  if (dayCounter < 10)
    dayCounter = '0'+dayCounter; 
  var monthCounter = (d.getMonth()+1);
  if (monthCounter < 10)
    monthCounter = '0'+monthCounter;
  var random = Math.floor ((Math.random() * 100) + 1); // entre 1 y 100

  var id = monthCounter+''+dayCounter+''+hourCounter+''+minuteCounter+''+secondCounter+''+random;

  return id;

}
// Funcion para generar la fecha en formato 00/00, utilizada solo en la funcion addItem
function generateDate_string () {



  month = d.getMonth();
	
  var meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

  return d.getDate()+'/'+meses[month];

}

//Funcion de añadir
// Parametros: id (generado con generateID), titulo y descripcion
function addItem (id,title,description,date_string) {

  window.location.href = 'kanban.php?status=4&id='+generateID()+'&table=idea&date_string='+generateDate_string();

}

//Funcion de mover
// Parametros: id de la tabla, table_old es la columna de donde sale y table_new la columna a donde va
function moveItem (id,table_old,table_new) {

  var arrayIds = [];
  $("#"+table_new).children().each(function(arrayNr,element){arrayIds.push(element.id)});
  console.log(arrayIds);
  console.log(id);

  $.ajax({
    method:'POST',
    url:'kanban.php',
    data: {function: "moveitem",id: id,table_old: table_old, table_new: table_new, arrayIds: arrayIds},
    datatype: 'json',

    success:function() {

      console.log(" moved success");
      cleanGrid();
      buildGrid();

    }

  });

}

//Funcion de borrar
// Parametros: id de la tabla, table es la columna de donde se borra y el es el div completo
function removeItem (id,table,el) {

  $.ajax({

    method:'POST',
    url:'kanban.php',
    data: {function: "removeitem",id: id,table: table},
    datatype: 'json',

    success:function() {
      
      console.log("deleted success");
      cleanGrid();
      buildGrid();

    }

  });

  // Borramos el div
  el.remove();

}

function cleanGrid () {

	  $('.container').find('div').each(function () {

		this.remove ();

	  });

}

// Estas variables sirven para pintar y dar funcion a la papelera de kanban ( el cuadro rojo arriba a la derecha)
dragula([document.querySelector('#wastebucket'),document.querySelector('.container'), document.querySelector('#todo'),document.querySelector('#deliver'),document.querySelector('#review')]).on('drag', function (el) {
}).on('drop', function (el, table_new, table_old ) {

  if (table_new.id === 'wastebucket') {

    removeItem(el.id,table_old.id,el);

  } else {

    moveItem(el.id,table_old.id,table_new.id);

  }

  $('#wastebucket').css('background-color','#e2dbb0');

}).on('over', function (el, container) {

  $('#wastebucket').css('background-color','red');

  if (el.id === 'itemformdiv')
    el.remove();

}).on('out', function (el, container) {

}).on('click', function (el, container) {

});

// Aqui llamamos a la funcion addItem cuando pulsamos su boton correspondiente
$(document).ready(function () {

  $('#newitem').click(function (e) {

    addItem(generateID(),generateDate_string());
    e.preventDefault();
  });

});