<?php 
namespace LeviZwannah\PesapalSdk;

use Exception;
use GuzzleHttp\Client;
use LeviZwannah\PesapalSdk\Traits\AssertTrait;
use LeviZwannah\PesapalSdk\Traits\PropertyTrait;

class Pesapal{
    use PropertyTrait;
    use AssertTrait;

    /**
     * The base url
     * @var string
     */
    public $baseUrl = "https://pay.pesapal.com/v3";

    /**
     * The environment
     * @var string
     */
    public $env = "live";

    /**
     * Consumer Key Defaults to Kenyan Merchant sandbox
     * Consumer key
     * @var string
     */
    public $key = "qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW";

    /**
     * Consumer Secret - Defaults to Kenyan Merchant Sandbox
     * Consumer secret
     * @var string
     */
    public $secret = "osGQ364R49cXKeOYSpaOnT++rHs=";

    /**
     * The current response for the recent request.
     * @var object|null
     */
    public $response = null;

    public function __construct(array $config)
    {
        $this->configure($config);
        
        if($this->env != "live"){
            $this->baseUrl = "https://cybqa.pesapal.com/pesapalv3";
        }
    }

    /**
     * Creates a new Pesapal object
     * @param array $config The config array should have the keys that
     * exists in the pesapal object.
     * 
     * @return Pesapal
     */
    public static function new(array $config){
        return new static($config);
    }

    /**
     * Maps the config to the object
     * @param array $config
     * 
     */
    public function configure(array $config){
        foreach($config as $key => $val){
            $this->$key = $val;
        }

        return $this;
    }

    /**
     * Returns the error object
     * @return object
     */
    public function error(){
        return $this->response()->error;
    }

    /**
     * Checks if the request returned a successful result
     * @return bool
     */
    public function accepted(){
        $this->assertProperty("response");

        return empty($this->response()->error) 
                && $this->response()->status == 200;
    }

    /**
     * Gets the HTTP object
     * @return Client
     */
    public function http(){
        return new Client([
            'base_url' => $this->baseUrl,
            'accept' => 'application/json',
            'content-type' => 'application/json'
        ]);
    }

    /**
     * Gets the access token
     * @return string
     */
    public function token(){

        $this->request([
            'consumer_key' => $this->key,
            'consumer_secret' => $this->secret
        ], 
        "/api/Auth/RequestToken", false);

        return $this->response()->token;
    }

    /**
     * Registers the IPN URL for your application and returns the IPN ID
     * @param string $ipnUrl
     * @param string $httpMethod
     * 
     * @return string IPN ID
     */
    public function registerIpn($ipnUrl, $httpMethod = "POST"){
        $this->request([
            "id" => $ipnUrl,
            "ipn_notification_type" => $httpMethod
        ], "/api/URLSetup/RegisterIPN");

        return $this->response()->ipn_id;
    }

    /**
     * Makes a request and ensure it is accepted before returning
     * the response.
     * @param array $data
     * @param string $endpoint
     * @param bool $authenticated adds token to the request
     * @param string $method http method (lowercase)
     * 
     * @return object response
     */
    public function request($data, $endpoint, $authenticated = true, $method = "post"){
        $http = $this->http();

        if(!$authenticated){
            $response = $http->$method($endpoint, [
                'json' => $data
            ]);
        }
        else{
            $response = $http->$method($endpoint, [
                'json' => $data,
                'auth' => ['Bearer', $this->token()]
            ]);
        }

        $this->response((object) $response->getBody());
        $this->assertAccepted();

        return $this->response();
    }
}

?>