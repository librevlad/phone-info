<?php


namespace Librevlad\PhoneInfo;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberToTimeZonesMapper;
use libphonenumber\PhoneNumberUtil;
use Librevlad\PhoneInfo\Def\Database;
use Librevlad\PhoneInfo\Def\DefRegionToRegionMapper;


class PhoneInfo {

    /**
     * @var int
     */
    public $phone;
    /**
     * @var string|null
     */
    protected $country;

    public function __construct( $phone, string $country = null ) {
        if ( Str::startsWith( $phone, '+' ) ) {
            $phone = substr( $phone, 1 );
        }
        $this->phone   = $phone;
        $this->country = $country;
    }

    public function isLandline(): ?bool {

        if ( $this->country() == 'RU' ) {
            return Str::startsWith( $this->fullPhone(), 79 ) ? false : true;

        }
        if ( $this->country() == 'LV' ) {
            if ( Str::startsWith( $this->fullPhone(), 3712 ) ) {
                return false;
            }

            return true;
        }

        if ( $this->country() == 'BY' ) {

            if ( Str::startsWith( $this->fullPhone(), [
                37525,
                37529,
                37533,
                37544,
            ] ) ) {
                return false;
            }

            return true;
        }
        if ( $this->country() == 'UA' ) {

            if ( Str::startsWith( $this->fullPhone(), [
                38067,
                38068,
                38096,
                38097,
                38098,
                38050,
                38066,
                38095,
                38099,
                38063,
                38073,
                38093,
                38089,
                38094,
            ] ) ) {
                return false;
            }

            return true;
        }
        if ( $this->country() == 'KZ' ) {

            if ( Str::startsWith( $this->fullPhone(), [
                7727,
                7700,
                7708,
                7705,
                7771,
                7776,
                7777,
                7701,
                7702,
                7775,
                7778,
                7707,
                7747,
            ] ) ) {
                return false;
            }

            return true;
        }

        return null;
    }

    public function timezone() {

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse( '' . $this->phone, $this->country() );
        }
        catch ( NumberParseException $e ) {
            return null;
        }

        // not detected

        if ( $this->country() == 'LV' ) {
            return 'Europe/Helsinki';
        }

        if ( $this->country() == 'UA' ) {
            return 'Europe/Kiev';
        }

        if ( $this->country() == 'BY' ) {
            return 'Europe/Istanbul';
        }

        if ( $this->country() == 'RU' ) {
            $map = floadcsv( dirname( __FILE__ ) . '/../data/mappings/ru_regions_to_timezones.csv', true );
            $reg = $this->region();
            foreach ( $map as $r ) {
                if ( $r[ 'region' ] == $reg ) {
                    return $r[ 'timezone' ] ?: null;
                }

            }
            //            var_dump($reg,$map); die();
        }

        $timeZoneMapper = PhoneNumberToTimeZonesMapper::getInstance();
        $timeZones      = $timeZoneMapper->getTimeZonesForNumber( $swissNumberProto );
        //        echo implode( $timeZones, PHP_EOL );
        $tz = null;
        if ( count( $timeZones ) > 0 ) {
            $tz = $timeZones[ 0 ];
            if ( $tz == 'Etc/Unknown' ) {
                return null;
            }
        }

