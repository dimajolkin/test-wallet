<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CurrencyService\Money;
use App\Service\CurrencyService\MoneyFactory;
use App\Service\CurrencyService\Operation\OperationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class WalletController extends AbstractController
{
    /**
     * @Route("/v1/user/{id}/wallet", name="user")
     * @param User|null $user
     * @return JsonResponse
     */
    public function wallet(?User $user)
    {
        if ($user === null) {
            return $this->notFoundResponse();
        }

        return $this->json($user->getWallet());
    }

    /**
     * @Route("/v1/user/{id}/wallet/operation", name="wallet_operation")
     * @param User $user
     * @param MoneyFactory $moneyFactory
     * @param OperationService $operationService
     * @param Request $request
     * @return JsonResponse
     */
    public function operation(User $user, MoneyFactory $moneyFactory, OperationService $operationService, Request $request)
    {
        $wallet = $user->getWallet();
        //@TODO add validation
        $money = $moneyFactory->build(
            $wallet,
            $request->request->get('currency'),
            $request->request->getInt('value')
        );
        $operationService->update($wallet, $money, $request->get('cause'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($wallet);
        $em->flush();

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
