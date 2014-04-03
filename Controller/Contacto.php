<?php

/**
 * SowerPHP: Minimalist Framework for PHP
 * Copyright (C) SowerPHP (http://sowerphp.org)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General GNU para obtener
 * una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/gpl.html>.
 */

namespace sowerphp\general;

/**
 * Controlador para página de contacto
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-05-26
 */
class Controller_Contacto extends \Controller_App
{

    /**
     * Método para autorizar la carga de index en caso que hay autenticación
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-13
     */
    public function beforeFilter ()
    {
    }

    /**
     * Método que desplegará y procesará el formulario de contacto
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-26
     */
    public function index ()
    {
        // si no hay datos para el envió del correo electrónico no
        // permirir cargar página de contacto
        if (\sowerphp\core\Configure::read('email.default')===NULL) {
            \sowerphp\core\Model_Datasource_Session::message(
                'Página de contacto deshabilitada'
            );
            $this->redirect('/');
        }
        // si se envió el formulario se procesa
        if (isset($_POST['submit'])) {			
            $email = new \sowerphp\core\Network_Email();
            $email->replyTo($_POST['correo'], $_POST['nombre']);
            $email->to(\sowerphp\core\Configure::read('email.default.to'));
            $email->subject('Contacto desde la web #'.date('YmdHis'));
            if (!is_array($email->send($_POST['mensaje']))) {
                \sowerphp\core\Model_Datasource_Session::message(
                    'Su mensaje ha sido enviado, se responderá a la brevedad.'
                );
            } else {
                \sowerphp\core\Model_Datasource_Session::message(
                    'Ha ocurrido un error al intentar enviar su mensaje, por favor intente nuevamente.'
                );
            }
            $this->redirect('/contacto');
        }
    }

}
