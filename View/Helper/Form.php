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
 * Helper para la creación de formularios en HTML
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-08-21
 */
class View_Helper_Form
{

    private $_id; ///< Identificador para el formulario
    private $_style; ///< Formato del formulario que se renderizará (mantenedor u false)

    /**
     * Método que inicia el código del formulario
     * @param style Estilo del formulario que se renderizará
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-05-10
     */
    public function __construct ($style = 'mantenedor')
    {
        $this->_style = $style;
    }

    /**
     * Método para asignar el estilo del formulario una vez ya se creo el objeto
     * @param style Estilo del formulario que se renderizará
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-08-21
     */
    public function setStyle($style = false)
    {
        $this->_style = $style;
    }
    
    /**
     * Método que inicia el código del formulario
     * @param config Arreglo con la configuración para el formulario
     * @return String Código HTML de lo solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-06-15
     */
    public function begin ($config = array())
    {
        // transformar a arreglo en caso que no lo sea
        if (!is_array($config)) {
            $config = array('action'=>$config);
        }
        // asignar configuración
        $config = array_merge(
            array(
                'id' => 'id',
                'action' => $_SERVER['REQUEST_URI'],
                'method'=> 'post',
                'onsubmit' => null,
                'focus' => null,
            ), $config
        );
        // crear onsubmit
        if ($config['onsubmit']) {
            $config['onsubmit'] = ' onsubmit="return '.$config['onsubmit'].'"';
        }
        // crear buffer
        $buffer = '';
        // si hay focus se usa
        if ($config['focus']) {
            $buffer .= '<script type="text/javascript"> $(function() { $("#'.$config['focus'].'Field").focus(); }); </script>'."\n";
        }
        // agregar formulario
        $buffer .= '<form action="'.$config['action'].'" method="'.$config['method'].'" enctype="multipart/form-data"'.$config['onsubmit'].' id="'.$config['id'].'">'."\n";
        // retornar
        return $buffer;
    }

    /**
     * Método que termina el código del formulario
     * @param config Arreglo con la configuración para el botón submit
     * @return String Código HTML de lo solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-05-10
     */
    public function end ($config = array())
    {
        // solo se procesa la configuración si no es falsa
        if ($config!==false) {
            // transformar a arreglo en caso que no lo sea
            if (!is_array($config))
                $config = array('value'=>$config);
            // asignar configuración
            $config['type'] = 'submit';
            $config = array_merge(
                array(
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => 'Enviar',
                    'label' => '',
                ), $config
            );
            // generar fin del formulario
            return $this->input($config).'</form>'."\n";
        } else {
            return '</form>'."\n";
        }
    }

    /**
     * Método que aplica o no un diseño al campo
     * @param field Campo que se desea formatear
     * @param config Arreglo con la configuración para el elemento
     * @return String Código HTML de lo solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-05-10
     */
    private function _formatear ($field, $config)
    {
        // si se debe aplicar estilo de mantenedor
        if (!in_array($config['type'], array('hidden')) && $config['style']=='mantenedor') {
            $buffer = '';
            // generar ayuda
            if ($config['help']!='') {
                $actions = 'onmouseover="$(\'#'.$config['name'].'FieldHelp\').dialog()" onmouseout="$(\'#'.$config['name'].'FieldHelp\').dialog(\'close\')"';
                $config['help'] =
                    ' <a href="#" class="helpIcon" onclick="return false" '.$actions.'>'.
                    '<img src="'._BASE.'/img/icons/16x16/actions/help.png" alt="" /></a>'.
                    '<div id="'.$config['name'].'FieldHelp" title="'.$config['label'].'" style="display:none" '.$actions.'>'.$config['help'].'</div>'
                ;
            }
            // generar campo
            $buffer .= '<div>'."\n";
            if (!empty($config['label'])) {
                if (!empty($config['name'])) {
                    $ast = $config['notempty'] ? '<span style="color:red">*</span> ' : '';
                    $buffer .= '<div class="label"><label for="'.$config['name'].'Field">'.$ast.$config['label'].'</label></div>'."\n";
                } else {
                    $buffer .= '<div class="label"><label>'.$config['label'].'</label></div>'."\n";
                }
            } else {
                $buffer .= '<div class="label">&nbsp;</div>'."\n";
            }
            $buffer .= '<div class="field">'.$field.$config['help'].'</div>'."\n";
            $buffer .= '</div>'."\n";
        }
        // si se debe alinear
        else if (isset($config['align'])) {
            $buffer = '<div style="text-align:'.$config['align'].'">'.$field.'</div>'."\n";
        }
        // si no se debe aplicar ningún formato solo agregar el campo dentro de un div y el EOL
        else {
            $buffer = '<div>'.$field.'</div>'."\n";
        }
        // retornar código formateado
        return $buffer;
    }

