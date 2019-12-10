<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Service\CurrencyService\MoneyFactory;
use App\Service\CurrencyService\Operation\OperationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class WalletController extends AbstractController
{
    public function checkWallet(?User $user, ?Wallet $wallet)
    {
        return $user === null || $wallet === null || ($user->getWallet()->getId() !== $wallet->getId());
    }
    /**
     * @Route("/v1/user/{id}/wallet/{wallet_id}", name="wallet")
     * @Entity("wallet", expr="repository.find(wallet_id)")
     * @param User|null $user
     * @param Wallet $wallet
     * @return JsonResponse
     */
    public function wallet(?User $user, ?Wallet $wallet)
    {
        if ($this->checkWallet($user, $wallet)) {
            return $this->notFoundResponse();
        }

        $wallet = $user->getWallet();
        return $this->json([
            'currency' => $wallet->getCurrency()->getName(),
            'value' => $wallet->getFormatValue(),
            'date_create' => $wallet->getDateCreate(),
            'date_update' => $wallet->getDateUpdate(),
        ]);
    }

    /**
     * @Route("/v1/user/{id}/wallet/{wallet_id}/balance", name="balance")
     * @Entity("wallet", expr="repository.find(wallet_id)")
     * @param User|null $user
     * @param Wallet|null $wallet
     * @return JsonResponse
     */
    public function balance(?User $user, ?Wallet $wallet)
    {
        if ($this->checkWallet($user, $wallet)) {
            return $this->notFoundResponse();
        }

        return $this->json([
            'balance' => $wallet->getFormatValue(),
            'currency' => $wallet->getCurrency()->getName(),
        ]);
    }

    /**
     * @Route("/v1/user/{id}/wallet/{wallet_id}/operation", name="wallet_operation")
     * @Entity("wallet", expr="repository.find(wallet_id)")
     * @param User $user
     * @param Wallet $wallet
     * @param MoneyFactory $moneyFactory
     * @param OperationService $operationService
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exception\DomainException
     */
    public function operation(
        ?User $user,
        ?Wallet $wallet,
        MoneyFactory $moneyFactory,
        OperationService $operationService,
        Request $request
    ) {
        if ($this->checkWallet($user, $wallet)) {
            return $this->notFoundResponse();
        }

        $wallet = $user->getWallet();
        $money = $moneyFactory->build(
            $wallet,
            $request->request->get('currency'),
            $request->request->get('value')
        );
        $operationService->update(
            $wallet,
            $money,
            $request->get('cause'),
            $request->get('type')
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($wallet);
        $em->flush();

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
