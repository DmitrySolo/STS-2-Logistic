<?php



Class Logistic {

    private static $instance = null;

    static $CdekPVZFilename = 'cdekRequest.txt';
    static $punktyVidachyPath = "/var/www/west/data/www/santehsmart.ru/punkty-vydachi/";
    static $cityName;
    static $pvzCount;
    static $cdekChunk;
    public $partnersPVZArr;
    static $cdekCityKey;
    protected $PARTNER = array(
      'city'=> array(
          'Воронеж' => array(
              'Офис Интернет-Магазина' => array(
                  'pickups' => array(
                      1 => array(
                          "phone" => "+7473 3036 05",
                          "adress" => "Донбасская 21",
                          "images" => array(),
                          "title" => "На Ярмарке на Донбасской",
                          "delivery" => array(
                              "pickUp" => array(
                                  "price" => "Бесплатно",
                                  "else" => 0,
                                  "rule" => 0,
                                  "min" => 0,
                                  "max" => 1,
                              ),
                              "curier" => array(
                                  "price" => 300,
                                  "else" => "Бесплатно",
                                  "rule" => 4000,
                                  "min" => 1,
                                  "max" => 3,
                              )
                          ),
                          "workingHours" => "Пн-Пт: 9:00-18:30 <br\>Сб-Вс: 9:00-17:30",
                          "Location" => array(
                              "x" => "2343253455345",
                              "y" => "9-9-9990-0-00-"
                          )
                      )
                  )
              )
          ),
          'Белгород' => array(
              'Филиал компании "Окно в Европу" Белгород' => array(
                  'pickups' => array(
                      1 => array(
                          "phone" => "+7 4722 21-78-62",
                          "adress" => "ул.Серафимовича, 66А",
                          "images" => array(),
                          "title" => "На Серафимовича",
                          "delivery" => array(
                              "pickUp" => array(
                                  "price" => "Бесплатно",
                                  "else" => 0,
                                  "rule" => 0,
                                  "min" => 0,
                                  "max" => 1,
                              ),
                              "curier" => array(
                                  "price" => 300,
                                  "else" => "Бесплатно",
                                  "rule" => 4000,
                                  "min" => 1,
                                  "max" => 3,
                              )
                          ),
                          "workingHours" => "Пн-Пт: 9:00-18:30 <br\>Сб-Вс: 9:00-17:30",
                          "Location" => array(
                              "x" => "2343253455345",
                              "y" => "9-9-9990-0-00-"
                          )
                      )
                  )
              )
          ),
          'Старый Оскол' => array(

          )
      )
    );




    public static function getInstance($cityName)
    {
        self::$cityName = $cityName;
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone() {}

    private function __construct() {
        $cityName = self::$cityName;


        if( array_key_exists( $cityName, $this->PARTNER['city'] ) ){
            $this->partnersPVZArr = $this->PARTNER['city'][$cityName];

        }else{
            $this->partnersPVZArr = [];
        }

        if(file_exists(self::$punktyVidachyPath.self::$CdekPVZFilename)){

            $sts_query = unserialize( file_get_contents(self::$punktyVidachyPath.self::$CdekPVZFilename));
        }else{

            $sts_query  =  ISDEKservice::getPVZ_sts();
            file_put_contents(self::$punktyVidachyPath.self::$CdekPVZFilename,serialize($sts_query));
        }

        self::$cdekCityKey = $sts_city_key = array_flip($sts_query['pvz']['CITY'])[$cityName];
        $sts_pvz_array = $sts_query['pvz']['PVZ'][$sts_city_key];
        self::$cdekChunk = array_slice($sts_pvz_array, 0, 3);
        self::$pvzCount = count($sts_pvz_array) + count( $this->partnersPVZArr);
    }

    public function test()
    {
        var_dump($this);
    }
   static function getMass($mass){
        $mass = $mass/1000; // in kg
        $sizeCub = $mass * 5000; // koef
        $sizesValue =  round(pow($sizeCub,1/3));
        return $sizesValue;

    }
    static function calcAll($mass){

        $side = self::getMass( $mass );


        $data = array(
            'shipment' => array(
                'cityFromId' => 506,
                'cityToId' => self::$cdekCityKey,
                'goods' => array(
                    array('height'=>$side,'length'=>$side,'width'=>$side,'weight'=> 1)
                ),
                'timestamp' => time(),
                'type' => 'pickup'
            ),
        );

        $time1 = microtime(true);
        $answer = ISDEKservice:: calc_sts($data);
        echo microtime(true) - $time1;

        $arResult['value'] = $answer['result']['price'];

        $arResult['min'] = $answer['result']['deliveryPeriodMin'];

        $arResult['max'] = $answer['result']['deliveryPeriodMax'];

        $arResult['tarif'] = $answer['result']['tariffId'];

        $arResult['chunk'] = self::$cdekChunk;
        foreach ( $_SESSION['LOGISTIC']['CDEK_POINTS'] as $value ){

            array_push($arResult['points'],$value['Address']);
            echo $value['Address'];
        };


      //  print_r($arResult);
        //print_r($_SESSION['LOGISTIC']['CDEK_POINTS']);

        return $arResult;

    }
}

