<?php
header('Access-Control-Allow-Origin: *');
class Vivenciasmodelo
{
    /* Atributo */
    public $conexion;

    /* Constructor */
    public function __construct()
    {
        require_once __DIR__ . '/../conexion.php'; //Llamo al archivo de las constantes de la base de datos
        $conexion = new Conexion();
        //$this->conexion = new mysqli(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS);
        $this->conexion = $conexion->iniciarBD();
    }

    /* Metodos */
    /**
     * Función consultar() para consultar la vivencia que el usuario elija.
     *
     * @param number $datosRecibidos
     * @return void
     */
    public function consultar($datosRecibidos)
    {
        $sql = 'SELECT * FROM Vivencias WHERE idVivencias=' . $datosRecibidos->idVivencia;
        $resultado = $this->conexion->query($sql);
        $consultarVivencia = array();
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            array_push(
                $consultarVivencia,
                [
                    "idVivencia" => $fila['idVivencias'],
                    "fechaCreacion" => $fila['fechaCreacion'],
                    "fechaModificación" => $fila['fechaModificacion'],
                    "imagen" => $fila['imagen'],
                    'texto' => $fila['texto'],
                    "idCuaderno" => $fila['idCuaderno'],
                    "idEtapa" => $fila['idEtapa']
                ]
            );
        }
        echo json_encode($consultarVivencia);
    }
    /**
     * Función listarVivencias() para listar las vivencias
     */
    public function listarVivencias()
    {
        $sql = 'SELECT * FROM Vivencias';
        $resultado = $this->conexion->query($sql);
        /* $prueba[] = 1;
        array_push($prueba,[1,2,3,4]);
        echo json_encode($prueba); */
        $listadoVivencias = array();
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            array_push(
                $listadoVivencias,
                [
                    "idVivencia" => $fila['idVivencias'],
                    "fechaCreacion" => $fila['fechaCreacion'],
                    "fechaModificación" => $fila['fechaModificacion'],
                    "imagen" => $fila['imagen'],
                    'texto' => $fila['texto'],
                    "idCuaderno" => $fila['idCuaderno'],
                    "idEtapa" => $fila['idEtapa']
                ]
            );

            /* $listadoVivencias = [
                "idVivencia" => $fila['idVivencias'],
                "fechaCreacion"=>$fila['fechaCreacion'],
                "fechaModificación" => $fila['fechaModificacion'],
                "rutaImagen" => $fila['rutaImagen'],
                'texto' => $fila['texto'],
                "idCuaderno" => $fila['idCuaderno'],
                "idEtapa" => $fila['idEtapa']
            ]; */
        }
        echo json_encode($listadoVivencias);
    }

    /**
     * Funcion listarEtapas() para mostrarlas en el select del alta de vivencias
     *
     * @return void
     */
    public function listarEtapas()
    {
        $sql = 'SELECT * FROM etapas';
        $resultado = $this->conexion->query($sql);
        /* $prueba[] = 1;
        array_push($prueba,[1,2,3,4]);
        echo json_encode($prueba); */
        $listadoEtapas = array();
        while ($fila = $resultado->fetch_array(MYSQLI_ASSOC)) {
            array_push(
                $listadoEtapas,
                [
                    "idEtapa" => $fila['idEtapa'],
                    "duracion" => $fila['duracion'],
                    "kilometros" => $fila['kilometros'],
                    "imgEtapa" => $fila['imgEtapa'],
                    'idPoblacionInicio' => $fila['idPoblacionInicio'],
                    "idPoblacionFin" => $fila['idPoblacionFin']
                ]
            );
        }
        echo json_encode($listadoEtapas);
    }

    /**
     * Función insertar() para insertar una nueva vivencia
     * @param datosRecibidos es el array de datos para insertar la Vivencia
     */
    public function insertar($datosRecibidos)
    {
        $idCuaderno = -1;

        session_start();

        if(isset($_SESSION["idCuaderno"])) {
            $idCuaderno = intval($_SESSION["idCuaderno"]);
        }

        $this->base64AImagen($datosRecibidos, $idCuaderno);


        $idEtapa = $datosRecibidos->idEtapa;
        $vivenciaIngresada = array();
        //Validamos que el texto sea NULL
        if ($datosRecibidos->texto == '') {
            $texto = 'null';
        } else {
            $texto = '"' . $datosRecibidos->texto . '"';
        }
        //Validamos la foto para datos NULL
        if ($datosRecibidos->foto == null) {
            $rutaImagen = 'null'; //Falta poner bien la imagen
        } else {
            $rutaImagen = $datosRecibidos->foto;
        }
        $sql = 'INSERT INTO Vivencias(fechaCreacion,fechaModificacion,imagen,texto,idCuaderno,idEtapa) VALUES (now(), now(), ' . $rutaImagen . ', ' . $texto . ','.$idCuaderno.',' . $idEtapa . ')';
        if ($this->conexion->query($sql)) {
            if ($this->conexion->affected_rows > 0) {
                array_push(
                    $vivenciaIngresada,
                    [
                        'idVivencia' => $this->conexion->insert_id,
                        'idEtapa' => $idEtapa,
                        'estado' => true
                    ]
                );
                echo json_encode($vivenciaIngresada);
            } else {
                array_push(
                    $vivenciaIngresada,
                    [
                        'estado' => false
                    ]
                );

                echo json_encode($vivenciaIngresada);
            }
        }
    }

    public function actualizar()
    {
        $this->conexion->query('UPDATE ... SET  ');
    }
    /**
     * Función borrar() para eliminar una vivencia
     * @param datosRecibidos es el id de la Vivencia que queremos eliminar
     */
    public function borrar($datosRecibidos)
    {
        $respuesta = array();
        $idVivencia = $datosRecibidos;
        $sql = 'DELETE FROM Vivencias WHERE idVivencias=' . $idVivencia;
        if ($this->conexion->query($sql)) {
            if ($this->conexion->affected_rows > 0) {
                array_push(
                    $respuesta,
                    [
                        "idVivencia" => $idVivencia,
                        "estado" => true
                    ]
                );
                echo json_encode($respuesta);
            } else {
                array_push(
                    $respuesta,
                    [
                        "idVivencia" => $idVivencia,
                        "estado" => false
                    ]
                );
                echo json_encode($respuesta);
            }
        }
    }

    /**
     * Método que crea una imagen a partir de unos datos en BASE64
     * @param data Cadena en base64
     * @return Ruta de la imagen...
     */
    function base64AImagen($data, $idCuaderno) {
        if(isset($data->foto)) {
            //Creamos la carpeta con el id del usuario...
            $ruta = "./userAssets/vivencias";

            //Si la carpeta no existe la creamos...
            if(!file_exists($ruta))
                mkdir($ruta,0777,true);

            $file = fopen("$ruta/vivencia$idCuaderno.png", "wb");

            //Si es un array iteramos sobre los elementos...
            if(gettype($data->foto) == "array") 
            {
                foreach ($data->foto as $key) {

                }
            } else {

            }
            echo json_encode($data->foto);

            //Actualizamos la fila de nuestro cuaderno con la nueva ruta
            $contraportada = null;
            if(isset($data->contraportada) && $data->contraportada != null) $contraportada = $data->contraportada;

            //Si la contraportada es null nos salimos y devolvemos null...
            if($contraportada == null) return null;

            $data = explode(',', $data->foto);

            //Crear imagen
            fwrite($file, base64_decode($data[1]));
            fclose($file);

            return $ruta;
        }
        return null;
    }

}
