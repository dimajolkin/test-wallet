<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return JsonResponse
     * @throws \Exception
     */
    public function createUser(Request $request)
    {
        $form = $this->createForm(UserType::class, new User());
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $wallet = new Wallet();
            $wallet->setValue(0);
            $wallet->setCurrencyId(1);
            $wallet->setDateCreate(new \DateTime('now'));
            $wallet->setDateUpdate(new \DateTime('now'));

            $user->setWallet($wallet);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->json($user);
        }

        return $this->json([
            'errors' => (string) $form->getErrors(),
        ]);
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
