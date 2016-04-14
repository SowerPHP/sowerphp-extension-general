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
 * Clase para trabajar con fechas
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2015-07-20
 */
class Utility_Date
{

    public static $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    public static $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    /**
     * Método que suma días hábiles a una fecha
     * @param fecha Desde donde empezar
     * @param dias Días que se deben sumar a la fecha
     * @param feriados Días que no se deberán considerar al sumar
     * @return Fecha con los días hábiles sumados
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-09-07
     */
    public static function addWorkingDays($fecha, $dias, $feriados = [])
    {
        // mover fecha los días solicitados
        $start = $end = strtotime($fecha);
        $dia = date('N', $start);
        if ($dias==0) {
            if ($dia==6) $end = $start + 2*86400;
            else if ($dia==7) $end = $start + 86400;
        } else {
            $total = $dia + $dias;
            $fds = (int)($total/5) * 2;
            if ($total%5==0) $fds -= 2;
            $end = $start + ($dias+$fds)*86400;
        }
        $nuevaFecha = date('Y-m-d', $end);
        // ver si hay feriados, por cada feriado encontrado mover un día hábil
        // la fecha, hacer esto hasta que no hayan más días feriados en el rango
        // que se movió la fecha
        while (($dias=self::countDaysMatch($fecha, $nuevaFecha, $feriados, true))!=0) {
            $fecha = date('Y-m-d', strtotime($nuevaFecha)+86400);
            $nuevaFecha = self::addWorkingDays($nuevaFecha, $dias);
        }
        // retornar fecha
        return $nuevaFecha;
    }

    /**
     * Método que resta días hábiles a una fecha
     * @param fecha Desde donde empezar
     * @param dias Días que se deben restar a la fecha
     * @param feriados Días que no se deberán considerar al restar
     * @return Fecha con los días hábiles restados
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-10
     */
    public static function subtractWorkingDays($fecha, $dias, $feriados = [])
    {
        // mover fecha los días solicitados
        $start = $end = strtotime($fecha);
        $dia = date('N', $start);
        if ($dias==0) {
            if ($dia==6) $end = $start - 86400;
            else if ($dia==7) $end = $start - 2*86400;
        } else {
            $total = $dia - $dias;
            $fds = $total > 0 ? (int)(abs($total)/5) * 2 : (int)(abs($total)/5) * 2 + 2;
            $end = $start - ($dias+$fds)*86400;
        }
        $nuevaFecha = date('Y-m-d', $end);
        // ver si hay feriados, por cada feriado encontrado mover un día hábil
        // la fecha, hacer esto hasta que no hayan más días feriados en el rango
        // que se movió la fecha
        while (($dias=self::countDaysMatch($nuevaFecha, $fecha, $feriados, true))!=0) {
            $fecha = date('Y-m-d', strtotime($nuevaFecha)-86400);
            $nuevaFecha = self::subtractWorkingDays($nuevaFecha, $dias);
        }
        // retornar fecha
        return $nuevaFecha;
    }

    /**
     * Método que obtiene el número de día hábil dentro de un mes que
     * corresponde el día de la fecha que se está pasando
     * @param fecha Fecha que se quiere saber que día hábil del mes correspone
     * @param feriados Arreglo con los feriados del mes (si no se pasa solo se omitirán fin de semanas)
     * @return Número de día hábil del mes que corresponde la fecha pasada o =false si no es día hábil
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-06-21
     */
    public static function whatWorkingDay($fecha, $feriados = [])
    {
        list($anio, $mes, $dia) = explode('-', $fecha);
        $desde = $anio.'-'.$mes.'-01';
        for($i=0; $i<$dia; $i++) {
            $f = self::addWorkingDays($desde, $i, $feriados);
            if ($f == $fecha)
                return $i+1;
        }
        return false;
    }

