<?php

/**
 * Autor: Sergio Matamoros Delgado
 * Descripción: Creación de un nuevo cuaderno usando los datos enviados por el cliente...
 */

//Estructura json:
//Acción: cuaderno.alta
//token: idUsuario

//Devolvemos:
//Resultado correcto: OK
//Resultado fallido: NOk
//Mensaje: Descripción ó error

require_once __DIR__ . "/../bd/database.php";

header("Content-Type: application/json");

// Recogemos los datos enviados por el cliente...
$jsonPost = file_get_contents('php://input');

// Lo convertimos en string...
$data = json_decode($jsonPost);

//Variable con los datos a enviar al usuario
$datosEnviar = array();

//Comprobaciones de los datos enviados...
if($data->accion == "cuaderno.alta") {

    $bd = new Database();

    //Comprobamos que es un usuario valido
    $usuarioValido = $bd->usuarioExiste($data->token);

    if(empty($data->titulo)) comprobarUsuario(9022);

    //Comprobamos que el usuario existe
    if($usuarioValido) {

        //Creación cuaderno en la base de datos...
        //Devuelve true si es válido la acción y los datos se subieron correctamente
        //Devuelve código de error si hubo algún tipo de error.
        $esCorrecto = $bd->crearCuaderno($data->token, $data->portada, $data->textoContraportada, $data->imagen);

        // /!\ NO TOCAR /!\
        //Devuelve los mensajes tanto de error como de éxito al cliente....
        $datosEnviar = comprobarUsuario($esCorrecto);
        
    } else {
        $datosEnviar["resultado"] = "NOK";
        $datosEnviar["mensaje"] = "Error, el usuario no existe...";
    }

    //Enviar respuesta a cliente...
    echo json_encode($datosEnviar);
    die();
}
//Dar de baja...
else if($data->accion == "cuaderno.baja") {

    $bd = new Database();

    //Comprobamos que es un usuario valido
    $usuarioValido = $bd->usuarioExiste($data->token);

    //Comprobamos que el usuario existe
    if($usuarioValido) {

        $esCorrecto = $bd->borrarCuaderno($data->token);

        // /!\ NO TOCAR /!\
        //Devuelve los mensajes tanto de error como de éxito al cliente....
        $datosEnviar = comprobarUsuario($esCorrecto);
        
        
    } else {
        $datosEnviar["resultado"] = "NOK";
        $datosEnviar["mensaje"] = "Error, el usuario no existe...";
    }

    //Enviar respuesta a cliente...
    echo json_encode($datosEnviar);
    die();
}

/**
 * Función que comprueba si el usuario es válido...
 * @param esCorrecto Parámetro a pasar con la información de si hubo un error, ó no...
 * @return Array- devuelve un array con los mensajes de éxito ó error.
 */
function comprobarUsuario($esCorrecto, $mensajeDatosOK = "Datos añadidos correctamente...", $mensajeRepetido = "Error, ya tienes un cuaderno creado...") {

    //Comprobación de datos correctos
    if($esCorrecto === true) {
        $datosEnviar["resultado"] = "OK";
        $datosEnviar["mensaje"] = $mensajeDatosOK;
    //Dato repetido...
    } else if($esCorrecto === "1062") {
        $datosEnviar["resultado"] = "NOK";
        $datosEnviar["mensaje"] = $mensajeRepetido;
    } else if($esCorrecto === 9022) {
        $datosEnviar["resultado"] = "NOK";
        $datosEnviar["mensaje"] = "Error, hay datos necesarios que están vacios...";
    }
    //Error genérico...
    else {
        $datosEnviar["resultado"] = "NOK";
        $datosEnviar["mensaje"] = "Error $esCorrecto. Hubo un error al insertar los datos... ";
    }

    return $datosEnviar;
}


//Si no es una acción válida lo especificamos...
$datosEnviar["mensaje"] = "Error, acción no válida.";

echo json_encode($datosEnviar);
die();