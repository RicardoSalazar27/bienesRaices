<?php

namespace Model;
use MVC\Router;

class ActiveRecord{
    // Base de datos
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';

    // Errores
    protected static $errores = [];

    //Definir la conexion a la Db
    public static function setDb($database){
        self::$db = $database;
     }
    
    public function guardar(){
        if(!is_null($this->id)){
            // actualizar 
            $this->actualizar();
        } else{
            // creando nuevo registro
            $this->crear();
        }
    }

    public function crear(){
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        //convertimos el arreglo a un texto plano //array_keys se trae las llaves del arreglo asociativo
        //$columnas = join(', ', array_keys($atributos));
        //$filas = join(', ', array_values($atributos));
  
        /*  INSERTAR EN LA BASE DE DATOS    */
        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('";
        $query .= join("', '", array_values($atributos));
        $query .= "') ";

        //*  Consulta para insertar datos
        //$query = "INSERT INTO propiedades($columnas) VALUES ('$filas')";

        $resultado = self::$db->query($query);

        if($resultado){//se valida si se agrego correctamente
            //UNA VEZ QUE HAYA SIDO CREADA LA CASA CORRECTAMENTE, SE REDICCIONARA AL USUARIO
            header('Location: /admin?resultado=1');//location:/bienesraices/admin/index.php?resultado=1
            //header('Location: /admin');
        }

    }

    public function actualizar(){
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value){
            $valores[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$tabla ." SET ";
        $query .= join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        $resultado = self::$db->query($query);

        if($resultado){//se valida si se agrego correctamente
            //UNA VEZ QUE HAYA SIDO CREADA LA CASA CORRECTAMENTE, SE REDICCIONARA AL USUARIO
            header('Location: /admin?resultado=2');
            //header('Location: /admin');
        }
    }

    // Eliminar un registro
    public function eliminar(){
        //ELIMINALA PROPIEDAD
        $query = "DELETE FROM " . static::$tabla ." WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        if($resultado){
            $this->borrarImagen();
            header('Location: /admin?resultado=3');
        }
    }

    //Identificar y unir los atributos de la BD
    public function atributos(){
        $atributos = [];
        foreach(static::$columnasDB as $columna){
            if($columna === 'id') continue;//igbnora el id y pasa al siguiente
            $atributos[$columna] = $this->$columna; 
        }
        return $atributos;
    }
 
    public function sanitizarAtributos(){
        $atributos = $this->atributos();
        $sanitizado = [];
        
        foreach($atributos as $key => $value){ //de esta forma paraacceder al atribuito y valor
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Subida de archivos
    public function setImagen($imagen){

        // Elimina su iamgen previa
        if(!is_null($this->id)){
            $this->borrarImagen();
        }
        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen){
            $this->imagen = $imagen;
        }
    }

    // Eliminar archivo
    public function borrarImagen(){
        // Comprobar si existe el archivo
        $existeArchivo = file_exists(CARPETAS_IMAGENES . $this->imagen);
        if($existeArchivo){
            unlink(CARPETAS_IMAGENES . $this->imagen);
        }
    }

    //Validacion
    public static function getErroes(){
        return static::$errores;
    }

    public function validar(){
        //VALIDACION DE CAMPOS VACIOS
        static::$errores = [];
        return static::$errores;
    }

    //Listar todas los registros
    public static function all(){
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
                          //$db->query($query);
        return $resultado;
    }

    //Obtiene determinado numero de registros
    public static function get($cantidad){
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
        $resultado = self::consultarSQL($query);
                          //$db->query($query);
        return $resultado;
    }
    //Buscar una registro por Id
    public static function find($id){
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
                          //$db->query($query);
        return array_shift($resultado);
    }

    public static function consultarSQL($query){
        //Consultar la bd
        $resultado = self::$db->query($query);

        //Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()){//nos trae un arreglo asociativo
            $array[] = static::crearObjeto($registro);
        }

        //Liberar la memoria
        $resultado->free();

        //Retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro){
        //$objeto = new self;//Instancia (objeto) de la clase "Propiedad"
        $objeto = new static;//crea una instancia en base a donde se esta heredando

        foreach($registro as $key => $value){//en cada iteracion el bucle toma una clave y un valor del arreglo que se pasara en $registro
            if(property_exists( $objeto, $key)){ //verifica si $key de registro, existe en el nuevo "objeto" de propiedad
                $objeto->$key = $value; //asigna al objeto el valor correspondiente al atributo
            }
        }
        return $objeto;//Hasta este punto me devuelve el objeto de la clase "Propiedad"
    }

    // Sincroniza el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar( $args =[]){
        foreach($args as $key => $value){//$args as titulo => hermosa chosa
            if(property_exists($this, $key) && !is_null($value)){ //Propiedad, titulo
                $this->$key = $value; //Propiedad->titulo = hermosa chosa
            }
        }
    }
}