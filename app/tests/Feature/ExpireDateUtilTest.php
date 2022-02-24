<?php

namespace Tests\Feature;

use App\Libs\ExpireDateUtil;
use Carbon\Carbon;
use Tests\TestCase;

class ExpireDateUtilTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_generate_expire_datetime_null()
    {
        $dateUtil = new ExpireDateUtil();

        $datetime = $dateUtil->generateExpireDatetime(null);

        $this->assertFalse($datetime);
    }

    public function test_generate_expire_datetime_add_7()
    {
        $dateUtil = new ExpireDateUtil();

        $datetime = $dateUtil->generateExpireDatetime('7');

        $actualDatetime = (string)Carbon::now()->addDay('7');

        $this->assertSame($datetime, $actualDatetime);
    }

    public function test_generate_expire_datetime_add_2()
    {
        $dateUtil = new ExpireDateUtil();

        $datetime = $dateUtil->generateExpireDatetime('2');

        $this->assertFalse($datetime);
    }

    public function test_generate_expire_datetime_add_0()
    {
        $dateUtil = new ExpireDateUtil();

        $datetime = $dateUtil->generateExpireDatetime('0');

        $this->assertNull($datetime);
    }

    public function test_check_expire_datetime_no_expire_limit()
    {
        $dateUtil = new ExpireDateUtil();

        $expireStatus = $dateUtil->checkExpireDate(null);

        $this->assertTrue($expireStatus);
    }

    public function test_check_expire_datetime_before_expire_limit()
    {
        $dateUtil = new ExpireDateUtil();

        $expireStatus = $dateUtil->checkExpireDate('2100-12-31 00:00:00');

        $this->assertTrue($expireStatus);
    }



    public function test_check_expire_datetime_after_expire_limit()
    {
        $dateUtil = new ExpireDateUtil();

        $expireStatus = $dateUtil->checkExpireDate('2022-01-01 00:00:00');

        $this->assertFalse($expireStatus);
    }
}