    /**
     * Método que obtiene la fecha de un día hábil X en un mes
     * @param anio Año del día hábil que se busca
     * @param mes Mes del día hábil que se busca
     * @param dia_habil Número de día hábil dentro del mes y año que se busca
     * @param feriados Arreglo con los feriados del mes (si no se pasa solo se omitirán fin de semanas)
     * @return Fecha del día hábil
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-06-21
     */
    public static function getWorkingDay($anio, $mes, $dia_habil, $feriados = [])
    {
        $fecha = self::addWorkingDays($anio.'-'.$mes.'-01', 0, $feriados); // obtiene primer día hábil
        $fecha = self::addWorkingDays($fecha, $dia_habil-1, $feriados);
        list($anio2, $mes2, $dia2) = explode('-', $fecha);
        return ($anio2 == $anio and $mes2 == $mes) ? $fecha : false;
    }

    /**
     * Método que indica si una fecha es el último día laboral del mes
     * @param fecha Fecha que se quiere saber si es el último día laboral del mes
     * @param feriados Arreglo con los feriados del mes (si no se pasa solo se omitirán fin de semanas)
     * @return =true si es el último día laboral del mes
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-07-20
     */
    public static function isLastWorkingDay($fecha, $feriados = [])
    {
        if (!self::whatWorkingDay($fecha, $feriados))
            return false;
        $fecha2 = self::addWorkingDays($fecha, 1, $feriados);
        list($anio, $mes, $dia) = explode('-', $fecha);
        list($anio2, $mes2, $dia2) = explode('-', $fecha2);
        return ($anio2 == $anio and $mes2 == $mes) ? false : true;
    }

    /**
     * Método que cuenta cuantos de los días de la variable 'days' existen en el
     * rango desde 'from' hasta 'to'.
     * @param from Desde cuando revisar
     * @param to Hasta cuando revisar
     * @param days Días que se están buscando en el rango
     * @param excludeWeekend =true se omitirán días que sean sábado o domingo
     * @return Cantidad de días que se encontraron en el rango
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-09-07
     */
    public static function countDaysMatch($from, $to, $days, $excludeWeekend = false)
    {
        $count = 0;
        $date = strtotime($from);
        $end = strtotime($to);
        while($date <= $end) {
            $dayOfTheWeek = date('N', $date);
            if ($excludeWeekend && ($dayOfTheWeek==6 || $dayOfTheWeek==7)) {
                $date += 86400;
                continue;
            }
            if (in_array(date('Y-m-d', $date), $days))
                $count++;
            $date += 86400;
        }
        return $count;
    }

    /**
     * Función para mostrar una fecha con hora con un formato "agradable"
     * @param timestamp Fecha en formto (de función date): Y-m-d H:i:s
     * @param hora Si se desea (true) o no (false) mostrar la hora
     * @param letrasFormato Si van en mayúscula ('u'), mínuscula ('l') o normal ('')
     * @param mostrarDia Si se incluye (=true) o no el día
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-04-19
     */
    public static function timestamp2string ($timestamp, $hora = true, $letrasFormato = '', $mostrarDia = true)
    {
        $puntoPos = strpos($timestamp, '.');
        if ($puntoPos) {
            $timestamp = substr($timestamp, 0, $puntoPos);
        }
        $unixtime = strtotime($timestamp);
        if ($mostrarDia)
            $fecha = date('\D\I\A j \d\e \M\E\S \d\e\l Y', $unixtime);
        else
            $fecha = date('j \d\e \M\E\S \d\e\l Y', $unixtime);
        if ($hora) $fecha .= ' a las '.date ('H:i', $unixtime);
        $dia = self::$dias[date('w', $unixtime)];
        $mes = self::$meses[date('n', $unixtime)-1];
        if ($letrasFormato == 'l') {
            $dia = strtolower ($dia);
            $mes = strtolower ($mes);
        } else if ($letrasFormato == 'u') {
            $dia = strtoupper ($dia);
            $mes = strtoupper ($mes);
        }
        return str_replace(array('DIA', 'MES'), array($dia, $mes), $fecha);
    }

