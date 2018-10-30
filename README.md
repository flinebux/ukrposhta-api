# UkrposhtaApi
Класс предоставляет доступ к функциям API Укрпочты

[![Latest Stable Version](https://poser.pugx.org/flinebux/ukrposhta-api/v/stable)](https://packagist.org/packages/flinebux/ukrposhta-api) 
[![Total Downloads](https://poser.pugx.org/flinebux/ukrposhta-api/downloads)](https://packagist.org/packages/flinebux/ukrposhta-api) 
[![License](https://poser.pugx.org/flinebux/ukrposhta-api/license)](https://packagist.org/packages/flinebux/ukrposhta-api)

# Подготовка
Для использования API необходимо: bearer ключ и token ключ.
После получения ключей API предоставляется возможность использовать все методы класса

## Установка последней версии класса для работы с API
### Git
Необходимо выполнить в командной строке
```git
git clone https://github.com/flinebux/ukrposhta-api
```
### Composer
Необходимо создать файл ``composer.json`` со следующим содержанием  
```json
{
    "require": {
        "flinebux/ukrposhta-api": "dev-feature"
    }
}
```
и запустить из командной строки команду ``php composer.phar install`` или ``php composer.phar update``
Или выполнить в командной строке 
```
composer require flinebux/ukrposhta-api:dev-feature
```

# Форматы данных
Для входящих данных используются PHP массивы, ответ сервера может быть получен в формате:
* как PHP массив
* JSON

# Использование 
## Создание экземпляра класса
```php
$ukrposhtaApi = new UkrposhtaApiNew('my_bearer','my_token');
```

## Получение последнего статуса о трек-номере
```php
$result = $ukrposhtaApi->modelStatuses('204001234567');
```

## Создание адреса клиента
```php
$address = $ukrposhtaApi->modelAddressPost(array(
"postcode"=>"07401",
"country"=> "UA",
"region"=>"Київська",
"city"=>"Бровари",
"district"=>"Київський",
"street"=>"Котляревського",
"houseNumber"=>"12",
"apartmentNumber"=>"33"
));
```
## Создание отправления
```php
 $address = $ukrposhtaApi->modelShipmentsPost(array(
   "sender" => array(
     "uuid" => "{SenderUuid}"
   ),
   "recipient" => array(
     "uuid"=> "{RecipientUuid}"
   ),
   "deliveryType"=> "W2D",
   "paidByRecipient"=> true,
   "nonCashPayment"=> false,
   "parcels"=> array(
     "weight"=> 1200,
     "length"=> 170
   )
  ));
```

## Создание собственных методов
На примере метода modelAddressPost создадим собственный метод
```php
 use flinebux\Shipping\UkrposhtaApi;
 
 class UkrposhtaApiCustom extends UkrposhtaApi
 {
     public function customMethod($data)
     {
         return $this->request(
             'addresses',
             self::METHOD_POST,
             $data
         );
     }
 }
```
Для кастомизации были определены 4 метода HTTP запросов:
```php
self::METHOD_GET;
self::METHOD_POST;
self::METHOD_PUT;
self::METHOD_DELETE;
```
А также 2 основных метода, для запросов с токеном и без:
```php
$this->request($model, $method = self::METHOD_GET, $params = null, $add = '');
$this->requestToken($model, $method = self::METHOD_GET, $params = null, $add = '', $file = false);
```