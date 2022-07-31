<?php


namespace Librevlad\PhoneInfo\Def;


class DefRegionToRegionMapper {

    protected $mapFile = 'mappings/def_regions_to_regions.csv';

    protected $map = [];

    public function __construct() {

        $this->map = collect( floadcsv( dirname( __FILE__ ) . '/../../data/' . $this->mapFile, true ) )->mapWithKeys( function ( $v ) {
            return [ $v[ 'def' ] => $v[ 'normal' ] ];
        } );

    }

    public function region( $def ) {

        //        return $def ? $this->map[ $def ] : 'Вся страна';
        return $def ? $this->map[ $def ] : null;
    }

}