        return $tz;
    }

    public function carrier() {
        if ( $this->country() == 'KZ' ) {
            $mobiles =
                [
                    '700' => 'АЛТЕЛ',
                    '701' => 'Кселл',
                    '702' => 'Кселл',
                    '703' => 'резерв для сотовых операторов',
                    '704' => 'резерв для сотовых операторов',
                    '705' => 'ТОО «КаР-Тел» (ВымпелКом, Beeline)',
                    '706' => 'резерв для сотовых операторов',
                    '707' => 'ТОО «Мобайл Телеком-Сервис» (Tele2)',
                    '708' => 'АЛТЕЛ',
                    '709' => 'резерв для сотовых операторов',
                    '747' => 'ТОО «Мобайл Телеком-Сервис» (Tele2)',
                    '750' => 'АО «Казахтелеком» (коммутируемый доступ)',
                    '751' => 'АО «Казахтелеком» (передача данных)',
                    '760' => 'АО «Казахтелеком» (Спутниковая сеть Кулан)',
                    '761' => 'АО «Казахтелеком»',
                    '762' => 'АО «NURSAT»',
                    '763' => 'АО «Арна»',
                    '764' => 'АО «2 Day Telecom»',
                    '771' => 'ТОО «КаР-Тел» (ВымпелКом, Beeline)',
                    '775' => 'Кселл',
                    '776' => 'ТОО «КаР-Тел» (ВымпелКом, Beeline)',
                    '777' => 'ТОО «КаР-Тел» (ВымпелКом, Beeline)',
                    '778' => 'Кселл',
                ];
            //            $mobiles = new Collection($mobiles);
            foreach ( $mobiles as $k => $v ) {
                if ( Str::startsWith( $this->phone, '7' . $k ) ) {
                    return $v;
                }
            }

        }
        if ( $this->country() == 'RU' ) {
            // Подключаем DEF
            $def = Database::getInstance();

            return $def->operatorByPhoneNumber( $this->phone );

            //            $code = substr( $this->phone, 1, 3 );
            //            $rest = substr( $this->phone, 4 );

            //            $model = \App\Models\PhoneCode::where( 'code', $code )->where( 'start', '<', $rest )->orderByDesc( 'id' )->first();

            //            return $model->operator;

        }

        // Все остальные страны
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse( '' . $this->phone, $this->country() );
        }
        catch ( NumberParseException $e ) {
            return null;
        }
        $carrierMapper = PhoneNumberToCarrierMapper::getInstance();
        // Outputs "Swisscom"
        $car = $carrierMapper->getNameForNumber( $swissNumberProto, "ru_RU" );

        $car = $car ?: null;

        return $car;
    }

    public function region() {

        $phoneUtil = PhoneNumberUtil::getInstance();
        $geocoder  = PhoneNumberOfflineGeocoder::getInstance();

        try {
            $swissNumberProto = $phoneUtil->parse( '' . $this->phone, $this->country() );
        }
        catch ( NumberParseException $e ) {
            return null;
        }


        if ( $this->country() == 'RU' ) {
            if ( $this->isLandline() ) // Подключаем DEF
            {
                return null;
                // не хотим мы возиться с маппингом геокодера
                //                return $geocoder->getDescriptionForNumber( $swissNumberProto, "ru_RU" );
            }

            //            $def = new Database();
            $def = Database::getInstance();
            $reg = $def->regionByPhoneNumber( $this->phone );

            // map def to normal

            $m = new DefRegionToRegionMapper();

            return $m->region( $reg );


            //            return app( 'def' )->regionByPhoneNumber( $this->phone );
        }

        //        return 'Неизвестно';

        // Все остальные страны
        return $geocoder->getDescriptionForNumber( $swissNumberProto, "ru_RU" );
    }

    public function country() {

        if ( $this->country ) {
            return $this->country;
        }


        // itak int
        //        $phone = digits( $_phone );
        $fullUaNumber = $this->guessFullUaNumber( $this->phone );
        if ( $fullUaNumber ) {
            $this->country = 'UA';
        }

        $fullByNumber = $this->guessFullByNumber( $this->phone );
        //        dd($fullByNumber);
        if ( $fullByNumber ) {
            $this->country = 'BY';
        }


        $fullLvNumber = $this->guessFullLvNumber( $this->phone );
        if ( $fullLvNumber ) {
            $this->country = 'LV';
        }


        $fullRuNumber = $this->guessFullRuKzNumber( $this->phone );

        if ( $fullRuNumber ) {
            // +7 might be!
            $this->country = 'RU';

            if ( Str::startsWith( $fullRuNumber, [ 77, 76 ] ) ) {
                $this->country = 'KZ';
            }

        }

        return $this->country;
    }

    public function fullPhone() {
        return $this->guessFullRuKzNumber( $this->phone ) ?: $this->phone;
    }

    protected function guessFullRuKzNumber( $phone ) {

        if ( Str::startsWith( $phone, [ 7, 8, 9 ] ) ) {
            // Russia KZ Abhazia land or mob

            if ( strlen( $phone ) == 11 ) {
                if ( Str::startsWith( $phone, '7' ) ) {
                    return $phone;
                }
            }

            if ( strlen( $phone ) == 11 ) {
                if ( Str::startsWith( $phone, '8' ) ) {
                    $a = (int) '7' . mb_substr( $phone, 1 );

                    return $a;
                }
            }
            if ( strlen( $phone ) == 11 ) {
                if ( Str::startsWith( $phone, '89' ) ) {
                    return (int) '79' . mb_substr( $phone, 2 );
                }
            }
            if ( strlen( $phone ) == 10 ) {
                if ( Str::startsWith( $phone, '9' ) ) {
                    return (int) '7' . $phone;
                }
            }
            if ( strlen( $phone ) == 9 ) {
                return (int) '79' . $phone;
            }

        }
    }

    protected function guessFullLvNumber( $phone ) {

        if ( strlen( $phone ) == 11 ) {
            if ( Str::startsWith( $phone, 371 ) ) {
                return $phone;
            }
        }


        if ( strlen( $phone ) == 8 ) {
            if ( Str::startsWith( $phone, 6 ) ) {
                return '371' . $phone;
            }
        }

        if ( strlen( $phone ) == 8 ) {
            if ( Str::startsWith( $phone, 2 ) ) {
                return '371' . $phone;
            }
        }


    }

    protected function guessFullUaNumber( $phone ) {

        if ( strlen( $phone ) == 12 ) {
            if ( Str::startsWith( $phone, 380 ) ) {
                return $phone;
            }
        }

        if ( strlen( $phone ) == 10 ) {
            if ( Str::startsWith( $phone, 0 ) ) {
                return '38' . $phone;
            }
        }


    }

    public function valid() {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse( '' . $this->phone, $this->country() );
        }
        catch ( NumberParseException $e ) {
            return false;
        }

        return $phoneUtil->isValidNumber( $swissNumberProto );
    }

    protected function guessFullByNumber( $phone ) {

        if ( strlen( $phone ) == 12 ) {
            if ( Str::startsWith( $phone, 375 ) ) {
                return $phone;
            }
        }
        // LV
        if ( strlen( $phone ) == 11 ) {
            if ( Str::startsWith( $phone, 371 ) ) {
                return $phone;
            }
        }

        if ( strlen( $phone ) == 9 ) {
            if ( Str::startsWith( $phone, 8 ) ) {
                return '375' . $phone;
            }
        }


    }

}
