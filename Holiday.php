<?php

/**
 * Clase que permite identificar si una fecha específica es un día festivo.
 * de acuerdo con la ley colombiana
 * @author vizapata
 *
 */
class Holiday {
	
	/**
	 * Lista de días festivos
	 * @var array
	 */
	private $list = null;
	
	/**
	 * Instancia única de clase
	 */
	private static $instance = null;
	
	/**
	 * Constructor privado. Se hace énfasis en el uso de la instancia única de clase
	 * según las indicaciones del patrón singleton
	 */
	private function __construct(){
		$this->list = array();
		$this->calculateForYear();
	}
	
	/**
	 * Retorna la instancia única de la clase
	 * @return Holiday instancia única de la clase Festivos
	 */
	public static function getInstance(){
		if(self::$instance==null) self::$instance= new Holiday();
		return self::$instance;
	}
	
	
	/**
	 * Retorna un arreglo de los días festivos del año indicado
	 * @param string $year
	 */
	private function calculateForYear($year = 0){
		if ($year <= 1970 || $fecha>=2037) $year = intval(date ( 'Y' ));
		
		
		// Fixed dates
		$this->list[] = $year."-01-01"; // Año nuevo
		$this->list[] = $year."-05-01"; // Dia del Trabajo 1 de Mayo
		$this->list[] = $year."-07-20"; // Independencia 20 de Julio
		$this->list[] = $year."-08-07"; // Batalla de Boyacá 7 de Agosto
		$this->list[] = $year."-12-08"; // Inmaculada 8 diciembre
		$this->list[] = $year."-12-25"; // Navidad 25 de diciembre

		

		// These dates are moved to the next monday
		$this->list[] = $this->moveToMonday ($year, 01, 06 ); // Reyes Magos Enero 6 (01-06)
		$this->list[] = $this->moveToMonday ($year, 03, 19 ); // Día de san Jose Marzo 19 (03-19)
		$this->list[] = $this->moveToMonday ($year, 06, 29 ); // San Pedro y San Pablo Junio 29 (06-29)
		$this->list[] = $this->moveToMonday ($year, 08, 15 ); // Asunción Agosto 15 (08-15)
		$this->list[] = $this->moveToMonday ($year, 10, 12 ); // Descubrimiento de América Oct 12 (10-12)
		$this->list[] = $this->moveToMonday ($year, 11, 01 ); // Todos los santos Nov 1 (11-01)
		$this->list[] = $this->moveToMonday ($year, 11, 11 ); // Independencia de Cartagena Nov 11 (11,11)

		// Holidays relative to the easterDate
		
		// Fixed
		$this->list[] = $this->calculateFromEasterDate ($year, -03, false ); // jueves santo (3 días antes de pascua)
		$this->list[] = $this->calculateFromEasterDate ($year, -02, false ); // viernes santo (2 días antes de pascua)
		
		// Moved to monday
		$this->list[] = $this->calculateFromEasterDate ($year, 36, true ); // Ascensión del Señor (Sexto domingo después de Pascua) - 36 días
		$this->list[] = $this->calculateFromEasterDate ($year, 60, true ); // Corpus Christi (Octavo domingo después de Pascua) - 60 días
		$this->list[] = $this->calculateFromEasterDate ($year, 68, true ); // Sagrado Corazón de Jesús (Noveno domingo después de Pascua) 68 días
		
		sort($this->list);
	}
	
	/**
	 * funcion que mueve una fecha diferente a lunes al siguiente lunes en el
	 * calendario y se aplica a fechas que estan bajo la ley emiliani
	 * @param int $month
	 * @param int $day
	 */
	private function moveToMonday($year, $month, $day) {
		// Número de días a sumar al día para llegar al siguiente lunes
		$daysToAdd = array(
			0 => 1, // Domingo
			1 => 0, // Lunes
			2 => 6, // Martes
			3 => 5, // Miércoles
			4 => 4, // Jueves
			5 => 3, // Viernes
			6 => 2, // Sábado
		);
		
		// Día de la semana original
		$monday = date ( "w", mktime ( 0, 0, 0, $month, $day, $year ) );
		
		// Lunes siguiente al día original
		$monday += $daysToAdd[$monday];
		
		// Es posible que el mes haya cambiado con la suma de días
		$month = date ( "m", mktime ( 0, 0, 0, $month, $monday, $year ) ) ;
		
		return date ( "d", mktime ( 0, 0, 0, $month, $monday, $year ) ) ;
	}
	
	/**
	 * Calcula una nueva fecha sumando o restando días a la fecha de pascua de un año indicado
	 * @param int $year Año en el que se calcula la fecha de pascua
	 * @param number $numDays Número de días a sumar o restar de la fecha de pascua
	 * @param boolean $toMonday si está en true, entonces se retorna la fecha correspondiente al siguiente lunes (Ley Emiliani)
	 * @return string fecha del día festivo en formato Y-m-d
	 */
	private function calculateFromEasterDate($year, $numDays = 0, $toMonday = false) {
		
		$easterMonth = date ( "m", easter_date ( $year ) );
		$easterDay = date ( "d", easter_date ( $year ) );
		
		$month = date ( "m", mktime ( 0, 0, 0, $easterMonth, $easterDay + $numDays, $year ) );
		$day = date ( "d", mktime ( 0, 0, 0, $easterMonth, $easterDay + $numDays, $year ) );
		
		if ($toMonday)  return $this->moveToMonday ($year, $month, $day );
		else return sprintf("%s-%s-%s",  $year, $month, $day );
	}
	
	public function isHoliday($date ) {
		return in_array($date, $this->list);
	}
	
	public function isWeekend($date ) {
		$dayWeek = date("w", strtotime($date));
		return $dayWeek == 0 || $dayWeek == 6;
	}
	
	
	public function isHolidayOrWeekend($date ) {
		return $this->isHoliday($date) || $this->isWeekend($date);
	}
	
	/**
	 * Retorna el siguiente día hábil disponible 
	 * @return string Fecha del siguiente día hábil en formato Y-m-d
	 */
	public function nextWorkDay(){
		$date = date("Y-m-d");
		$day = intval(date("d"));
		$month = date("m");
		$year = date("Y");
		$dayWeek = date("w");
		
		$esFDS = $dayWeek == 0 || $dayWeek == 6;
		$isFridayPM = $dayWeek == 5;
		if($isFridayPM){
			$hour = intval(date("G"));
			$isFridayPM = $hour > 18;
		}
		
		$noWorkDay = $esFDS || $isFridayPM || $this->isHoliday($date);
		
		while($noWorkDay){
			$date = date("Y-m-d",mktime(0,0,0,$month, ++$day, $year));
			$noWorkDay = $this->isHolidayOrWeekend($date);
		}
		return $date;
	}
	
	/**
	 * Retorna una lista de los próximos días festivos
	 */
	public function nextHolidays(){
		$pos = 0;
		$tsToday = strtotime(date("Y-m-d"));
		$tsfestivo = 0;
		
		do{
			$tsFestivo = strtotime($this->list[$pos]);
			$pos++;
		}while(isset($this->list[$pos]) && $tsToday>$tsFestivo);
		
		if(!isset($this->list[$pos])){
			$this->calculateForYear(intval(date('Y'))+1);
			return $this->nextHolidays();
		
		} else{
			return array_splice($this->list, $pos-1);
		}
	}
	
	
}