    /**
     * Método para crear una nuevo campo para que un usuario ingrese
     * datos a través del formulario, ya sea un tag: input, select, etc
     * @param config Arreglo con la configuración para el elemento
     * @return String Código HTML de lo solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-05-10
     */
    public function input ($config)
    {
        // transformar a arreglo en caso que no lo sea
        if (!is_array($config))
            $config = array('name'=>$config, 'label'=>$config);
        // asignar configuración
        $config = array_merge(
            array(
                'type'=>'text',
                'value'=>'',
                'autoValue'=>false,
                'class' => '',
                'attr' => '',
                'check' => null,
                'help' => '',
                'notempty' =>false,
                'style'=>$this->_style,
            ), $config
        );
        if (!isset($config['name']) && isset($config['id']))
            $config['name'] = $config['id'];
        // si no se indicó un valor y existe uno por POST se usa
        if (!isset($config['value'][0]) && isset($config['name']) && isset($_POST[$config['name']])) {
            $config['value'] = $_POST[$config['name']];
        }
        // si label no existe se usa el nombre de la variable
        if (!isset($config['label'])) $config['label'] = $config['name'];
        // si se paso check se usa
        if ($config['check']) {
            // si no es arreglo se convierte
            if (!is_array($config['check'])) $config['check'] = explode(' ',$config['check']);
            // hacer implode, agregar check y meter al class
            $config['class'] = $config['class'].' check '.implode(' ', $config['check']);
            if (in_array('notempty', $config['check']))
                $config['notempty'] = true;
        }
        // si se paso class se usa
        if ($config['class']!='') $config['class'] = ' class="'.$config['class'].'"';
        // generar buffer
        $buffer = $this->_formatear($this->{'_'.$config['type']}($config), $config);
        // retornar buffer
        return $buffer;
    }

    private function _submit ($config)
    {
        return '<input type="'.$config['type'].'" name="'.$config['name'].'" value="'.$config['value'].'" />';
    }

    private function _hidden ($config)
    {
        return '<input type="hidden" name="'.$config['name'].'" value="'.$config['value'].'" />';
    }

    private function _text ($config)
    {
        return '<input type="text" name="'.$config['name'].'" value="'.$config['value'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].' />';
    }

    private function _password($config)
    {
        return '<input type="password" name="'.$config['name'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].' />';
    }

    private function _textarea ($config)
    {
        $config = array_merge(
            array(
                'rows'=>5,
                'cols'=>10
            ), $config
        );
        return '<textarea name="'.$config['name'].'" rows="'.$config['rows'].'" cols="'.$config['cols'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].'>'.$config['value'].'</textarea>';
    }

    private function _checkbox ($config)
    {
        // si el valor por defecto se pasó en value se copia donde corresponde
        if (isset($_POST[$config['name']])) {
            $config['checked'] = true;
        }
        $checked = isset($config['checked']) && $config['checked'] ? 'checked="checked"' : '';
        return '<input type="checkbox" name="'.$config['name'].'" value="'.$config['value'].'" id="'.$config['name'].'Field" '.$checked.''.$config['class'].' '.$config['attr'].'/>';
    }

