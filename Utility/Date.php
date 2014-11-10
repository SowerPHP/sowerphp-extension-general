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
 * Clase para trabajar con fechas
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-10-01
 */
class Utility_Date
{

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
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-02-19
     */
    public static function timestamp2string ($timestamp, $hora = true, $letrasFormato = '')
    {
        $puntoPos = strpos($timestamp, '.');
        if ($puntoPos) {
            $timestamp = substr($timestamp, 0, $puntoPos);
        }
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $unixtime = strtotime($timestamp);
        $fecha = date('\D\I\A j \d\e \M\E\S \d\e\l Y', $unixtime);
        if ($hora) $fecha .= ', a las '.date ('H:i', $unixtime);
        $dia = $dias[date('w', $unixtime)];
        $mes = $meses[date('n', $unixtime)-1];
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
     * @return String trasnformado (2010-05-23 o 2010-05)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-10-04
     */
    public static function normalize($fecha)
    {
        if (strlen($fecha)==6) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5];
        else if (strlen($fecha)==8) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5].'-'.$fecha[6].$fecha[7];
        return $fecha;
    }

}
