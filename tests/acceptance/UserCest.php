<?php

namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;
use Codeception\Example;
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

    /**
     * @example { "wallet_currency": "RUB"}
     * @example { "wallet_currency": "USD"}
     * @param AcceptanceTester $I
     * @param \Codeception\Example $example
     * @return mixed
     */
    public function createUser(AcceptanceTester $I, \Codeception\Example $example)
    {
        $I->sendPOST('/v1/user', [
            'name' => 'ivan',
            'wallet_currency' => $example['wallet_currency'],
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseMatchesJsonType([
            'id' => 'integer:>0',
            'name' => 'string:!empty',
            'wallet' => [
                'currency' => [
                    'name' => 'string:=' . $example['wallet_currency'],
                ],
            ],
        ]);

        $createdUser = json_decode($I->grabResponse(), true);
        $I->assertEquals($createdUser, $this->getUserId($I, $createdUser['id']));
        return $createdUser['id'];
    }

    public function getNotFoundWallet(AcceptanceTester $I)
    {
        $I->sendGET('/v1/user/8888/wallet');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function getWallet(AcceptanceTester $I)
    {
        $id = $this->createUser($I, new Example(['wallet_currency' => 'RUB']));
        $I->assertNotEmpty($id);
        $I->sendGET("/v1/user/$id/wallet");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->canSeeResponseMatchesJsonType([
           'value' => 'integer:=0',
           'currency' => [
               'name' => 'string:!empty',
           ],
           'date_create' => 'string:!empty',
           'date_update' => 'string:!empty',
        ]);
    }


    public function putMoneyInWallet(AcceptanceTester $I)
    {

    }
}
