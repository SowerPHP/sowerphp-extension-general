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

class Controller_Exportar extends \Controller_App
{

    public function beforeFilter ()
    {
    }

    public function ods ($id)
    {
        $this->_exportTable($id);
    }

    public function xls ($id)
    {
        $this->_exportTable($id);
    }

    public function csv ($id)
    {
        $this->_exportTable($id);
    }

    public function pdf ($id)
    {
        $this->_exportTable($id);
    }

    public function xml ($id)
    {
        $this->_exportTable($id);
    }

    public function json ($id)
    {
        $this->_exportTable($id);
    }

    private function _exportTable ($id)
    {
        $data = (new \sowerphp\core\Cache())->get('session.'.session_id().'.export.'.$id);
        if (!$data) {
            throw new Exception_Data_Missing(['id'=>$id]);
        }
        $this->set(array(
            'id' => $id,
            'data' => $data,
        ));
    }

    public function barcode ($string, $type = 'C128')
    {
        $this->set(array(
            'string' => base64_decode($string),
            'type' => $type,
        ));
    }

    public function qrcode ($string)
    {
        $this->set('string', base64_decode($string));
    }

    public function pdf417 ($string)
    {
        $this->set('string', base64_decode($string));
    }

}
