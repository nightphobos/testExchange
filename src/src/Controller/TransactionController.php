<?php
namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Transaction;
use App\Entity\Rate;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Client;
use App\Component\ORMDate;

class TransactionController
{
    private EntitymanagerInterface $em;

    /**
     * TransactionController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }

    /**
     * @Post("/api/transaction")
     * @OA\RequestBody(
     *     required=true,
     *     description="Поля необходимые для создания клиента",
     *     @OA\JsonContent(
     *         type="object",
     *          @OA\Property(
     *              property="from",
     *              type="integer",
     *              description="Идентификатор кошелька с которого осуществляется перевод"
     *          ),
     *          @OA\Property(
     *              property="to",
     *              type="integer",
     *              description="Идентификатор кошелька на который осуществляется перевод"
     *          ),
     *          @OA\Property(
     *              property="currency",
     *              type="string",
     *              description="Валюта перевода, 'from' - в валюте отправителя, 'to' - в валюте получателя"
     *          ),
     *          @OA\Property(
     *              property="value",
     *              type="number",
     *              format="float",
     *              description="Сумма перевода в валюте по полю currency"
     *          )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="id проведенной транзакции",
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
     *     description="ошибка транзакции",
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
    public function transactController(Request $request): JsonResponse
    {
        $fromCode = (int) $request->request->get('from');
        $toCode = (int) $request->request->get('to');
        $currencySide = (int) $request->request->get('currency');
        $value = $request->request->get("value");

        /** @var Wallet $from */
        $from=$this->em->getRepository(Wallet::class)->findOneBy(['id'=>$fromCode]);
        if($from==null){
            return new JsonResponse("{error:'некорректный кошелек отправителя'}", 400);
        }
        /** @var Wallet $to */
        $to=$this->em->getRepository(Wallet::class)->findOneBy(['id'=>$toCode]);

        //все транзакции - проводятся по дате транзакции(сегодня)
        $today = new ORMDate();

        if ($currencySide == 'from') {
            /** @var Rate $rateFrom */
            $rate = $this->em->getRepository(Rate::class)->findOneBy(['currency' => $from->getCurrency(), 'date' => $today]);
        } else {
            /** @var Rate $rateTo */
            $rate = $this->em->getRepository(Rate::class)->findOneBy(['currency' => $to->getCurrency(), 'date' => $today]);
        }

        $valueUSD=$value/$rate->getValue();

        $transaction=new Transaction();
        $transaction->setFrom($fromCode);
        $transaction->setTo($toCode);
        $transaction->setValue($valueUSD);

        $this->em->persist($transaction);
        $this->em->flush();

        $response=new \stdClass();
        $response->id=$transaction->getId();

        return new JsonResponse($response);
    }
}
