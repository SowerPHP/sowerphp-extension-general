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
 * Helper para la creación de código HTML genérico a través de métodos estáticos
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-10-01
 */
class View_Helper_HTML
{

    /**
     * Método para cargar todos los archivos de un directorio como código y
     * "pintarlo" mediante http://google-code-prettify.googlecode.com
     * @param src Archivo o directorio (fullpath)
     * @param recursive Procesar (o no) de forma recursiva el directorio src
     * @param ext Procesar solo extensiones indicadas
     * @param header Header desde el cual se desea utilizar (ej: 2, que es <h2>)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2013-06-27
     */
    public static function codeFrom ($src, $recursive = false, $ext = array(), $header = 2)
    {
        // en caso que sea un directorio se recorre recursivamente
        if (is_dir($src)) {
            // si se limitan extensiones
            if (count($ext)) $restrictExtensions = true;
            else $restrictExtensions = false;
            // buscar archivos
            $files = scandir($src);
            foreach ($files as &$file) {
                // si es archivo oculto se omite
                if ($file[0]=='.') continue;
                // si se limitan extensiones y no esta en las permitidas saltar
                if ($restrictExtensions && !in_array(substr($file, strrpos($file, '.')+1), $ext)) {
                    continue;
                }
                // si es un directorio, verificar que se deba procesar
                // recursivamente, sino se veran solo archivos
                if (is_dir($src.'/'.$file) && !$recursive) continue;
                // si es un directorio colocar el nombre del directorio
                if (is_dir ($src.'/'.$file)) {
                    $permlink = string2url($file);
                    echo "<h$header id=\"$permlink\">$file <a href=\"#$permlink\">&lt;&gt;</a></h$header>";
                    $h = $header + 1;
                }
                // llamar a la función por cada archivo
                self::codeFrom($src.'/'.$file, $recursive, $ext, $header);
            }
        }
        // si no es directorio entonces es un archivo, se muestra
        else {
            echo '<div><strong>',basename($src),'</strong></div>';
            echo '<pre class="prettyprint"><code>';
            echo htmlspecialchars(file_get_contents($src));
            echo '</code></pre>',"\n\n";
        }
    }

    /**
     * Función para crear enlaces hacia archivos que se encuentran en un
     * directorio visible desde el DocummentRoot, la ruta que se debe indicar en
     * dir debe ser la ruta completa que se vería desde dominio.com/full/path, o
     * sea (en este caso) dir = /full/path
     * @param dir Directorio donde están los archivos que se desean enlazar
     * @param recursive Procesar (o no) de forma recursiva el directorio dir
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public static function linksFrom ($dir, $recursive = false)
    {
        $realdir = DIR_WEBSITE.'/webroot'.$dir;
        if (!is_dir($realdir)) {
            echo '<p>No es posible leer el directorio de archivos.</p>';
            return;
        }
        $files = scandir($realdir);
        echo '<ul>',"\n";
        // procesar cada archivo
        foreach ($files as &$file) {
            // si es archivo oculto o no hay permiso de lectura => se omite
            if ($file[0]=='.' || !is_readable($realdir.'/'.$file)) continue;
            // si es un directorio
            if (is_dir($realdir.'/'.$file)) {
                // verificar que se deba procesar recursivamente, sino se veran
                // solo archivos
                if (!$recursive) continue;
                // mostrar directorio y llamar función de forma recursiva
                echo '<li style="list-style-image: url(\'',_BASE,'/img/icons/16x16/files/directory.png\')">';
                echo '<span style="display:block;margin-bottom:1em">',str_replace(array('_', '-'), ' ', $file),'</span>',"\n";
                self::linksFrom($dir.'/'.$file, $recursive);
                echo '</li>',"\n";
            }
            // si es un archivo
            else {
                // definir nombre y extensión
                if (strrchr($file, '.')!==FALSE) {
                    $ext = substr(strrchr($file, '.'), 1);
                    $name = str_replace(array('_', '-'), ' ', preg_replace("/.$ext$/", '', $file));
                } else {
                    $ext = '';
                    $name = str_replace(array('_', '-'), ' ', $file);
                }
                // buscar icono a partir de la extension
                $icon = App::location('webroot/img/icons/16x16/files/'.$ext.'.png');
                if($icon) $icon = 'img/icons/16x16/files/'.$ext.'.png';
                else $icon = 'img/icons/16x16/files/generic.png';
                // mostrar enlace
                echo '<li style="list-style-image: url(\'',_BASE,'/',$icon,'\')"><a href="',_BASE.$dir,'/',$file,'">',$name,'</a></li>',"\n";
            }
        }
        echo '</ul>',"\n";
    }

    /**
     * @author http://stackoverflow.com/questions/1960461/convert-plain-text-urls-into-html-hyperlinks-in-php
     */
    public static function makeClickableLinks($s)
    {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1">$1</a>', $s);
    }

    /**
     * Muestra logos e información sobre ellos de una forma "linda", para un
     * ejemplo revisar: http://sasco.cl/clientes
     *
     * El arreglo que se recibe tiene la forma:
     * $logos = array (
     *      'Empresa' => array (
     *              'desc' => 'Descripción empresa',
     *              'info' => array ('Info 1', 'Info 2', 'etc'),
     *              'imag' => 'Imagen dentro de $dir'
     *      ),
     * );
     *
     * @param logos Arreglo con la información de los logos
     * @param dir Ruta completa de la URL donde se encuentran los logos
     * @param infoTitle Título para la información que se mostrará por logo
     */
    public static function boxAnimated ($logos, $dir, $infoTitle = '')
    {
        // si es la primera vez que se llama la función se agrega código css y js
        if (!defined('LOGO_INFO_CALLED')) {
            echo '<link type="text/css" href="',_BASE,'/css/boxAnimated.css" media="screen" title="screen" rel="stylesheet" />',"\n";
            echo '<script type="text/javascript" src="',_BASE,'/js/boxAnimated.js"></script>',"\n";
            define ('LOGO_INFO_CALLED', true);
        }
        // mostrar logos
        foreach ($logos as $name => &$info) {
            echo '
<div class="boxAnimated">
    <div class="image">
        <div class="inner">
            <img src="',$dir,'/',$info['imag'],'" alt="" />
            <div class="longdescription">
                <div class="title">',$infoTitle,'</div>
                <div class="description">
                    <ul>
                        <li>',implode('</li><li>', $info['info']),'</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="shortdescription">
        <div class="title">',$name,'</div>
        <div class="description">',$info['desc'],'</div>
    </div>
</div>
            ';
        }
    }

}
