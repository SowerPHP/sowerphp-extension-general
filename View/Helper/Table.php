<?php

/**
 * SowerPHP
 * Copyright (C) SowerPHP (http://sowerphp.org)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General Affero de GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General Affero de GNU para
 * obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace sowerphp\general;

/**
 * Helper para la creación de tablas en HTML
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2016-02-18
 */
class View_Helper_Table
{

    private $_id = null; ///< Identificador de la tabla
    private $_class = 'table table-striped'; ///< Atributo class para la tabla
    private $_export = false; ///< Crear o no datos para exportar
    private $_exportRemove = array(); ///< Datos que se removeran al exportar
    private $_display = null; ///< Indica si se debe o no mostrar la tabla
    private $_height = null; ///< Altura de la tabla en pixeles
    private $_colsWidth = []; ///< Ancho de las columnas en pixeles

    /**
     * Constructor de la clase para crear una tabla
     * @param table Datos con la tabla que se desea generar
     * @param id Identificador de la tabla
     * @param export Si se desea poder exportar los datos de la tabla
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2016-02-18
     */
    public function __construct($table = null, $id = null, $export = false, $display = null)
    {
        // si se paso una tabla se genera directamente y se imprime
        // esto evita una línea de programación em muchos casos
        if (is_array($table)) {
            $this->_id = $id;
            $this->_export = $export;
            $this->_display = $display;
            echo $this->generate($table);
        }
    }

    /**
     * Asigna un identificador a la tabla
     * @param id Identificador para asignar a la tabla
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-12-02
     */
    public function setId ($id)
    {
        $this->_id = $id;
    }

    /**
     * Asignar el atributo class para la tabla
     * @param class Atributo class (o varios) que se asignarán
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-12-02
     */
    public function setClass ($class)
    {
        $this->_class = $class;
    }

    /**
     * Asignar si se deberán generar o no iconos para exportar la tabla
     * @param export Flag para indicar si se debe o no exportar
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-12-02
     */
    public function setExport ($export = true)
    {
        $this->_export = $export;
    }

    /**
     * Definir que se deberá remover de la tabla antes de poder exportarla
     * @param remove Atributo con lo que se desea extraer antes de exportar
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-12-02
     */
    public function setExportRemove ($remove)
    {
        $this->_exportRemove = $remove;
    }

    /**
     * Asignar si se debe o no mostrar la tabla (o se usa más para mostrar)
     * @param display Flag para indicar si se debe o no mostrar la tabla
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-12-02
     */
    public function setDisplay ($display = true)
    {
        $this->_display = $display;
    }

    /**
     * Asignar la altura que podrá ocupar todo el contenedor (div) de la tabla
     * @param height Altura en pixeles del div
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-05-14
     */
    public function setHeight ($height = null)
    {
        $this->_height = $height;
    }

    /**
     * Asignar ancho de las columnas
     * @param width Arreglo con los anchos de las columnas (null si una columna debe ser automática)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-07-09
     */
    public function setColsWidth ($width = [])
    {
        $this->_colsWidth = $width;
    }

    /**
     * Método que genera la tabla en HTML a partir de un arreglo
     * @param table Tabla que se generará
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2016-02-10
     */
    public function generate ($table, $thead = 1)
    {
        // si el arreglo esta vacio o no es arreglo retornar nada
        if (!is_array($table) || !count($table)) {
            return null;
        }
        // Utilizar buffer para el dibujado, así lo retornaremos en vez
        // de imprimir directamente
        if ($this->_height)
            $buffer = '<div style="max-height:'.$this->_height.'px;overflow:auto">'."\n";
        else
            $buffer = '<div>'."\n";
        // Crear iconos para exportar y ocultar/mostrar tabla
        if ($this->_id!==null) {
            $buffer .= '<div class="tableIcons hidden-print" style="text-align:right">'."\n";
            $buffer .= $this->export($table);
            $buffer .= $this->showAndHide();
            $buffer .= '</div>'."\n";
        }
        // Iniciar tabla
        $buffer .= '<div style="width:100%;overflow:auto">'."\n";
        $buffer .= '<table class="'.$this->_class.'"'.($this->_id?' id="'.$this->_id.'"':'').'>'."\n";
        // Definir cabecera de la tabla
        // títulos de columnas
        $buffer .= "\t".'<thead>'."\n";
        $titles = array_shift($table);
        $buffer .= "\t\t".'<tr>'."\n";
        $i = 0;
        foreach ($titles as &$col) {
            if (isset($this->_colsWidth[$i]) && $this->_colsWidth[$i]!=null) {
                $w = ' style="width:'.$this->_colsWidth[$i].'px"';
            } else $w = '';
            $buffer .= "\t\t\t".'<th'.$w.'>'.$col.'</th>'."\n";
            $i++;
        }
        $buffer .= "\t\t".'</tr>'."\n";
        // extraer otras filas que son parte de la cabecera
        for ($i=1; $i<$thead; ++$i) {
            $titles = array_shift($table);
            $buffer .= "\t\t".'<tr>'."\n";
            foreach ($titles as &$col) {
                $buffer .= "\t\t\t".'<td>'.$col.'</td>'."\n";
            }
            $buffer .= "\t\t".'</tr>'."\n";
        }
        $buffer .= "\t".'</thead>'."\n";
        // Definir datos de la tabla
        $buffer .= "\t".'<tbody>'."\n";
        if (is_array($table)) {
            foreach ($table as &$row) {
                $buffer .= "\t\t".'<tr>'."\n";
                foreach ($row as &$col) {
                    $buffer .= "\t\t\t".'<td>'.$col.'</td>'."\n";
                }
                $buffer .= "\t\t".'</tr>'."\n";
            }
        }
        $buffer .= "\t".'</tbody>'."\n";
        // Finalizar tabla
        $buffer .= '</table>'."\n";
        $buffer .= '</div>'."\n";
        $buffer .= '</div>'."\n";
        // Retornar tabla en HTML
        return $buffer;
    }

