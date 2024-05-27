<?php

namespace Controllers;
use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;

class PropiedadController {
    public static function index(Router $router){

        $propiedades = Propiedad::all();
        $vendedores = Vendedor::all();
        
        //mensaje conmdicional
        $resultado = $_GET['resultado'] ?? NULL; //NULL PARA QUE TENGA UN VALOR VACIO Y EXISTA

        $router->render('propiedades/admin',[
            'propiedades' => $propiedades,
            'resultado' => $resultado,
            'vendedores' => $vendedores
        ]);
    }

    public static function crear(Router $router){

        $propiedad = new Propiedad();
        $vendedores = Vendedor::all();

        //ARREGLO CON MENSAJES DE ERROES
        $errores = Propiedad::getErroes();

         //EJECUTAR EL CODIGO DESPUES DE QUE EL USUARIO ENVIA EL FORMULARIO
    if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
        
        /* Crea una nueva instancia */
        $propiedad = new Propiedad($_POST['propiedad']);
    
        //GENERAR UN NOMBRE UNICO PARA LA IMAGEN
        $nombreImagen = md5( uniqid( rand(), true)) . ".jpg";
        
        // Setear la imagen
        // Realiza un resize a la imagen con intervention
        if($_FILES['propiedad']['tmp_name']['imagen']){ //imagen es el name en el formulario
            $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
            $propiedad->setImagen($nombreImagen);
        }
        
        // Validar
        $errores = $propiedad->validar();
        
        //REVISAR QUE EL ARREGLO DE ERRORES ESTE VACIO
        if(empty($errores)){
        
            //Crear la carpeta para subir imagenes
            if(!is_dir(CARPETAS_IMAGENES)) {
                mkdir(CARPETAS_IMAGENES);
            }

            // Guarda la imagen en el servidor
            $image->save(CARPETAS_IMAGENES . $nombreImagen);

            //Guarda en la base de datos
            $propiedad->guardar();//esto devuelve un true o false

        }
    }

        $router->render('propiedades/crear',[
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }

    public static function actualizar(Router $router){
        $id = validarORedireccionar('/admin');

        $propiedad = Propiedad::find($id);
        $vendedores = Vendedor::all();

        //ARREGLO CON MENSAJES DE ERROES
        $errores = Propiedad::getErroes();

        // METODO POST PARA ACTUALIZAR CUANDO SE ENVIE EL FOMRULARIO 
        if($_SERVER['REQUEST_METHOD'] === 'POST' ) {

            // Asignar los atributos
            $args = $_POST['propiedad'];

            $propiedad->sincronizar($args); //$args funciona igual usando solo $_POST

            // Validacion
            $errores = $propiedad->validar();

            //GENERAR UN NOMBRE UNICO PARA LA IMAGEN
            $nombreImagen = md5( uniqid( rand(), true)) . ".jpg";
            
            // Subida de archivos
            if($_FILES['propiedad']['tmp_name']['imagen']){ //imagen es el name en el formulario
                $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
                $propiedad->setImagen($nombreImagen);
            }

            //REVISAR QUE EL ARREGLO DE ERRORES ESTE VACIO
            if(empty($errores)){
                // Almacena la imagen
                if($_FILES['propiedad']['tmp_name']['imagen']){
                $image->save(CARPETAS_IMAGENES . $nombreImagen);
                }
                $propiedad->guardar();
            }
        }

        $router->render('/propiedades/actualizar',[
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);   
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // Validar Id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
    
            if($id){
                //Valida eltipo a eliminar
                $tipo = $_POST['tipo'];
    
                if(validarTipoContenido($tipo)){
                    $propiedad = Propiedad::find($id);
                    $propiedad ->eliminar();
                }
            }
        }
    }
}
