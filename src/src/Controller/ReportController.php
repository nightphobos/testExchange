<?php
namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use App\Entity\Client;

class ReportController extends AbstractController
{
    private EntitymanagerInterface $em;

    /**
     * ReportController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }

    /**
     * @Get("/api/report/view/{userid}")
     * @OA\Response(
     *     response=200,
     *     description="отчет по заданному клиенту и / или выбранному периоду"
     * )
     * @OA\Parameter (
     *     name="from",
     *     in="query",
     *     required=false,
     *     description="дата начала отчета",
     *     schema=@OA\Schema (
     *         @OA\Items(type="string")
     *     )
     * )
     * @OA\Parameter (
     *     name="to",
     *     in="query",
     *     required=false,
     *     description="дата конца отчета",
     *     schema=@OA\Schema (
     *         @OA\Items(type="string")
     *     )
     * )
     * @param Request $request
     * @return Response
     */
    public function reportViewController(int $userid, Request $request): Response
    {
        $from=$request->get('from', null);
        $to=$request->get('to', null);
        $client=$this->em->getRepository(Client::class)->findOneBy(['id'=>$userid]);
        $currencyCode = $client->getWallet()->getCurrency();
        $currency = $this->em->getRepository(Currency::class)->findOneBy(['id'=>$currencyCode]);
        $data = $this->em->getRepository(Transaction::class)->getReportIdFromTo($userid, $currencyCode, $from, $to);

        $response = $this->renderView('report.html.twig', [
            'clientId' => $userid,
            'clientName' => $client->getName(),
            'period'=>$this->periodText($from, $to),
            'sum'=>$data->sum,
            'usdSum'=>$data->usdSum,
            'transactions'=>$data->transactions,
            'currency' => $currency->getName(),
            'downloadUrl' => sprintf('/api/report/download/%s?from=%s&to=%s',$userid, $from, $to)]);
        $response = new Response($response);
        $response->headers->set("content-type","text/html; charset=UTF-8");
        return $response;
    }

    /**
     * @Get("/api/report/download/{userid}")
     * @OA\Response(
     *     response=200,
     *     description="Скачать отчет по заданному клиенту и / или выбранному периоду"
     * )
     * @OA\Parameter (
     *     name="from",
     *     in="query",
     *     required=false,
     *     description="дата начала отчета",
     *     schema=@OA\Schema (
     *         @OA\Items(type="string")
     *     )
     * )
     * @OA\Parameter (
     *     name="to",
     *     in="query",
     *     required=false,
     *     description="дата конца отчета",
     *     schema=@OA\Schema (
     *         @OA\Items(type="string")
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function reportDownloadController(int $userid, Request $request): JsonResponse
    {
        $from=$request->get('from', null);
        $to=$request->get('to', null);
        $client=$this->em->getRepository(Client::class)->findOneBy(['id'=>$userid]);
        $currencyCode = $client->getWallet()->getCurrency();
        $data = $this->em->getRepository(Transaction::class)->getReportIdFromTo($userid, $currencyCode, $from, $to);

        return new JsonResponse($data);
    }

    private function periodText(string $from, string $to) {
        if (is_null($from) && is_null($to)) {
            return 'Без ограничений';
        }
        if (!is_null($from) && !is_null($to)){
            return sprintf("От %s до %s", $from, $to);
        }
        if (is_null($from)){
            return sprintf("До %s", $to);
        }
        if(is_null($to)){
            return sprintf("С %s", $from);
        }


    }
}
