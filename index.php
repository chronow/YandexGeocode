<?

/**
 * Yandex Geocode. Получение координат ближайшего метро от нашей точки и вычисление  расстояния между ними
 */
class YandexGeocode
{	private $apiKey;	// API-ключ Yandex Developer
	public $latitude;	// Широта нашей точки
	public $longitude;	// Долгота нашей точки
	public $latitude2;	// Широта 2й точки 
	public $longitude2;	// Долгота 2й точки
	
	function __construct(){
		$this->apiKey = 'ваш API-ключ';
	}

	/**
	 * Получение информации о ближайшем расположении(ях) метро от нашей точки
	 */
	function getInfo( $param = [
		'latitude'	=> 59.939095,
		'longitude'	=> 30.315868,
		'kind'			=> 'metro',
	]){
		$this->latitude = $param['latitude'];
		$this->longitude = $param['longitude'];

		$url = "https://geocode-maps.yandex.ru/1.x/";
		$url .="?apikey=".$this->apiKey;
		$url .="&geocode=".$param['latitude'].",".$param['longitude'];	// Координаты, либо Адрес
		$url .="&sco=latlong";																					// longlat (долгота, широта), latlong (широта, долгота)
		$url .="&kind=".$param['kind'];																	// house, street, district, locality .. metro, railway, airport
		$url .="&rspn=1";																								// 1 (ограничивать поиск), 0 - (не ограничивать поиск)
		$url .="&format=json";																					// Формат ответа xml, json
		$url .="&results=1";																						// кол-во выводимых результатов

		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch); 
		curl_close($ch);

		return json_decode($result);
	}

	/**
	 * Получение первой (ближайшей) координаты из ответа
	 * return: 30.315078 59.935985
	 */
	function getCoord( $json ){
		$Point = $json->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
		
		$coord = explode(" ", $Point);
		$this->latitude2	= $coord[1];
		$this->longitude2 = $coord[0];

		return $coord;
	}

	/**
	 * Формула вычисления расстояния между двух точек на Земле.
	 */
	function getDistance($latitude1=0, $longitude1=0, $latitude2=0, $longitude2=0){
		if($latitude1==0 || $longitude1==0 || $latitude2 == 0 || $longitude2==0){
			$latitude1	= $this->latitude;
			$longitude1	= $this->longitude;
			$latitude2	= $this->latitude2;
			$longitude2	= $this->longitude2;
		}
		
		if($latitude1==0 || $longitude1==0 || $latitude2 == 0 || $longitude2==0) return false;

		$earthRadius = 6371302; // средний радиус Земли в м
			  
		$dLat = deg2rad($latitude2 - $latitude1);  
		$dLon = deg2rad($longitude2 - $longitude1);  
			  
		$a = sin($dLat/2) * sin($dLat/2) + cos( deg2rad($latitude1) ) * cos( deg2rad($latitude2) ) * sin($dLon/2) * sin($dLon/2);  
		$c = 2 * asin(sqrt($a));  
		$d = $earthRadius * $c;  
			  
		return ceil( $d );  
	}
}


/** Пример вызова **/

$YandexGeocode = new YandexGeocode();

$json = $YandexGeocode->getInfo([
	'latitude'	=> 59.939095,
	'longitude'	=> 30.315868,
	'kind'			=> 'metro',
]);

$coordinates = $YandexGeocode->getCoord( $json );
$distance = $YandexGeocode->getDistance();

echo $distance.' м';

