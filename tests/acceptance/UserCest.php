<?php

namespace App\Tests\acceptance;

use App\Entity\CauseEnum;
use App\Entity\TypeEnum;
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
     * @param AcceptanceTester $I
     * @param \Codeception\Example $example
     * @return mixed
     * @example { "wallet_currency": "USD"}
     * @example { "wallet_currency": "RUB"}
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
        $I->sendGET('/v1/user/1/wallet/2');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function getWallet(AcceptanceTester $I): array
    {
        $id = $this->createUser($I, new Example(['wallet_currency' => 'RUB']));
        $I->assertNotEmpty($id);
        $I->sendGET("/v1/user/$id/wallet/{$id}");
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->canSeeResponseMatchesJsonType([
            'value' => 'float:=0|integer:=0',
            'currency' => 'string:!empty',
            'date_create' => 'string:!empty',
            'date_update' => 'string:!empty',
        ]);
        return json_decode($I->grabResponse(), true);
    }


    /**
     * @param AcceptanceTester $I
     *
     * @param Example $example
     * @throws \Exception
     *
     *
     * @example {"wallet_currency": "RUB", "currency": "USD", "value": 1, "result": 70}
     * @example {"wallet_currency": "RUB", "currency": "USD", "value": 2, "result": 140}
     *
     * @example {"wallet_currency": "USD", "currency": "RUB", "value": 70, "result": 1}
     * @example {"wallet_currency": "USD", "currency": "RUB", "value": 140, "result": 2}
     *
     * @example {"wallet_currency": "USD", "currency": "USD", "value": 1, "result": 1}
     * @example {"wallet_currency": "USD", "currency": "USD", "value": 100, "result": 100}
     *
     * @example {"wallet_currency": "RUB", "currency": "RUB", "value": 1, "result": 1}
     * @example {"wallet_currency": "RUB", "currency": "RUB", "value": 100, "result": 100}
     */
    public function putMoneyInWallet(AcceptanceTester $I, Example $example)
    {
        $I->haveInDatabase('currency_rate', ['currency_id' => 1, 'value' => 100, 'date_create' => '3019-12-09 22:56:59']);
        $I->haveInDatabase('currency_rate', ['currency_id' => 2, 'value' => 7000, 'date_create' => '3019-12-09 22:56:59']);

        $id = $this->createUser($I, $example);
        $walletId = (int) $I->grabFromDatabase('user', 'wallet_id', ['id' => $id]);
        $I->seeInDatabase('wallet', ['id' => $walletId, 'value' => 0]);
        $I->sendPOST("/v1/user/{$id}/wallet/{$walletId}/operation", [
            'currency' => $example['currency'],
            'value' => $example['value'],
            'type' => TypeEnum::DEBIT,
            'cause' => CauseEnum::REFUND,
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"status":"ok"}');

        $I->sendGET("/v1/user/$id/wallet/{$walletId}/balance");
        $balance = json_decode($I->grabResponse(), true);
        $I->assertEquals($example['result'], $balance['balance']);
    }

    /**
     * @param AcceptanceTester $I
     * @param Example $example
     *
     * @example {"var": 12}
     * @example {"currency": "RUBLIK", "value": 1, "type": "debit", "cause": "refund"}
     * @example {"currency": "RUB", "value": "string", "type": "debit", "cause": "refund"}
     * @example {"currency": "RUB", "value": 1, "type": "string", "cause": "refund"}
     * @example {"currency": "RUB", "value": 1, "type": "debit", "cause": "string"}
     */
    public function testValidationOperation(AcceptanceTester $I, Example $example)
    {
        $id = $this->createUser($I, new Example(['wallet_currency' => 'RUB']));
        $I->sendPOST("/v1/user/{$id}/wallet/{$id}/operation", iterator_to_array($example));
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }
}
