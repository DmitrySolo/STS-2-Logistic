<?php


Class Logistic
{

    private static $instance = null;

    static $CdekPVZFilename = 'cdekRequest.txt';
    static $punktyVidachyPath = "/var/www/west/data/www/santehsmart.ru/punkty-vydachi/";
    static $cityName;
    static $pvzCount;
    static $cdekChunk;
    public $partnersPVZArr;
    static $cdekCityKey;
    static $PARTNER = array(
        'city' => array(
            'Воронеж' => array(
                'Наш Офис' => array(
                    "ID" => '',
                    'pickups' => array(
                        1 => array(
                            "phone" => "+7473 3036 05",
                            "adress" => "Донбасская 21",
                            "images" => array(),
                            "title" => "На Ярмарке на Донбасской",
                            "delivery" => array(
                                "pickUp" => array(
                                    "price" => 'Бесплатно',
                                    "else" => false,
                                    "rule" => 0,
                                    "min" => 0,
                                    "max" => 1,
                                ),
                                "curier" => array(
                                    "price" => 300,// or false
                                    "else" => "Бесплатно",//or false
                                    "rule" => 4000,
                                    "min" => 1,
                                    "max" => 3,
                                )
                            ),
                            "workingHours" => "Пн-Пт: 9:00-18:30 <br>Сб-Вс: 9:00-17:30",
                            "Location" => array(
                                "x" => "39.177959",
                                "y" => "51.670067"
                            )
                        )
                    )
                )
            ), 'Белгород' => array(
                "Филиал компании Окно в Европу Белгород" => array(
                    "ID" => 2393,
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
                                    "min" => 2,
                                    "max" => 3,
                                ),
                                "curier" => array(
                                    "price" => 'NOT',
                                    "else" => "Бесплатно",
                                    "rule" => 4000,
                                    "min" => 1,
                                    "max" => 3,
                                )
                            ),
                            "workingHours" => "Пн-Пт: 9:00-18:30 <br\>Сб-Вс: 9:00-17:30",
                            "Location" => array(
                                "x" => "36.623254",
                                "y" => "50.585533",
                            )
                        )
                    )
                )
            ),
            'Старый Оскол' => array(
                'Филиал компании "Окно в Европу" Белгород' => array(
                    "ID" => 52577,
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
                            "workingHours" => "Пн-Пт: 9:00-18:30 <br>Сб-Вс: 9:00-17:30",
                            "Location" => array(
                                "x" => "2343253455345",
                                "y" => "9-9-9990-0-00-"
                            )
                        )
                    )
                )
            ),
            'Липецк' => array(
                "ИП Атаманенко Сергей Петрович" => array(
                    "ID" => 91653,
                    "email" => 'solo-webworks@mail.ru',
                    'pickups' => array(
                        1 => array(
                            "phone" => "+7 (4742) 46-79-47",
                            "adress" => "г.Липецк пр.Победы 108а",
                            "images" => array(),
                            "title" => "На Проспекте Победы",
                            "delivery" => array(
                                "pickUp" => array(
                                    "price" => "Бесплатно",
                                    "else" => 0,
                                    "rule" => 0,
                                    "min" => 2,
                                    "max" => 3,
                                ),
                                "curier" => array(
                                    "price" => '700',
                                    "else" => false,
                                    "rule" => false,
                                    "min" => 3,
                                    "max" => 4,
                                )
                            ),
                            "workingHours" => "Пн-Пт: 9:00-18:00 <br>Сб: 9:00-16:00 <br>Вс: выходной",
                            "Location" => array(
                                "x" => "39.548321",
                                "y" => "52.586891",
                            )
                        )
                    )
                )
            ),

        )
    );


    public static function getInstance($cityName)
    {
        self::$cityName = $cityName;
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __construct()
    {
        $cityName = self::$cityName;


        if (array_key_exists($cityName, self::$PARTNER['city'])) {
            $this->partnersPVZArr = self::$PARTNER['city'][$cityName];

        } else {
            $this->partnersPVZArr = [];
        }

        if (file_exists(self::$punktyVidachyPath . self::$CdekPVZFilename)) {

            $sts_query = unserialize(file_get_contents(self::$punktyVidachyPath . self::$CdekPVZFilename));
        } else {

            $sts_query = ISDEKservice::getPVZ_sts();
            file_put_contents(self::$punktyVidachyPath . self::$CdekPVZFilename, serialize($sts_query));
        }

        self::$cdekCityKey = $sts_city_key = array_flip($sts_query['pvz']['CITY'])[$cityName];
        $sts_pvz_array = $sts_query['pvz']['PVZ'][$sts_city_key];
        self::$cdekChunk = array_slice($sts_pvz_array, 0, 3);
        self::$pvzCount = count($sts_pvz_array) + count($this->partnersPVZArr);
    }

    public function test()
    {
        var_dump($this);
    }

    static function getMass($mass)
    {
        $mass = $mass / 1000; // in kg
        $sizeCub = $mass * 5000; // koef
        $sizesValue = round(pow($sizeCub, 1 / 3));
        return $sizesValue;

    }

    static function calcAll($mass)
    {

        $side = self::getMass($mass);


        $data = array(
            'shipment' => array(
                'cityFromId' => 506,
                'cityToId' => self::$cdekCityKey,
                'goods' => array(
                    array('height' => $side, 'length' => $side, 'width' => $side, 'weight' => 1)
                ),
                'timestamp' => time(),
                'type' => 'pickup'
            ),
        );

        $time1 = microtime(true);
        $answer = ISDEKservice:: calc_sts($data);
        //echo microtime(true) - $time1;

        $arResult['value'] = $answer['result']['price'];

        $arResult['min'] = $answer['result']['deliveryPeriodMin'];

        $arResult['max'] = $answer['result']['deliveryPeriodMax'];

        $arResult['tarif'] = $answer['result']['tariffId'];

        $arResult['chunk'] = self::$cdekChunk;
        foreach ($_SESSION['LOGISTIC']['CDEK_POINTS'] as $value) {

            array_push($arResult['points'], $value['Address']);
            echo $value['Address'];
        };


        //  print_r($arResult);
        //print_r($_SESSION['LOGISTIC']['CDEK_POINTS']);

        return $arResult;

    }

    static function getEmailByPartnerId($partnerId){

        foreach (self::$PARTNER['city'] as $city){
            foreach ($city as $partner){
              if ($partner['ID'] == $partnerId) return $partner['email'];
            }
        }
    }
    function setPartnerPvzHtml($setPvzInfo =  false)
    {

        if ($this->partnersPVZArr) {
            foreach ($this->partnersPVZArr as $pointTitle => $pointInfo):?>

                <div class="partnerPVZ">
                <input  type="hidden" name="ORDER_PROP_76" value="<?= $pointInfo['ID'] ?>">
                <input type="hidden" name="pvzTitle" value="<?= $pointTitle ?>">
                <? foreach ($pointInfo['pickups'] as $pickup): ?>
                    <input type="hidden" name="pvzPhone" value="<?= $pickup['phone'] ?>">
                    <input type="hidden" name="pvzAdress" value="<?= $pickup['adress'] ?>">
                    <input id="partnerPvzTitle" type="hidden" name="ORDER_PROP_84" value="<?= $pickup['adress'] ?>">

                    <? foreach ($pickup['img'] as $img): ?>
                        //IMAGES
                    <?endforeach ?>
                    <input type="hidden" name="pvzTitle" value="<?= $pickup['title'] ?>">
                    <input type="hidden" name="pvzTime" value="<?= $pickup['workinghours'] ?>">
                    <input type="hidden" name="pvzLocationX" value="<?= $pickup['Location']['x'] ?>">
                    <input type="hidden" name="pvzLocationY" value="<?= $pickup['Location']['y'] ?>">
		            <?
		            if ($setPvzInfo):?>
                        <input type="hidden" name="ORDER_PROP_85" value='<?= $pickup['title'].'|'.$pickup['adress']."<br>"
                                                                             ."Номер телефона ПВЗ: ".$pickup['phone']."<br>"
                                                                             ."Срок доставки: ".$pickup['delivery']['pickUp']['min']."-".$pickup['delivery']['pickUp']['max']."дня";?>'>
		            <? endif;?>



                <?endforeach; ?>

            <?endforeach ?>
            <input id="ptnPvzCheck" type="hidden" value="Y">

            </div>
        <?
        }else{
            echo '<input id="ptnPvzCheck" type="hidden" value="N">';
        }


    }

}


