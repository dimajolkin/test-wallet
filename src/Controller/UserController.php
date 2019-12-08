<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Exception\DomainException;
use App\Form\UserType;
use App\Repository\CurrencyRepository;
use App\Service\UserService\WalletFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;

class UserController extends AbstractController
{
    protected const GROUPS = ['rest'];

    /**
     * @Route("/v1/user/{id}", name="get_user", methods={"GET"})
     * @param User|null $user
     * @return JsonResponse
     */
    public function user(?User $user)
    {
        if ($user === null) {
            return $this->notFoundResponse();
        }

        return $this->json($user);
    }

    /**
     * @Route("/v1/user", name="create_user", methods={"POST"})
     *
     * @param Request $request
     * @param WalletFactory $walletFactory
     * @return JsonResponse
     * @throws \Exception
     */
    public function createUser(Request $request, WalletFactory $walletFactory)
    {
        try {
            $wallet = $walletFactory->buildEmpty($request->request->get('wallet_currency', null));
            $request->request->remove('wallet_currency');

            $form = $this->createForm(UserType::class, new User());
            $form->submit($request->request->all());
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var User $user */
                $user = $form->getData();
                $user->setWallet($wallet);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->json($user);
            }
            return $this->json([
                'errors' => (string) $form->getErrors(),
            ]);
        } catch (ValidatorException $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


//     * @Route("/v1/user/{id}", name="user", methods={"GET"})
//     * @param User $user
//     * @return JsonResponse
//     */
//    public function user()
//    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/UserController.php',
//        ]);
//    }
//

}
