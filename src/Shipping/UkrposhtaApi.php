<?php

namespace flinebux\Shipping;

/**
 * Ukrposhta API Class
 *
 * @author flinebux
 * @see https://github.com/flinebux
 * @license MIT
 */
class UkrposhtaApi
{
    /**
     * @var string $bearer Bearer key for UkrposhtaApi
     */
    protected $bearer;
    /**
     * @var string $token Token for UkrposhtaApi. There are request without token.
     */
    protected $token;
    /**
     * @var bool $throwErrors Throw exceptions when in response is error
     */
    protected $throwErrors = false;
    /**
     * @var string $format Format of returned data - array
     */
    protected $format = 'array';
    /**
     * @var string $url Link to ukrposhtaApi
     */
    protected $url = 'https://www.ukrposhta.ua/';
    /**
     * @var string $apiVersion version for url
     */
    protected $apiVersion = '/0.0.1/';
    /**
     * @var string $responseTime waiting for response from server, sec.
     */
    protected $responseTime = '30';

    const METHOD_GET = 'HTTPGET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    /**Default constructor
     * UkrposhtaApi constructor.
     * @param $bearer
     * @param bool $token
     * @param bool $throwErrors
     */
    public function __construct($bearer, $token = false, $throwErrors = false)
    {
        $this->throwErrors = $throwErrors;
        return $this
            ->setBearer($bearer)
            ->setToken($token);
    }

    /**Setter for bearer property
     * @param $bearer
     * @return $this
     */
    public function setBearer($bearer)
    {
        $this->bearer = $bearer;
        return $this;
    }

    /**Getter for bearer property
     * @return string
     */
    public function getBearer()
    {
        return $this->bearer;
    }

    /**Setter for token property
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**Getter for token property
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**Setter for format property
     * @param $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**Getter for format property
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**Setter for property responseTime
     * @param $responseTime
     * @return $this
     */
    public function setResponseTime($responseTime)
    {
        if (is_numeric($responseTime)) {
            $this->responseTime = $responseTime;
        }
        return $this;
    }

    /**Getter for property responseTime
     * @return string
     */
    public function getResponseTime()
    {
        return $this->responseTime;
    }

    /**Prepare data before return
     * @param $data
     * @return array|mixed
     */
    protected function prepare($data)
    {
        //Returns array
        if ($this->format == 'array') {
            $result = is_array($data) ? $data : json_decode($data, 1);
            return $result;
        }
        // Returns json or raw data
        return $data;
    }

