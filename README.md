# Festivos Colombia
Clase en PHP que permite calcular los días festivos en Colombia

## Uso
Para utilizar la clase, debe incluir el archivo PHP y obtener la instancia de la clase para hacer el cálculo de la siguiente manera:

``` PHP
<?php
// Include class
require_once 'festivos.php';

// get the class instance (singleton pattern)
$holidayCalculator = Holiday::getInstance();

// This method returns the next work day. (Saturday and Sunday are holidays)
$workDay = $holidayCalculator->nextWorkDay();
echo $workDay;
```
