<?php
namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Client;

class ClientController
{
    private EntitymanagerInterface $em;

    /**
     * ClientController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }

    /**
     * @Post("/api/client")
     * @OA\RequestBody(
     *     required=true,
     *     description="Создание нового клиента",
     *     @OA\JsonContent(
     *         type="object",
     *          @OA\Property(
     *              property="name",
     *              type="string",
     *              description="Имя клиента"
     *          ),
     *          @OA\Property(
     *              property="country",
     *              type="string",
     *              description="Страна клиента"
     *          ),
     *          @OA\Property(
     *              property="city",
     *              type="string",
     *              description="Город клиента"
     *          ),
     *          @OA\Property(
     *              property="currency",
     *              type="integer",
     *              description="Идентификатор валюты кошелька клиента"
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="id созданного клиента",
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property (
     *              property="id",
     *              type="integer"
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=400,
     *     description="ошибка создания клиента",
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property (
     *              property="error",
     *              type="string"
     *          )
     *      )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function createController(Request $request): JsonResponse
    {
        $currencyCode = $request->request->get('currency');
        $name = $request->request->get('name');
        $country = $request->request->get('country');
        $city=$request->request->get("city");

        $client = new Client();
        $client->setCity($city);
        $client->setName($name);
        $client->setCountry($country);

        $wallet = new Wallet();
        $wallet->setCurrency($currencyCode);
        $wallet->setValue(0);

        $client->setWallet($wallet);

        $this->em->persist($wallet);
        $this->em->persist($client);
        $this->em->flush();

        $response=new \stdClass();
        $response->id=$client->getId();
        return new JsonResponse($response);


        //Имя клиента не является уникальным полем - этот метод всегда будет работать. Даже если клиент с такими данными уже есть.

    }
}