    /**
     * Método para transformar un string a una fecha
     * @param fecha String a transformar (20100523 o 201005)
     * @param invertir =true si la fecha a normalizar parte con día o mes
     * @return String trasnformado (2010-05-23 o 2010-05)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-12-18
     */
    public static function normalize($fecha, $invertir = false)
    {
        if ($invertir) {
            if (strlen($fecha)==6) return $fecha[2].$fecha[3].$fecha[4].$fecha[5].'-'.$fecha[0].$fecha[1];
            else if (strlen($fecha)==8) return $fecha[4].$fecha[5].$fecha[6].$fecha[7].'-'.$fecha[2].$fecha[3].'-'.$fecha[0].$fecha[1];
        } else {
            if (strlen($fecha)==6) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5];
            else if (strlen($fecha)==8) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5].'-'.$fecha[6].$fecha[7];
        }
        return $fecha;
    }

    /**
     * Método que calcula los años que han pasado a partir de una fecha
     * @param fecha Desde cuando calcular los años
     * @return Años que han pasado desde la fecha indicada
     * @author http://es.wikibooks.org/wiki/Programaci%C3%B3n_en_PHP/Ejemplos/Calcular_edad
     * @version 2015-03-27
     */
    public static function age($fecha)
    {
        list($Y, $m, $d) = explode('-', $fecha);
        return date('md') < $m.$d ? date('Y')-$Y-1 : date('Y')-$Y;
    }

    /**
     * Método que calcula cuanto tiempo ha pasado desde cierta fecha y hora y lo
     * entrega en un string que representa dicho tiempo
     * @param datetime Fecha y hora en cualquier formato soportado por clase \DateTime
     * @param full Si se debe mostrar todo el string o solo una parte
     * @return String con el tiempo que ha pasado para la fecha
     * @author http://stackoverflow.com/a/18602474
     * @version 2014-08-19
     */
    public static function ago($datetime, $full = false)
    {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = array(
            'y' => 'año',
            'm' => 'mes',
            'w' => 'semana',
            'd' => 'día',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? ($k=='m'?'es':'s') : '');
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) $string = array_slice($string, 0, count($string)>=2 ? 2 : 1);
        return $string ? 'hace '.implode(', ', $string) : 'recién';
    }

    /**
     * Método que calcula cuanto tiempo ha pasado desde cierta fecha y hora y lo
     * entrega como la cantidad de días
     * @param from Fecha desde cuando contar
     * @param to Fecha hasta cuando contar (si es null será la fecha actual)
     * @return Días que han pasado entre las fechas
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-05-06
     */
    public static function count($from, $to = null)
    {
        if (!$to) $now = new \DateTime();
        else $now = new \DateTime($to);
        $ago = new \DateTime($from);
        $diff = $now->diff($ago);
        return $diff->days;
    }

    /**
     * Método que aplica un formato en particular a un timestamp
     * @param datetime Fecha y hora (http://php.net/manual/es/datetime.formats.php)
     * @param format Formato de salida requerido (http://php.net/manual/es/function.date.php)
     * @return Fecha formateada según formato solicitado
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-05-13
     */
    public static function format($datetime, $format = 'd/m/Y')
    {
        if (!$datetime) return null;
        return date($format, strtotime($datetime));
    }

    /**
     * Método que obtiene la fecha a partir de un número serial
     * @param n número serial
     * @return Fecha en formato YYYY-MM-DD
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2015-07-07
     */
    public static function fromSerialNumber($n)
    {
        return date('Y-m-d', ($n - 25568) * 86400);
    }

    /**
     * Método que obtiene un periodo (mes) anterior a uno específicado
     * @param periodo Período para el cual se quiere saber el anterior o =null para actual
     * @return Periodo en formato YYYYMM
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2016-02-03
     */
    public static function previousPeriod($periodo = null)
    {
        if (!$periodo)
            $periodo = date('Ym');
        $periodo_anterior = $periodo - 1;
        if (substr($periodo_anterior, 4)=='00')
            $periodo_anterior = $periodo_anterior - 100 + 12;
        return $periodo_anterior;
    }

}
