<?php
namespace Controllers;
use MVC\Router;
use Model\Vendedor;

class VendedorController{
    public static function crear(Router $router){
        $errores = Vendedor::getErroes();

        $vendedor = new Vendedor();

        if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    
            // Crear una nueva instancia 
            $vendedor = new Vendedor($_POST['vendedor']);
    
            // Validar que no haya campos vacios
            $errores = $vendedor ->validar();
    
            // No hya errores
            if(empty($errores)){
                $vendedor->guardar();
            }
        }

        $router->render('vendedores/crear',[
            'vendedor' => $vendedor,
            'errores' => $errores

        ]);
    }

    public static function actualizar(Router $router){

        $errores = Vendedor::getErroes();
        $id = validarORedireccionar('/admin');

        //Obtener datos del vendedor a actualizar
        $vendedor = Vendedor::find($id);

        if($_SERVER['REQUEST_METHOD'] === 'POST' ) {
        
            // Asignar los valores
            $args = $_POST['vendedor'];
    
            //Sincronizar objeto en memoria con lo que el usuario escribio
            $vendedor->sincronizar($args);
    
            // Validacion
            $errores = $vendedor->validar();
    
            if(empty($errores)){
                $vendedor->guardar();
            }
        }    

        $router->render('vendedores/crear',[
            'vendedor' => $vendedor,
            'errores' => $errores

        ]);
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // Validar Id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
    
            if($id){
                // Valida el tipo a eliminar
                $tipo = $_POST['tipo'];
    
                if(validarTipoContenido($tipo)){
                    $vendedor = Vendedor::find($id);
                    $vendedor ->eliminar();
                }
            }
        }
    }
}