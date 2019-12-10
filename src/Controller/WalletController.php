<?php

namespace App\Controller;

use App\Entity\CauseEnum;
use App\Entity\TypeEnum;
use App\Entity\User;
use App\Entity\Wallet;
use App\Exception\ValidationException;
use App\Service\CurrencyService\MoneyFactory;
use App\Service\CurrencyService\Operation\OperationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Route("/v1/user/{id}/wallet/{wallet_id}/operation", name="wallet_operation", methods={"POST"})
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
        $constraint = new Assert\Collection([
            'currency' => new Assert\NotBlank(),
            'value' => new Assert\Optional([new Assert\NotBlank(), new Assert\Type(['type' => ['numeric']])]),
            'cause' => new Assert\Choice(['choices' => CauseEnum::ALL]),
            'type' => new Assert\Choice(['choices' => TypeEnum::ALL]),
        ]);
        $validator = Validation::createValidator();
        $violations = $validator->validate($request->request->all(), $constraint);
        if ($violations->count() !== 0) {
            throw new ValidationException($violations);
        }

        $money = $moneyFactory->build(
            $wallet,
            $request->request->get('currency'),
            $request->request->get('value')
        );
        $operationService->update(
            $wallet,
            $money,
            $request->request->get('cause'),
            $request->request->get('type')
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($wallet);
        $em->flush();

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
