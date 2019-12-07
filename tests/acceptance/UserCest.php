<?php

namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;

class UserCest
{
    private function getUserId(AcceptanceTester $I, $id): array
    {
        $I->sendGET('/v1/user/' . $id);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        return json_decode($I->grabResponse(), true);
    }
    public function getNotFoundUser(AcceptanceTester $I)
    {
        $I->sendGET('/v1/user/16');
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
        $createdUser = json_decode($I->grabResponse(), true);
        $I->assertEquals($createdUser, $this->getUserId($I, $createdUser['id']));
    }

}
