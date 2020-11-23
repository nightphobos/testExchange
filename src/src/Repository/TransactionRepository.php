<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class TransactionRepository extends EntityRepository {
    public function getReportIdFromTo(int $id, int $currencyId, ?string $from, ?string $to){
        //https://dba.stackexchange.com/questions/51340/should-i-join-datetime-to-a-date-using-cast-or-range
        // интересное обсуждение на тему join datetime<->date

        $params=[];
        $params['id']=$id;
        $params['currencyid']=$currencyId;
        $query="SELECT t.created, t.value AS usd_value, CAST(t.value*r.value AS DECIMAL(10,2)) AS value, t.to, t.from
                            FROM   transaction t
                            LEFT OUTER JOIN rate r
                            ON CAST(t.created AS DATE) = r.date
                            WHERE (t.to=:id OR t.from=:id) AND r.`currency`=:currencyid";

        if (!is_null($from)){
            $query.=" AND t.created > :from";
            $params['from']=$from;
        }

        if (!is_null($to)){
            $query.=" AND t.created < :to";
            $params['to']=$to;
        }

        $stmt = $this->getEntityManager()->getConnection()
            ->prepare($query);
        $stmt->execute($params);

        $result=new \stdClass();
        $result->transactions=[];
        $usdSum=0;
        $sum=0;
        while ($row = $stmt->fetch()){
            $direction = $row['to']==$id ? 1 : -1;
            $result->transactions[]=[$row['created'], $row['value']*$direction ];
            $usdSum+=$row['usd_value']*$direction;
            $sum+=$row['value']*$direction;
        }

        $result->usdSum=round($usdSum,2);
        $result->sum=round($sum, 2);
        return $result;
    }
}