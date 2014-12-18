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
 * @version 2014-12-10
 */
class View_Helper_Form
{

    private $_id; ///< Identificador para el formulario
    private $_style; ///< Formato del formulario que se renderizará (mantenedor u false)

    /**
     * Método que inicia el código del formulario
     * @param style Estilo del formulario que se renderizará
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-12-10
     */
    public function __construct($style = 'horizontal')
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
     * @version 2014-12-10
     */
    public function begin($config = [])
    {
        // transformar a arreglo en caso que no lo sea
        if (!is_array($config)) {
            $config = ['action'=>$config];
        }
        // asignar configuración
        $config = array_merge(
            [
                'id' => 'id',
                'action' => $_SERVER['REQUEST_URI'],
                'method'=> 'post',
                'onsubmit' => null,
                'focus' => null,
                'attr' => '',
            ], $config
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
        $buffer .= '<form action="'.$config['action'].'" method="'.$config['method'].'" enctype="multipart/form-data"'.$config['onsubmit'].' id="'.$config['id'].'" '.$config['attr'].' class="form-horizontal" role="form">'."\n";
        // retornar
        return $buffer;
    }

    /**
     * Método que termina el código del formulario
     * @param config Arreglo con la configuración para el botón submit
     * @return String Código HTML de lo solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-12-10
     */
    public function end($config = [])
    {
        // solo se procesa la configuración si no es falsa
        if ($config!==false) {
            // transformar a arreglo en caso que no lo sea
            if (!is_array($config))
                $config = ['value'=>$config];
            // asignar configuración
            $config['type'] = 'submit';
            $config = array_merge(
                [
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => 'Enviar',
                    'label' => '',
                ], $config
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
     * @version 2014-12-10
     */
    private function _formatear($field, $config)
    {
        // si es campo oculto no se aplica ningún estilo
        if ($config['type'] == 'hidden') {
            $buffer = $field."\n";
        }
        // si se debe aplicar estilo de mantenedor
        else if ($config['style']=='horizontal') {
            if ($config['help']!='')
                $config['help'] = ' <p class="help-block">'.$config['help'].'</p>';
            $buffer = '    <div class="form-group'.($config['notempty']?' required':'').'">'."\n";
            if (!empty($config['label'])) {
                if (!empty($config['name']) && substr($config['name'], -2)!='[]') {
                    $buffer .= '        <label for="'.$config['name'].'Field" class="col-sm-2 control-label">'.$config['label'].'</label>'."\n";
                } else {
                    $buffer .= '        <label class="col-sm-2 control-label">'.$config['label'].'</label>'."\n";
                }
            }
            if (!in_array($config['type'], ['submit'])) {
                $buffer .= '        <div class="col-sm-10">'.$field.$config['help'].'</div>'."\n";
            } else {
                $buffer .= '        <div class="col-sm-offset-2 col-sm-10">'.$field.$config['help'].'</div>'."\n";
            }
            $buffer .= '    </div>'."\n";
        }
        // si se debe alinear
        else if (isset($config['align'])) {
            $buffer = '<div style="text-align:'.$config['align'].'">'.$field.'</div>'."\n";
        }
        // si se debe usar estilo inline
        else if ($config['style']=='inline') {
            $buffer = '<div style="display:inline">'.$field.'</div>'."\n";
        }
        // si no se debe aplicar ningún formato solo agregar el campo dentro de un div y el EOL
        else {
            $buffer = '<div>'.$field.'</div>';
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
     * @version 2014-12-18
     */
    public function input($config)
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
                'placeholder' => '',
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
        // generar buffer
        if (!in_array($config['type'], ['submit', 'checkbox', 'file'])) {
            $config['class'] = (!empty($config['class']) ? $config['class'] : '').' form-control';
        }
        $buffer = $this->_formatear($this->{'_'.$config['type']}($config), $config);
        // retornar buffer
        return $buffer;
    }

    private function _submit ($config)
    {
        return '<input type="'.$config['type'].'" name="'.$config['name'].'" value="'.$config['value'].'" class="'.$config['class'].' btn btn-default" />';
    }

    private function _hidden ($config)
    {
        return '<input type="hidden" name="'.$config['name'].'" value="'.$config['value'].'" />';
    }

    private function _text ($config)
    {
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        return '<input type="text" name="'.$config['name'].'" value="'.$config['value'].'"'.$id.' class="'.$config['class'].'" placeholder="'.$config['placeholder'].'" '.$config['attr'].' />';
    }

    private function _password($config)
    {
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        return '<input type="password" name="'.$config['name'].'"'.$id.' class="'.$config['class'].'" '.$config['attr'].' />';
    }

    private function _textarea ($config)
    {
        $config = array_merge(
            array(
                'rows'=>5,
                'cols'=>10
            ), $config
        );
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        return '<textarea name="'.$config['name'].'" rows="'.$config['rows'].'" cols="'.$config['cols'].'"'.$id.' class="'.$config['class'].'" placeholder="'.$config['placeholder'].'" '.$config['attr'].'>'.$config['value'].'</textarea>';
    }

    private function _checkbox ($config)
    {
        // si el valor por defecto se pasó en value se copia donde corresponde
        if (isset($_POST[$config['name']])) {
            $config['checked'] = true;
        }
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        $checked = isset($config['checked']) && $config['checked'] ? ' checked="checked"' : '';
        return '<input type="checkbox" name="'.$config['name'].'" value="'.$config['value'].'"'.$id.$checked.' class="'.$config['class'].'" '.$config['attr'].'/>';
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
            $buffer .= '<input type="checkbox" name="'.$config['name'].'[]" value="'.$key.'" class="'.$config['class'].'" '.$config['attr'].'/> '.$value.'<br />';
        }
        return $buffer;
    }

    private function _date ($config)
    {
        $config['datepicker'] = array_merge(
            (array)\sowerphp\core\Configure::read('datepicker'),
            isset($config['datepicker']) ? $config['datepicker'] : []
        );
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        $buffer = '<script type="text/javascript">$(function() { $("#'.$config['name'].'Field").datepicker('.json_encode($config['datepicker']).'); }); </script>';
        $buffer .= '<input type="text" name="'.$config['name'].'" value="'.$config['value'].'"'.$id.' class="'.$config['class'].'" '.$config['attr'].' />';
        return $buffer;
    }

    private function _file ($config)
    {
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        return '<input type="file" name="'.$config['name'].'"'.$id.' class="'.$config['class'].'" '.$config['attr'].' />';
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
        $id = substr($config['name'], -2)!='[]' ? ' id="'.$config['name'].'Field"' : '';
        $buffer = '';
        $buffer .= '<select name="'.$config['name'].'"'.$id.' class="'.$config['class'].'" '.$config['attr'].'>';
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

    private function _js ($config, $js = true)
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
        if ($js) {
            $inputs .= '<td><a href="" onclick="Form.delJS(this); return false" title="Eliminar"><img src="'._BASE.'/img/icons/16x16/actions/delete.png" alt="add" /></a></td>';
        }
        $inputs .= '</tr>';
        // si no se indicaron valores se tratan de determinar
        if (!isset($config['values'])) {
            if (isset($_POST[$config['inputs'][0]['name']])) {
                $values = '';
                $filas = count($_POST[$config['inputs'][0]['name']]);
                for ($i=0; $i<$filas; $i++) {
                    $values .= '<tr>';
                    foreach ($config['inputs'] as $input) {
                        $input['value'] = $_POST[$input['name']][$i];
                        $input['name'] = $input['name'].'[]';
                        $d = (isset($input['type']) && $input['type']=='hidden') ? ' style="display:none;"' : '';
                        $values .= '<td'.$d.'>'.rtrim($this->input($input)).'</td>';
                    }
                    if ($js) {
                        $values .= '<td><a href="" onclick="Form.delJS(this); return false" title="Eliminar"><img src="'._BASE.'/img/icons/16x16/actions/delete.png" alt="add" /></a></td>';
                    }
                    $values .= '</tr>';
                }
            }
            // si no hay valores por post se crea una fila con los campos vacíos
            else $values = $inputs;
        }
        // en caso que se cree el formulario con valores por defecto ya asignados
        else {
            $values = '';
            foreach ($config['values'] as $value) {
                $values .= '<tr>';
                foreach ($config['inputs'] as $input) {
                    if (isset($input['type']) && $input['type']=='checkbox')
                        $input['checked'] = $value[$input['name']];
                    else if (isset($input['type']) && $input['type']=='select')
                        $input['selected'] = $value[$input['name']];
                    else
                        $input['value'] = $value[$input['name']];
                    $input['name'] = $input['name'].'[]';
                    $d = (isset($input['type']) && $input['type']=='hidden') ? ' style="display:none;"' : '';
                    $values .= '<td'.$d.'>'.rtrim($this->input($input)).'</td>';
                }
                if ($js) {
                    $values .= '<td><a href="" onclick="Form.delJS(this); return false" title="Eliminar"><img src="'._BASE.'/img/icons/16x16/actions/delete.png" alt="add" /></a></td>';
                }
                $values .= '</tr>';
            }
        }
        // restaurar formato
        $this->_style = $formato;
        // generar tabla
        $buffer = '';
        if ($js) {
            $buffer .= '<script type="text/javascript"> window["inputsJS_'.$config['id'].'"] = \''.$inputs.'\'; </script>'."\n";
        }
        $buffer .= '<table id="'.$config['id'].'" class="formTable" style="width:'.$config['width'].'">';
        $buffer .= '<thead><tr>';
        foreach ($config['titles'] as &$title) {
            $buffer .= '<th>'.$title.'</th>';
        }
        if ($js) {
            $buffer .= '<th><a href="javascript:Form.addJS(\''.$config['id'].'\')" title="Agregar [+]" accesskey="+"><img src="'._BASE.'/img/icons/16x16/actions/add.png" alt="add" /></a></th>';
        }
        $buffer .= '</tr></thead>';
        $buffer .= '<tbody>'.$values.'</tbody>';
        $buffer .= '</table>';
        return $buffer;
    }

    private function _tablecheck ($config)
    {
        if(!isset($config['table'][0]))
            return '-';
        // configuración por defecto
        $config = array_merge([
            'id'=>$config['name'],
            'titles'=>array(),
            'width'=>'100%',
            'mastercheck'=>false,
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

    private function _div ($config)
    {
        return '<div'.(!empty($config['attr'])?' '.$config['attr']:'').'>'.$config['value'].'</div>';
    }

    private function _table($config)
    {
        return $this->_js($config, false);
    }

}
