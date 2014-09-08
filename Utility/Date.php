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
 * @version 2014-09-07
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
            $fds = 0;
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

}