    /**Default curl options
     * @return array
     */
    protected function curlDefaultOptions()
    {
        return [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $this->bearer],
            CURLOPT_HEADER => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => $this->responseTime
        ];
    }

    /**Request function for model Address
     * @param $model
     * @param string $method
     * @param null $params
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    protected function request($model, $method = self::METHOD_GET, $params = null, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add;

        /* Convert data to necessary format*/
        $post = json_encode($params);

        $options = $this->curlDefaultOptions();
        $options[constant(CURLOPT_ . $method)] = 1;

        $ch = curl_init($url);
        if ($method != self::METHOD_GET) {
            $options[CURLOPT_POSTFIELDS] = $post;
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return $this->prepare($result);
    }

    /**Request for model client, smartBox, print with token
     * @param $model
     * @param string $method
     * @param null $params
     * @param string $add
     * @param bool $file
     * @return array|mixed
     * @throws \Exception
     */
    protected function requestToken($model, $method = self::METHOD_GET, $params = null, $add = '', $file = false)
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add . '?token=' . $this->token;

        /* Convert data to necessary format*/
        $post = json_encode($params);

        $options = $this->curlDefaultOptions();
        $options[constant(CURLOPT_ . $method)] = 1;

        $ch = curl_init($url);
        if ($method != self::METHOD_GET) {
            $options[CURLOPT_POSTFIELDS] = $post;
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        if ($file) {
            return $result;
        } else {
            return $this->prepare($result);
        }
    }

    /**Similar function to requestToken, but only for PUT request
     * @param $model
     * @param null $params
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    protected function requestTokenPut($model, $params = null, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'ecom' . $this->apiVersion . $model . $add . '?token=' . $this->token;

        /* Convert data to necessary format*/
        $post = json_encode($params);

        $options = $this->curlDefaultOptions();
        $options[CURLOPT_CUSTOMREQUEST] = self::METHOD_PUT;
        $options[CURLOPT_POSTFIELDS] = $post;

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return $this->prepare($result);
    }

    /**Request token for tracking barcode
     * @param $model
     * @param string $add
     * @return array|mixed
     * @throws \Exception
     */
    protected function requestTracking($model, $add = '')
    {
        /* Get required URL*/
        $url = $this->url . 'status-tracking' . $this->apiVersion . $model . $add;

        $options = $this->curlDefaultOptions();
        $options[CURLOPT_HTTPGET] = 1;

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (curl_errno($ch) && $this->throwErrors) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return $this->prepare($result);
    }

    /**Get created address by id
     * @param $id int
     * @return array|mixed
     */
    public function modelAdressGet($id)
    {
        return $this->request(
            'addresses',
            self::METHOD_GET,
            null,
            '/' . $id
        );
    }

    /**Create address. For example:
     * @param $data array
     * @return array|mixed
     */
    public function modelAdressPost($data)
    {
        return $this->request(
            'addresses',
            self::METHOD_POST,
            $data
        );
    }

    /**Creating new client
     * @param $data array
     * @return array|mixed
     */
    public function modelClientsPost($data)
    {
        return $this->requestToken(
            'clients',
            self::METHOD_POST,
            $data
        );
    }

    /**Change data to existing client
     * @param $id int
     * @param $data array
     * @return array|mixed
     */
    public function modelClientsPut($id, $data)
    {
        return $this->requestToken(
            'clients',
            self::METHOD_PUT,
            $data,
            '/' . $id
        );
    }

    /**Get created clients by external-id
     * @param $id int
     * @return array|mixed
     */
    public function modelClientsGet($id)
    {
        return $this->requestToken(
            'clients',
            self::METHOD_GET,
            null,
            '/external-id/' . $id
        );
    }

    /**Creating shipment
     * @param $data array
     * @return array|mixed
     */
    public function modelShipmentsPost($data)
    {
        return $this->requestToken(
            'shipments',
            self::METHOD_POST,
            $data
        );
    }

    /**Get file for print
     * @param $id string
     * @return array|mixed
     */
    public function modelPrint($id)
    {
        return $this->requestToken(
            'shipments',
            self::METHOD_GET,
            null,
            '/' . $id . '/label',
            true
        );
    }

    /**Request for use smartBox
     * @param $smartBoxCode string
     * @param $clientUuid string
     * @return array|mixed
     */
    public function modelSmartBoxPost($smartBoxCode, $clientUuid)
    {
        return $this->requestToken(
            'smart-boxes',
            self::METHOD_POST,
            null,
            '/' . $smartBoxCode . '/use-with-sender/' . $clientUuid
        );
    }

    /**Initialization smartBox shipment
     * @param $smartBoxCode string
     * @return array|mixed
     */
    public function modelSmartBoxGet($smartBoxCode)
    {
        return $this->requestToken(
            'smart-boxes',
            self::METHOD_GET,
            null,
            '/' . $smartBoxCode . '/shipments/next'
        );
    }

    /**Creating smartBox shipment
     * @param $smartBoxShipmentUuid string
     * @param $data array
     * @return array|mixed
     */
    public function modelSmartBoxPut($smartBoxShipmentUuid, $data)
    {
        return $this->requestTokenPut(
            'shipments',
            $data,
            '/' . $smartBoxShipmentUuid
        );
    }

    /**Getting last status of barcode
     * @param $barcode string
     * @return array|mixed
     */
    public function modelStatuses($barcode)
    {
        return $this->requestTracking(
            'statuses/last',
            '?barcode=' . $barcode
        );
    }
}
