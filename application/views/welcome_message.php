<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">

<head>
<link rel="stylesheet" href="css/css/bootstrap.css" >
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js" ></script>    
<script src="js/jquery.countdown.min.js"></script>
</head>
<script>
//Variable globales
var inicio = 0;
var result = '';
var equipos = 2;
var proximo_equipo = 1;
var nombres=[];
var tiempo=60000;
var url = "<?=base_url() ?>index.php/welcome/getPreguntas";
var urlKey = "<?=base_url() ?>index.php/welcome/getKey";
function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    var div = $("pregunta");
    var color = "{background-color: "+color+"!important; padding: 5%; margin: 5%;}";
    div.attr("style",color);
    
}

function regresiva(){
    var fiveSeconds = new Date().getTime() + tiempo;
    $("#clock").countdown(fiveSeconds, {elapse: false}).on('update.countdown', function(event) {
        var $this = $(this);
        $this.html(event.strftime('<span>%H:%M:%S</span>'));
    });
}

function proximaPregunta(){
  getRandomColor();
  return Math.floor((Math.random() * result.length));
}

function getProximoEquipo(){
    proximo_equipo = proximo_equipo + 1;
    if (proximo_equipo >= equipos)
        proximo_equipo = 0;
    return nombres[proximo_equipo];
}

function getPregunta(equipo){
    inicio = proximaPregunta();
    $("#pregunta").html("");
    $("#pregunta").append("<h2> <center> Juega "+equipo+" <center> </h2>");
    $("#pregunta").append("<h3>"+result[inicio].pregunta+"</h3><p>"+result[inicio].opciones+"</p>");
    $("#showRespuesta").html("");
}

function getRespuesta(){
    $("#respuesta").html("");
    $("#respuesta").html("<h3>"+result[inicio].repuesta+"</h3>");
}


$(document).ready(function(){

    var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'sounds/clock.mp3');
        audioElement.setAttribute('loop', 'loop');
        
    var audioElement3 = document.createElement('audio');
        audioElement3.setAttribute('src', 'sounds/correcto.mp3');

    var audioElement2 = document.createElement('audio');
        audioElement2.setAttribute('src', 'sounds/incorrecto.mp3');

    

    //TODO (Refactor) Validacion de perguntas existentes
    var save = localStorage.getItem("preguntas");
    if (save != null){
      result = JSON.parse(save);
    }
    if (result==""){
      $.getJSON( url, function(data) {
        localStorage.setItem("preguntas",  JSON.stringify(data));
        result = data;
      })
      .done(function() {
        console.log( "second success" );
      });
    }
    $("#p4").hide();
    $("#p3").hide();
    $("#p2").hide();


    //TODO (Refactor) Validacion de actulizacion de nuevas preguntas
    var version = localStorage.getItem("version");
    if (save != null){
      max = JSON.parse(save);
    }
    if (result==""){
      $.getJSON( urlKey, function(data) {
        localStorage.setItem("version",  JSON.stringify(data));
        if (max < data){
          $.getJSON( url, function(data) {
            localStorage.setItem("preguntas",  JSON.stringify(data));
            result = data;
          })
          .done(function() {
            console.log( "second success" );
          });
        }
      })
      .done(function() {
        console.log( "second success" );
      });
    }




    $("#siguiente").click(function(){
        $("#clock").show();
        $("#p4").hide();
        $("#finalizar").hide();
        $("#p3").show();
        getPregunta(getProximoEquipo());
        audioElement.play();
        $("#finalizar").show();
        regresiva();
        $("#correcto").show();

     });

    $("#siguientep2").click(function(){
      	equipos = $("#jugadores").val();
        $("#p1").hide();
        $("#p2").show();
        $("#p3").hide();
        var aux = 1;
	    for (i = 0; i < equipos; i++) { 
        	$("#configuracion").append("<div class='form-group form-group-lg'><label class='col-sm-2 control-label' for='formGroupInputLarge'>Equipo "+aux+"</label><div class='col-sm-10'><input type='text' class='nombre form-control' name='equipo"+i+"' value='Equipo "+aux+"'/></div>");
            aux = aux + 1;

	    }
      
    });

    $(".siguientep3").click(function(){
  
      $("#p1").hide();
      $("#p2").hide();
      $("#p3").show();
  
      $("#puntuacion").append("<tbody> <tr><td>Equios</td><td>Puntaje</td></tr>");
      $(".nombre").each(function(elem){
        var nombre = $(this).val();
        nombres.push(nombre);
        $("#puntuacion").append("<tr><td>"+nombre+"</td><td><input type='number' id='"+nombre.replace(" ","_")+"' value='0' disabled></td></tr>");
      });    
      $("#puntuacion").append("</tbody>");
        getPregunta(nombres[proximo_equipo]);
        audioElement.play();
        var valor = $("#tiempo").val();
        if (valor != ''){
            tiempo = valor * 1000;
        }
        regresiva();
     });

    $("#finalizar").click(function(){
       $("#p3").hide();
       $("#p4").show();
       $("#clock").hide();
       getRespuesta();
       audioElement.pause();
       audioElement3.play();
       inicio = inicio + 1;
       $("#siguiente").val("Incorrecto");
    });

    $("#verRespuesta").click(function(){
       $("#showRespuesta").html("Repuesta: "+result[inicio].repuesta);
    });

    $("#correcto").click(function(){
        var elem = "#"+nombres[proximo_equipo].replace(" ","_");
        var valor = parseInt($(elem).val()) + 1;
        $(elem).val(valor);
        audioElement2.play();
        getPregunta(nombres[proximo_equipo]);
    });


});

