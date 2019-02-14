## Получение координат ближайшего метро (аэропорта, жд) от нашей точки, вычисление  расстояния между ними.

***

```php
$YandexGeocode = new YandexGeocode();

//Получение информации о ближайшем расположении(ях) метро от нашей точки
$json = $YandexGeocode->getInfo([
	'latitude'	=> 59.939095,
	'longitude'	=> 30.315868,
	'kind'			=> 'metro',
]);

//Получение первой (ближайшей) координаты из ответа
$coordinates = $YandexGeocode->getCoord( $json );

//Вычисление расстояния между двух точек
$distance = $YandexGeocode->getDistance();

echo $distance.' м';
```
***
