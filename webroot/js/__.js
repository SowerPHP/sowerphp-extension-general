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

/**
 * @file __.js
 * Biblioteca con métodos genéricos (bajo la clase __) para propósitos
 * genéricos
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-09
 */

/*jslint browser: true, devel: true, nomen: true, indent: 4 */

/**
 * Constructor de la clase
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-09
 */
function __() {
    'use strict';
    return;
}

/**
 * Método que determina si un objeto es/está vacío o no
 * @param obj Objeto que se está revisando
 * @return =true si es vacio
 */
__.empty = function (obj) {
    'use strict';
    if (obj === undefined || obj === null || obj === "") { return true; }
    if (typeof obj === "number" && isNaN(obj)) { return true; }
    if (obj instanceof Date && isNaN(Number(obj))) { return true; }
    return false;
};

/**
 * Método que determina si un string es la representación entera de un número
 * @param value Valor que se desea verificar si es una representación entera
 * @return =true valor pasado es un enero
 */
__.isInt = function (value) {
    'use strict';
    if ((parseFloat(value) === parseInt(value, 10)) && !isNaN(value)) {
        return true;
    }
    return false;
};

/**
 * Método que formatea un número usando separador de miles
 * @param n Número a formatear (ej: 1234)
 * @return Número formateado (ej: 1.234)
 * @author http://ur1.ca/h1cvs
 */
__.num = function (n) {
    'use strict';
    var number = n.toString(), result = "", isNegative = false;
    if (number.indexOf("-") > -1) {
        number = number.substr(1);
        isNegative = true;
    }
    while (number.length > 3) {
        result = "." + number.substr(number.length - 3) + result;
        number = number.substring(0, number.length - 3);
    }
    result = number + result;
    if (isNegative) { result = "-" + result; }
    return result;
};

/**
 * Obtiene el dígito verificador a partir del rut sin este
 * @param numero Rut sin puntos ni digito verificador
 * @return char dígito verificador del rut ingresado
 * @author http://ur1.ca/h1d8v
 * @version 2011-04-21
 */
__.rutDV = function (numero) {
    'use strict';
    var nuevo_numero, i, j, suma, n_dv;
    nuevo_numero = numero.toString().split("").reverse().join("");
    for (i = 0, j = 2, suma = 0; i < nuevo_numero.length; i += 1, j = j === 7 ? 2 : j + 1) {
        suma += (parseInt(nuevo_numero.charAt(i), 10) * j);
    }
    n_dv = 11 - (suma % 11);
    return ((n_dv === 11) ? 0 : ((n_dv === 10) ? "K" : n_dv));
};

/**
 * Método que abre un popup
 * @param url Dirección web que se debe abrir en el popup
 * @param w Ancho de la ventana que se abrirá
 * @param h Alto de la ventana que se abrirá
 * @param s Si se muestran ("yes") o no ("no") los scrollbars
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-09
 */
__.popup = function (url, w, h, s) {
    'use strict';
    s = s || "no";
    window.open(
        url,
        window,
        "width=" + w + ",height=" + h + ",directories=no,location=no,menubar=no,scrollbars=" + s + ",status=no,toolbar=no,resizable=no"
    );
}

/**
 * Implementación sincrónica de $.getJSON, esto para poder recuperar el objeto
 * JSON fuera de la funcion que se ejecuta en success
 * @param url Url desde donde se obtendrá el JSON
 * @param data Datos que se deben enviar por la URL (como GET)
 * @return json Datos retornados por la página
 * @todo Migrar método para que use Javascript puro (sin JQuery)
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-05-10
 */
__.getJSON = function(url, data) {
    'use strict';
    var json;
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function (result) {json = result;},
        data: data,
        async: false
    });
    return json;
}
