<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">

<head>
<link rel="stylesheet" href="css/css/bootstrap.css" >
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js" ></script>    
<script src="js/jquery.countdown.min.js"></script>
<title>El sabio</title>
</head>
<script>
//Variable globales
var inicio = 0;
var result = '';
var equipos = 2;
var proximo_equipo = 1;
var nombres=[];
var tiempo=60000;
var maxPuntos = 2;
var url = "<?=base_url() ?>index.php/welcome/getPreguntas";
var urlKey = "<?=base_url() ?>index.php/welcome/getKey";
var volumenAudio = true;

function regresiva(){
    var fiveSeconds = new Date().getTime() + tiempo;
    $("#clock").countdown(fiveSeconds, {elapse: false}).on('update.countdown', function(event) {
        var $this = $(this);
        $this.html(event.strftime('<span>%H:%M:%S</span>'));
    });
}

function validarGanador(equipo){
    var elem = "#"+nombres[proximo_equipo].replace(" ","_");
    var valor = parseInt($(elem).val());
    if (valor>=maxPuntos){
      $("#p5").hide();
      $("#p4").hide();
      $("#p3").hide();
      $("#p2").hide();
      $("#p1").show();
      alert("Ha ganado el equipo"+nombres[proximo_equipo]);
    }
    
}


function proximaPregunta(){
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
    $("#p5").hide();
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

    $("#agregarEquipos").click(function(){
      var aux = 1;
      equipos = $("#jugadores").val();
      $("#equipos").html("");
      for (i = 0; i < equipos; i++) { 
          $("#equipos").append("<div class='form-group form-group-lg'><label class='col-sm-2 control-label' for='formGroupInputLarge'>Equipo "+aux+"</label><div class='col-sm-10'><input type='text' class='nombre form-control' name='equipo"+i+"' value='Equipo "+aux+"'/></div>");
            aux = aux + 1;
      }
    });

    $("#siguientep2").click(function(){
        $("#p1").hide();
        $("#p2").show();
        $("#p3").hide();
    });

    $(".siguientep3").click(function(){
  
      var aux = 1;
      equipos = $("#jugadores").val();
      $("#equipos").html("");
      for (i = 0; i < equipos; i++) { 
          $("#equipos").append("<div class='form-group form-group-lg'><label class='col-sm-2 control-label' for='formGroupInputLarge'>Equipo "+aux+"</label><div class='col-sm-10'><input type='text' class='nombre form-control' name='equipo"+i+"' value='Equipo "+aux+"'/></div>");
            aux = aux + 1;
      }

      $("#p1").hide();
      $("#p2").hide();
      $("#p3").show();
  
      $("#puntuacion").html("<tbody> <tr><td>Equios</td><td>Puntaje</td></tr>");
      nombre = [];
      $(".nombre").each(function(elem){
        var nombre = $(this).val();
        nombres.push(nombre);
        $("#puntuacion").append("<tr><td>"+nombre+"</td><td><input type='number' id='"+nombre.replace(" ","_")+"' value='0' disabled></td></tr>");
      });    
      maxPuntos = parseInt($("#maxPuntos").val());
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
       validarGanador(nombres[proximo_equipo]);
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

    $("#help").click(function(){
        $("#p1").hide();
        $("#p5").show();
    });

    $("#okAyuda").click(function(){
        $("#p5").hide();
        $("#p1").show();
    });

    $("#sonido").hide();
    $("#mute").click(function(){
        $(this).hide();
        $("#sonido").show();
        audioElement.muted = true;  
    });
    $("#sonido").click(function(){
        $(this).hide();
        $("#mute").show();
        audioElement.muted = false;  
    });
});

</script>
<style type="text/css">
body{
  font-family:'MyWebFont';background-image: url(http://www.snazzyspace.com/wallpapers/7e7e7e_pastel-stripes.png);
}
.tarjeta{
  background-image: url(img/fondo3.gif); padding: 5%; margin: 5%; border-radius: 15px;
}
</style>

<body>

    <div id="p1" >
      <center>
        <br>
        <img data-src="holder.js/140x140" class="img-circle" alt="140x140" src="http://2.bp.blogspot.com/-Sla8M5YPxvQ/VOSjOG0nORI/AAAAAAAAALw/r-RolACwRHw/s1600/aristoteles-portrait.png" data-holder-rendered="true" style="width: 140px; height: 140px;">
        <h1>
          El sabio
        </h1>
      </center>
      <!--<div class="tarjeta" style="">
         <h2>Configuracion</h2>
        <hr/>
        <p>Cantidad de jugadores</p>
            <input type="number" min="2" name="jugadores" id="jugadores" required="true" value="2"></br>
      </div> -->
      <center>
            <input type="button"  class="btn btn-primary btn-lg" value="?" id="help">
            <button class="btn btn-primary btn-lg" id="siguientep2"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span></br></button>
            <button type="button" class="btn btn-primary btn-lg siguientep3"> <span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
      </center>
    </div>

    <div id="p2">
      <div class="tarjeta">
          <div class="form-horizontal" id="configuracion" >
            <div class="form-group form-group-lg">
              <label class="col-sm-2 control-label" for="formGroupInputLarge">Cantidad de jugadores</label>
              <div class="col-sm-10">
                <input class="form-control" type="number" min="2" name="jugadores" id="jugadores" required="true" value="2" placeholder="Segundos">
              </div>
            </br>
            <center>    
              <button class="btn btn-primary btn-lg" id="agregarEquipos">Agregar Equipos</button>
            </center>
        
            </div>
            
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
            <div id="equipos">
            </div>
          </div>
      </div>
      <center>
        <button class="btn btn-primary btn-lg siguientep3"> <span class="glyphicon glyphicon-play" aria-hidden="true"></span></button>
      </center>
       </div>
      </div>
    </div>

    <div id="p3">
      <div class="tarjeta pregunta">
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
        <button type="button" class="btn btn-primary btn-lg" id="mute"> <span class="glyphicon glyphicon-volume-off" aria-hidden="true"></button>
        <button type="button" class="btn btn-primary btn-lg" id="sonido"> <span class="glyphicon glyphicon-volume-up" aria-hidden="true"></button>
      </center>
    </div>
   
    </div>
    <div id="p4" >
        <div class="tarjeta">
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

    <div id="p5">
        <div class="tarjeta">
          <center>
            <h1>Reglas</h1>
          </center>
          <p>
            Bievenido al Sabio seguramente estes aqui para jugar con la reglas pensadas sobre este juego, bien aqui van:
          </p>
          <p>
            El primero que llegue a los puntos configurados al limite de cantidad de puntos sera el ganador.<br>
          </p>
          <p>
            Al tocar un pregunta del tipo todos juegan los equipos podran utilizar el tiempo que le queda al equipo que actualmente este jugando y luego se debera definir cual de los equipos salio ganador. 
            Esto le dara el turno al equipo que gano salteando a los demas equipos.
          </p>

        </div>
        <center><button type="button" id="okAyuda" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button></center>
          
    </div>    
    
</body>
</html>