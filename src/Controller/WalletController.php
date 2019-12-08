<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
