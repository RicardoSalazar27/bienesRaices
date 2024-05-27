<?php

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETAS_IMAGENES',$_SERVER['DOCUMENT_ROOT'] . '/imagenes/');

function incluirTemplate( string $nombre, bool $inicio = false) {
    include TEMPLATES_URL. "/{$nombre}.php";
}

function estaAutenticado(){
    session_start();

    if(!$_SESSION['login']){
        header('location:/bienesraices/index.php');
    }
    
    return true;
}

function debuguear($variable){
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitiza el HTMl
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s; 
}

//Validar tipo de contenido
function validarTipoContenido($tipo){
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos);//valor por buscar, donde buscar
}

// Muestra los mensajes
function mostrarNotificacion($codigo){
    $mensaje='';

    switch ($codigo) {
        case '1':
            $mensaje = "Creado Correctamente";
            break;
        case '2':
            $mensaje = "Actualizado Correctamente";
            break;
        case '3':
            $mensaje = "Eliminado Correctamente";
            break;
        default:
            $mensaje = false;
            break;
    }

    return $mensaje;
}

function validarORedireccionar(string $url){
    //VALIDAR LA URL POR UN ID VALIDO
    $id = $_GET['id']; //obtengo el id en base al boton actualizar
    $id = filter_var($id, FILTER_VALIDATE_INT); //validamos que sea un entero el id
    if(!$id){
        header("Location: {$url}");
    }
    return $id;
}