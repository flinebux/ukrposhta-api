# UkrposhtaApi
Класс предоставляет доступ к функциям API Укрпочты

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
        "flinebux/ukrposhta-api": "dev-master"
    }
}
```
и запустить из командной строки команду ``php composer.phar install`` или ``php composer.phar update``
Или выполнить в командной строке 
```
composer require flinebux/ukrposhta-api:dev-master
```

# Форматы данных
Для входящих данных используются PHP массивы, ответ сервера может быть получен в формате:
* как PHP массив
* JSON

# Использование 
## Создание экземпляра класса
```php
$ukrposhtaApi = new UkrposhtaApi('my_bearer','my_token');
```

## Получение последнего статуса о трек-номере
```php
$result = $ukrposhtaApi->modelStatuses('204001234567');
```

## Создание адреса клиента
```php
$address = $ukrposhtaApi->modelAdressPost(array(
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