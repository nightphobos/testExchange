<?php
namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Transaction;
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

class WalletController
{
    private EntitymanagerInterface $em;

    /**
     * WalletController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }

    /**
     * @Post("/api/wallet/topup")
     * @OA\RequestBody(
     *     required=true,
     *     description="Пополнение кошелька",
     *     @OA\JsonContent(
     *         type="object",
     *          @OA\Property(
     *              property="wallet",
     *              type="integer",
     *              description="Код кошелька"
     *          ),
     *          @OA\Property(
     *              property="value",
     *              type="number",
     *              format="float",
     *              description="Сумма для пополнения"
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="id созданного клиента",
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property (
     *              property="value",
     *              type="number",
     *              format="float",
     *              description="Новая сумма кошелька"
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Кошелек не найден",
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
    public function walletTopupController(Request $request): JsonResponse
    {
        $wallet = (int) $request->request->get('wallet');
        $value = (float) $request->request->get('value', 0);
        $response = new \stdClass();

        $targetValue = $this->em->getRepository(Wallet::class)->findOneBy(['id'=>$wallet]);
        if($targetValue===null){
            $response->error="Кошелек не найден";
            return new JsonResponse($response, 404);
        }

        /** @var Wallet $targetValue */
        $newValue=$targetValue->getValue()+$value;
        $targetValue->setValue($newValue);
        $this->em->persist($targetValue);

        //фиксируем операцию в истории
        $transaction = new Transaction();
        $transaction->setValue($value);
        $transaction->setTo($wallet);
        $transaction->setFrom(NULL); //признак "ручного" пополнения
        $this->em->persist($transaction);

        $this->em->flush();

        $response->value=$targetValue->getValue();//пусть округлением займется БД
        return new JsonResponse($response);
    }
}
