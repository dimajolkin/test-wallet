<?php

namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;

class UserCest
{
    public function getNotFoundUser(AcceptanceTester $I)
    {
        $I->sendGET('/v1/user/8888');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"message":"not found"}');
    }

    public function createUser(AcceptanceTester $I)
    {
        $I->sendPOST('/v1/user', [
            'name' => 'ivan',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
    }

}