</script>
<body style="font-family:'MyWebFont';background-image: url(http://www.snazzyspace.com/wallpapers/7e7e7e_pastel-stripes.png);">

    <div id="p1" >
      <center>
        <img data-src="holder.js/140x140" class="img-circle" alt="140x140" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgdmlld0JveD0iMCAwIDE0MCAxNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzE0MHgxNDAKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNTVmNmNmM2MyYiB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE1NWY2Y2YzYzJiIj48cmVjdCB3aWR0aD0iMTQwIiBoZWlnaHQ9IjE0MCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjQ0LjY5NTMxMjUiIHk9Ijc0LjUiPjE0MHgxNDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="width: 140px; height: 140px;">
      </center>
      <div id="tarjeta" style="background-image: url(img/fondo3.gif); padding: 5%; margin: 5%; border-radius: 15px;">
        <h2>Configuracion</h2>
        <hr/>
        <p>Cantidad de jugadores</p>
            <input type="number" min="2" name="jugadores" id="jugadores" required="true" value="2"></br>
      </div>
      <center>
            <input type="button"  class="btn btn-primary btn-lg" value="?" id="help">
            <button class="btn btn-primary btn-lg" id="siguientep2"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span></br></button>
            <button type="button" class="btn btn-primary btn-lg siguientep3"> <span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
      </center>
    </div>

    <div id="p2">
      <div id="tarjeta" style="background-image: url(img/fondo3.gif); padding: 5%; margin: 5%; border-radius: 15px;">
          <form class="form-horizontal" id="configuracion" >
            <div class="form-group form-group-lg">
              <label class="col-sm-2 control-label" for="formGroupInputLarge">Tiempo por partida</label>
              <div class="col-sm-10">
                <input class="form-control" type="number" min="60" name="tiempo" id="tiempo" value="180" placeholder="Segundos">
              </div>
            </div>
            <div class="form-group form-group-lg">
              <label class="col-sm-2 control-label" for="formGroupInputLarge">Cantidad de puntos</label>
              <div class="col-sm-10">
                <input class="form-control" type="number" min="2" name="maxPuntos" id="maxPuntos" required="true" value="2">
              </div>
            </div>
          </form>
      </div>
      <center>
        <button class="btn btn-primary btn-lg siguientep3"> <span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
      </center>
       </div>
      </div>
    </div>

    <div id="p3">
      <div id="tarjeta" class="pregunta" style="background-image: url(img/fondo3.gif); padding: 5%; margin: 5%; border-radius: 15px;">
        <div id="pregunta">
        </div>
        <div id="showRespuesta">
        </div>
          <span id="clock"></span>
      </div>

      <center>
        <button type="button" id="correcto" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
        <button type="button" class="btn btn-primary btn-lg" id="verRespuesta"> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></button>
        <button type="button" class="btn btn-primary btn-lg" value="Finalizar" id="finalizar"> <span class="glyphicon glyphicon-remove" aria-hidden="true"></button>
      </center>
    </div>
   
    </div>
    <div id="p4">
        <div id="tarjeta" style="background-image: url(img/fondo3.gif); padding: 5%; margin: 5%; border-radius: 15px;">
          <center>
            <h1>Puntuacion</h1>
          </center>
          <table class="table" id="puntuacion">
          </table>
        </div>
        <center>
          <button type="button" id="siguiente" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-step-forward" aria-hidden="true"></button>
        </center>
    </div>
    
</body>
</html>