    /**
     * @todo No se está utilizando checked
     * @warning icono para ayuda queda abajo (por los <br/>)
     */
    private function _checkboxes ($config)
    {
        $buffer = '';
        foreach ($config['options'] as $key => &$value) {
            if (is_array($value)) {
                $key = array_shift($value);
                $value = array_shift($value);
            }
            $buffer .= '<input type="checkbox" name="'.$config['name'].'[]" value="'.$key.'" '.$config['class'].' '.$config['attr'].'/> '.$value.'<br />';
        }
        return $buffer;
    }

    private function _date ($config)
    {
        $config = array_merge (array(
            'yearFrom' => date('Y')-100,
            'yearTo' => date('Y')+1
        ), $config);
        $options = 'dateFormat: "yy-mm-dd", changeYear: true, yearRange: "'.$config['yearFrom'].':'.$config['yearTo'].'"';
        $buffer = '<script type="text/javascript">$(function() { $("#'.$config['name'].'Field").datepicker({ '.$options.' }); }); </script>';
        $buffer .= '<input type="text" name="'.$config['name'].'" value="'.$config['value'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].' />';
        return $buffer;
    }

    private function _file ($config)
    {
        return '<input type="file" name="'.$config['name'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].' />';
    }

    private function _select ($config)
    {
        $config = array_merge(array(
            'selected'=>''
        ), $config);
        // si el valor por defecto se pasó en value se copia donde corresponde
        if (isset($config['value'][0])) {
            $config['selected'] = $config['value'];
        }
        $buffer = '';
        $buffer .= '<select name="'.$config['name'].'" id="'.$config['name'].'Field"'.$config['class'].' '.$config['attr'].'>';
        foreach ($config['options'] as $key => &$value) {
            if (is_array($value)) {
                $key = array_shift($value);
                $value = array_shift($value);
            }
            $buffer .= '<option value="'.$key.'"'.($config['selected']==$key?' selected="selected"':'').'>'.$value.'</option>';
        }
        $buffer .= '</select>';
        return $buffer;
    }

    private function _radios ($config)
    {
        // si el valor por defecto se pasó en value se copia donde corresponde
        if (isset($config['value'][0])) {
            $config['checked'] = $config['value'];
        }
        $buffer = '';
        foreach ($config['options'] as $key => &$value) {
            if (is_array($value)) {
                $key = array_shift($value);
                $value = array_shift($value);
            }
            $checked = isset($config['checked']) && $config['checked']==$key ? 'checked="checked"' : '';
            $buffer .= ' <input type="radio" name="'.$config['name'].'" value="'.$key.'" '.$checked.'> '.$value.' ';
        }
        return $buffer;
    }

    private function _js ($config)
    {
        // configuración por defecto
        $config = array_merge(array('titles'=>array(), 'width'=>'100%'), $config);
        // respaldar formato
        $formato = $this->_style;
        $this->_style = null;
        // determinar inputs
        $inputs = '<tr>';
        foreach ($config['inputs'] as $input) {
            $input['name'] = $input['name'].'[]';
            $d = (isset($input['type']) && $input['type']=='hidden') ? ' style="display:none;"' : '';
            $inputs .= '<td'.$d.'>'.rtrim($this->input($input)).'</td>';
        }
        $inputs .= '<td><a href="" onclick="Form.delJS(this); return false" title="Eliminar"><img src="'._BASE.'/img/icons/16x16/actions/delete.png" alt="add" /></a></td>';
        $inputs .= '</tr>';
        // si no se indicaron valores, entonces se crea una fila con los campos vacíos
        if (!isset($config['values'])) {
            $values = $inputs;
        }
        // en caso que se cree el formulario con valores por defecto ya asignados
        else {
            $values = '';
            foreach ($config['values'] as $value) {
                $values .= '<tr>';
                foreach ($config['inputs'] as $input) {
                    $input['value'] = $value[$input['name']];
                    $input['name'] = $input['name'].'[]';
                    $d = (isset($input['type']) && $input['type']=='hidden') ? ' style="display:none;"' : '';
                    $values .= '<td'.$d.'>'.rtrim($this->input($input)).'</td>';
                }
                $values .= '<td><a href="" onclick="Form.delJS(this); return false" title="Eliminar"><img src="'._BASE.'/img/icons/16x16/actions/delete.png" alt="add" /></a></td>';
                $values .= '</tr>';
            }
        }
        // restaurar formato
        $this->_style = $formato;
        // generar tabla
        $buffer = '<script type="text/javascript"> window["inputsJS_'.$config['id'].'"] = \''.$inputs.'\'; </script>'."\n";
        $buffer .= '<table id="'.$config['id'].'" class="formTable" style="width:'.$config['width'].'">';
        $buffer .= '<thead><tr>';
        foreach ($config['titles'] as &$title) {
            $buffer .= '<th>'.$title.'</th>';
        }
        $buffer .= '<th><a href="javascript:Form.addJS(\''.$config['id'].'\')" title="Agregar [+]" accesskey="+"><img src="'._BASE.'/img/icons/16x16/actions/add.png" alt="add" /></a></th>';
        $buffer .= '</tr></thead>';
        $buffer .= '<tbody>'.$values.'</tbody>';
        $buffer .= '</table>';
        return $buffer;
    }

