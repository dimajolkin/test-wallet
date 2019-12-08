<?php

namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;
use Codeception\Util\HttpCode;

class UserCest
{
    private function getUserId(AcceptanceTester $I, $id): array
    {
        $I->sendGET('/v1/user/' . $id);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        return json_decode($I->grabResponse(), true);
    }

    public function getNotFoundUser(AcceptanceTester $I)
    {
        $I->sendGET('/v1/user/8888');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"message":"not found"}');
    }

    public function createUser(AcceptanceTester $I)
    {
        $I->sendPOST('/v1/user', [
            'name' => 'ivan',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $createdUser = json_decode($I->grabResponse(), true);
        $I->assertEquals($createdUser, $this->getUserId($I, $createdUser['id']));
    }

    public function getNotFoundWallet(AcceptanceTester $I)
    {
        $I->sendGET('/v1/user/8888/wallet');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }
}