    /**
     * Crea los datos de la sesión de la tabla para poder exportarla
     * @param table Tabla que se está exportando
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2016-02-18
     */
    private function export(&$table)
    {
        // si no se debe exportar retornar vacío
        if (!$this->_export)
            return '';
        // generar datos para la exportación
        $data = array();
        $nRow = 0;
        $nRows = count($table);
        foreach ($table as &$row) {
            $nRow++;
            if (isset($this->_exportRemove['rows'])) {
                if (
                    in_array($nRow, $this->_exportRemove['rows']) ||
                    in_array($nRow-$nRows-1, $this->_exportRemove['rows'])
                ) {
                    continue;
                }
            }
            $nCol = 0;
            $nCols = count($row);
            $aux = array();
            foreach ($row as &$col) {
                $nCol++;
                if (isset($this->_exportRemove['cols'])) {
                    if (
                        in_array($nCol, $this->_exportRemove['cols']) ||
                        in_array($nCol-$nCols-1, $this->_exportRemove['cols'])
                    ) {
                        continue;
                    }
                }
                $aux[] = $col;
            }
            $data[] = $aux;
        }
        // escribir datos para la exportación y colocar iconos si se logró
        // guardar en la caché
        $buffer = '';
        if ((new \sowerphp\core\Cache())->set('session.'.session_id().'.export.'.$this->_id, $data)) {
            $buffer .= '<div class="btn-group">';
            $buffer .= '<button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown" title="Guardar tabla como..."><i class="fa fa-download"></i> Guardar como...</button>';
            $buffer .= '<ul class="dropdown-menu slidedown">';
            $extensions = array('ods'=>'OpenDocument', 'csv'=>'Planilla CSV', 'xls'=>'Planilla Excel', 'pdf'=>'Documento PDF', 'xml'=>'Archivo XML', 'json'=>'Archivo JSON');
            foreach ($extensions as $e => $n) {
                $buffer .= '<li><a href="'._BASE.'/exportar/'.$e.'/'.$this->_id.'">'.$n.'</a></li>';
            }
            $buffer .= '</ul></div>'."\n";
        }
        return $buffer;
    }

    /**
     * Botones para mostrar y ocultar la tabla (+/-)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2016-02-18
     */
    public function showAndHide()
    {
        $buffer = '';
        if ($this->_display!==null) {
            $buffer .= '<button type="button" class="btn btn-default btn-default" onclick="$(\'#'.$this->_id.'\').show(); $(\'#tableShow'.$this->_id.'\').hide(); $(\'#tableHide'.$this->_id.'\').show();" id="tableShow'.$this->_id.'" title="Mostrar tabla"><i class="fa fa-plus-square-o"></i></button>';
            $buffer .= '<button type="button" class="btn btn-default btn-default" onclick="$(\'#'.$this->_id.'\').hide(); $(\'#tableHide'.$this->_id.'\').hide(); $(\'#tableShow'.$this->_id.'\').show();" id="tableHide'.$this->_id.'" title="Ocultar tabla"><i class="fa fa-minus-square-o"></i></button>';
            $buffer .= '<script type="text/javascript"> $(function() { ';
            if ($this->_display) {
                $buffer .= '$(\'#tableShow'.$this->_id.'\').hide();';
            } else {
                $buffer .= '$(\'#'.$this->_id.'\').hide(); $(\'#tableHide'.$this->_id.'\').hide();';
            }
            $buffer .= ' }); </script>';
        }
        return $buffer;
    }

}
