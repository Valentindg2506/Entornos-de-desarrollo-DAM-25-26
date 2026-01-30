<?php
function evaluarNota($nota) {
    if ($nota < 0 || $nota > 10) {
        return "Nota no válida";
    }

    if ($nota >= 9) {
        return "Sobresaliente";
    } elseif ($nota >= 7) {
        return "Notable";
    } elseif ($nota >= 5) {
        return "Aprobado";
    } elseif ($nota >= 3) {
  //ERROR: devuelve algo no esperado en el test
        return "Suspenso";
    }
    // ERROR: este return nunca se prueba
    return "Suspenso";
}
 
function mensajeExtra() {
    // FUNCIÓN NO USADA
    return "Función no ejecutada";}