    private function _tablecheck ($config)
    {
        // configuración por defecto
        $config = array_merge([
            'id'=>$config['name'],
            'titles'=>array(),
            'width'=>'100%',
            'mastercheck'=>true,
            'checked'=>(isset($_POST[$config['name']])?$_POST[$config['name']]:[])
        ], $config);
        if (!isset($config['key']))
            $config['key'] = array_keys($config['table'][0])[0];
        if (!is_array($config['key']))
            $config['key'] = array($config['key']);
        $buffer = '<table id="'.$config['id'].'" class="formTable" style="width:'.$config['width'].'">';
        $buffer .= '<tr>';
        foreach ($config['titles'] as &$title) {
            $buffer .= '<th>'.$title.'</th>';
        }
        $checked = $config['mastercheck'] ? ' checked="checked"' : '';
        $buffer .= '<th><input type="checkbox"'.$checked.' onclick="Form.checkboxesSet(\''.$config['name'].'\', this.checked)"/></th>';
        $buffer .= '</tr>';
        foreach ($config['table'] as &$row) {
            // determinar la llave
            $key = array();
            foreach ($config['key'] as $k) {
                $key[] = $row[$k];
            }
            $key = implode (';', $key);
            // agregar fila
            $buffer .= '<tr>';
            foreach ($row as &$col) {
                $buffer .= '<td>'.$col.'</td>';
            }
            $checked = in_array($key, $config['checked']) ? ' checked="checked"' : '' ;
            $buffer .= '<td><input type="checkbox" name="'.$config['name'].'[]" value="'.$key.'"'.$checked.' /></td>';
            $buffer .= '</tr>';
        }
        $buffer .= '</table>';
        return $buffer;
    }

    private function _tableradios ($config)
    {
        // configuración por defecto
        $config = array_merge(array('id'=>$config['name'], 'titles'=>array(), 'width'=>'100%'), $config);
        $buffer = '<table id="'.$config['id'].'" class="tableradios" style="width:'.$config['width'].'">';
        $buffer .= '<tr>';
        foreach ($config['titles'] as &$title) {
            $buffer .= '<th>'.$title.'</th>';
        }
        foreach ($config['options'] as &$option) {
            $buffer .= '<th><div><span>'.$option.'</span></div></th>';
        }
        $buffer .= '</tr>';
        $options = array_keys($config['options']);
        foreach ($config['table'] as &$row) {
            $key = array_shift($row);
            // agregar fila
            $buffer .= '<tr>';
            foreach ($row as &$col) {
                $buffer .= '<td>'.$col.'</td>';
            }
            foreach ($options as &$value) {
                if (isset($_POST[$config['name'].'_'.$key]) && $_POST[$config['name'].'_'.$key]==$value)
                    $checked = 'checked="checked" ';
                else $checked = '';
                $buffer .= '<td><input type="radio" name="'.$config['name'].'_'.$key.'" value="'.$value.'" '.$checked.'/></td>';
            }
            $buffer .= '</tr>';
        }
        $buffer .= '</table>';
        return $buffer;
    }

}
