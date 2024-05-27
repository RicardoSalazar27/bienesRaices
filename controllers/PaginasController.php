<?php
namespace Controllers;
use Model\Propiedad;
use MVC\Router;
use PHPMailer\PHPMailer\PHPMailer;

class PaginasController{
    public static function index(Router $router){
        $propiedadesListar = 3;
        $inicio = true;
        $propiedades = Propiedad::get($propiedadesListar);

        $router->render('paginas/index',[
            'propiedades' => $propiedades,
            'inicio' => $inicio
         ]);
    }

    public static function nosotros(Router $router){
        $router ->render('paginas/nosotros',[]);
    }

    public static function propiedades(Router $router){
        $propiedades = Propiedad::all();
        $router ->render('paginas/propiedades',[
            'propiedades' => $propiedades
        ]);
    }

    public static function propiedad(Router $router){
        $id = validarORedireccionar('propiedades');
        $propiedad = Propiedad::find($id);
        $router->render('paginas/propiedad',[
            'propiedad' => $propiedad
        ]);
    }

    public static function blog(Router $router){
        $router->render('paginas/blog');
    }

    public static function entrada(Router $router){
        $router->render('paginas/entrada');
    }

    public static function contacto(Router $router){

        $mensaje = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $respuestas = $_POST['contacto'];
            
            //Crear nueva instancia de phpMailer
            $mail = new PHPMailer();

            //Configurar SMTP
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'a5b437a0aea523';
            $mail->Password = '926dd049905375';
            $mail->SMTPSecure = 'tls';

            //Configurar el contenido del email
            $mail->setFrom('admin@bienesraices.com');
            $mail->addAddress('admin@bienesraices.com');
            $mail->Subject = 'Tienes un nuevo mensaje';

            //Habilitar HTML
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            //Definir el contenido
            $contenido = '<html>'; 
            $contenido.= '<p>Tienes un nuevo mensaje</p>';
            $contenido.= '<p>Nombre: ' . $respuestas['nombre'] .' </p>';
            
            //Enviar de forma condicional algunos campos de email o telefono
            if($respuestas['contacto'] === 'telefono'){
                $contenido.= '<p>Eligio ser contactado por Teléfono</p>';
                $contenido.= '<p>Telefono: ' . $respuestas['telefono'] .' </p>';
                $contenido.= '<p>Fecha Contacto: ' . $respuestas['fecha'] .' </p>';
                $contenido.= '<p>Hora: ' . $respuestas['hora'] .' </p>';
            } else{
                // Es email, entonces agreagmos el campo de email
                $contenido.= '<p>Eligio ser contactado por email</p>';
                $contenido.= '<p>Email: ' . $respuestas['email'] .' </p>';
            }
            $contenido.= '<p>Mensaje: ' . $respuestas['mensaje'] .' </p>';
            $contenido.= '<p>Vende o Compra: ' . $respuestas['tipo'] .' </p>';
            $contenido.= '<p>Precio o Presupuesto: $' . $respuestas['precio'] .' </p>';
            $contenido.= '<p>Prefiere ser contactado por: ' . $respuestas['contacto'] .' </p>';
            $contenido.= '</html>';
            $mail->Body = $contenido;
            $mail->AltBody = 'Esto es texto alternativo sin HTML';

            //Enviar el email
            if($mail->send()){
                $mensaje = "El Formulario se envio correctamente";
            } else{
                $mensaje = "el mensaje no se envio";
            }
        }
        $router->render('paginas/contacto',[
            'mensaje' => $mensaje
        ]);
    }
}

