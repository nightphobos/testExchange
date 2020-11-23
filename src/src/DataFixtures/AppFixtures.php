<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use App\Entity\Rate;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;
use App\Component\ORMDate;

class AppFixtures extends Fixture
{
    private const DATE_FROM = "2020-07-01";
    private const CLIENTS = 50;
    private const TRANSACTIONS = 1000;


    public function load(ObjectManager $manager)
    {

        $sql = file_get_contents("src/Resources/currency.sql");  // Read file contents
        $manager->getConnection()->exec($sql);  // Execute native SQL

        echo "Импорт кодов валют завершен\r\n";
        $begin = new ORMDate( self::DATE_FROM );
        $end   = new ORMDate( );
        $end->modify('+1 day');


        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            for($n=2; $n <= 119; $n++){ //no rate for us dollars
                $rate = new Rate();
                $rate->setDate($i);
                $rate->setCurrency($n);
                $rate->setValue(mt_rand(1,9999)/100);
                $manager->persist($rate);
            }
            $manager->flush();
            echo "Генерация курсов валют за  ".$i->__toString()." из ".$end->__toString()." завершена\r\n";
        }

        echo "Генерация курсов валют завершена\r\n";

        // create 20 client with wallets
        for ($i = 0; $i < self::CLIENTS; $i++) {
            $wallet = new Wallet();
            $wallet->setValue(100);
            $wallet->setCurrency(mt_rand(1, 119)); //@TODO hardcoded based on currency list

            $client = new Client();
            $client->setName('product '.$i);
            $client->setCity("Город".$i);
            $client->setCountry("Страна".$i);
            $client->setWallet($wallet);
            $manager->persist($client);
            $manager->persist($wallet);
        }

        echo "Генерация кошельков и клиентов завершена\r\n";

        $begin = new ORMDate( self::DATE_FROM );
        $begin = $begin->getTimestamp();
        $end   = new ORMDate();
        $end = $end->getTimestamp();
        $timestep = ($end-$begin)/self::TRANSACTIONS;
        $timestep = (int) $timestep;
        $date = new ORMDate();

        $date->setTimestamp(1171502725);
        for ($i = 1; $i < self::CLIENTS; $i++) {
            for ($n=1; $n < self::TRANSACTIONS; $n++) {
                $trans = new Transaction();
                $date->setTimestamp($begin+$timestep*$n);
                $trans->setValue(mt_rand(1,99)/100);
                $trans->setTo(mt_rand(1,self::CLIENTS-1));
                $trans->setFrom($i);
                $trans->setCreated($date);
                $manager->persist($trans);
            }
            $manager->flush();
            echo "Генерация операций для клиента ".$i." из ".self::CLIENTS." завершена\r\n";
        }

        $manager->flush();
    }
}