<?php

namespace Librevlad\PhoneInfo\Tests;

use Librevlad\PhoneInfo\PhoneInfo;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase {

    public function testUaPhone() {
        $info = new PhoneInfo( '+380931112233' );
        $this->assertEquals( 'UA', $info->country() );
        $this->assertFalse( $info->isLandline() );
        $this->assertEquals( 'Europe/Kiev', $info->timezone() );
        $this->assertEquals( 'lifecell', $info->carrier() );

        $info = new PhoneInfo( 380561112233 );
        $this->assertEquals( 'UA', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Kiev', $info->timezone() );
        $this->assertNull( $info->carrier() );

        $info = new PhoneInfo( '0561112233' );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Kiev', $info->timezone() );

        $info = new PhoneInfo( '0561112233', 'UA' );
        $this->assertEquals( 'UA', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Kiev', $info->timezone() );
    }

    public function testByPhone() {
        $info = new PhoneInfo( '+375292456789' );
        $this->assertEquals( 'BY', $info->country() );
        $this->assertFalse( $info->isLandline() );
        $this->assertEquals( 'Europe/Istanbul', $info->timezone() );
        $this->assertEquals( 'МТС', $info->carrier() );

        $info = new PhoneInfo( 375232456789 );
        $this->assertEquals( 'BY', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Istanbul', $info->timezone() );
        $this->assertNull( $info->carrier() );
    }

    public function testLvPhone() {
        $info = new PhoneInfo( '+37126789012' );
        $this->assertEquals( 'LV', $info->country() );
        $this->assertFalse( $info->isLandline() );
        $this->assertEquals( 'Europe/Helsinki', $info->timezone() );
        $this->assertNull( $info->carrier() );

        $info = new PhoneInfo( 37162245678 );
        $this->assertEquals( 'LV', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Helsinki', $info->timezone() );
        $this->assertNull( $info->carrier() );
    }

    public function testKzPhone() {
        $info = new PhoneInfo( '+76101112233' );
        $this->assertEquals( 'KZ', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertNull( $info->timezone() );
        $this->assertNull( $info->carrier() );

        $info = new PhoneInfo( '+77781112233' );
        $this->assertEquals( 'KZ', $info->country() );
        $this->assertFalse( $info->isLandline() );
        $this->assertEquals( 'Asia/Almaty', $info->timezone() );
        $this->assertEquals( 'Кселл', $info->carrier() );

        $info = new PhoneInfo( '+76111112233' );
        $this->assertEquals( 'KZ', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertNull( $info->timezone() );
        $this->assertNull( $info->carrier() );
    }

    public function testRuPhone() {
        $info = new PhoneInfo( '+74951112233' );
        $this->assertEquals( 'RU', $info->country() );
        $this->assertTrue( $info->isLandline() );
        $this->assertEquals( 'Europe/Moscow', $info->timezone() );
        $this->assertNull( $info->region() );
        //        $this->assertEquals( 'Московская обл.', $info->region() );
        $this->assertNull( $info->carrier() );

        $info = new PhoneInfo( '+79046261757' );
        $this->assertEquals( 'RU', $info->country() );
        $this->assertFalse( $info->isLandline() );
        $this->assertEquals( 'Приморский край', $info->region() );
        $this->assertEquals( 'Asia/Vladivostok', $info->timezone() );
        $this->assertEquals( 'ПАО "Вымпел-Коммуникации"', $info->carrier() );
    }

}
