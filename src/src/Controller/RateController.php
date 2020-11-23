<?php
namespace App\Controller;

use App\Component\ORMDate;
use App\Entity\Rate;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;


class RateController
{
    private EntitymanagerInterface $em;

    /**
     * RateController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }

    /**
     * @Post("/api/rate")
     * @OA\RequestBody(
     *     required=true,
     *     description="Запись курса на определенную валюту в определенный день",
     *     @OA\JsonContent(
     *         type="object",
     *          @OA\Property(
     *              property="currency",
     *              type="integer",
     *              description="код валюты"
     *          ),
     *          @OA\Property(
     *              property="date",
     *              type="string",
     *              description="дата создания"
     *          ),
     *          @OA\Property(
     *              property="value",
     *              type="number",
     *              format="float",
     *              description="курс валюты"
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Успешная запись"
     * )
     * @OA\Response(
     *     response=400,
     *     description="значение или валюта некорректны",
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
    public function rateController(Request $request): JsonResponse
    {
        $currencyCode = $request->request->get('currency');
        $date = new ORMDate($request->request->get('date'));
        $value = (float) $request->request->get('value', 0);

        if ($value===0) {
            $response = new \stdClass();
            $response->error="недопустимое значение курса";
            return new JsonResponse($response,400);
        }

        //проверим, может запись уже существует
        $rate = $this->em->getRepository(Rate::class)->findOneBy(['currency'=>$currencyCode, 'date'=>$date]);

        if ($rate===null){
            $rate=new Rate();
            $rate->setCurrency($currencyCode);
            $rate->setDate($date);
        }

        $rate->setValue($value);

        $this->em->persist($rate);
        $this->em->flush();

        return new JsonResponse(null,200);
    }
}
