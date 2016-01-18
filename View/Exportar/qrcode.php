<?php
$barcodeobj = new TCPDF2DBarcode($string, 'QRCode');
$barcodeobj->getBarcodePNG();
exit (0);
