<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Encode le mdp
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $facker = Factory::create('fr_FR');

        for($u=0; $u<10; $u++) {
            $user = new Utilisateur();
            $chrono = 1;
            $hash = $this->encoder->encodePassword($user, "password");

            $user->setFirstName($facker->firstName())
                 ->setLastName($facker->lastName)
                 ->setEmail($facker->email)
                 ->setPassword($hash);

            $manager->persist($user);

            for($c=0; $c< mt_rand(5, 20); $c++) {
                $customer = new User();
                $customer->setFirstname($facker->firstName())
                         ->setLastName($facker->lastName)
                         ->setCompany($facker->company)
                         ->setEmail($facker->email)
                         ->setUser($user);
    
                $manager->persist($customer);
    
                for($i=0;$i<mt_rand(3,10); $i++)
                {
                    $invoice = new Invoice();
                    $invoice->setAmout($facker->randomFloat(2,2, 5000))
                            ->setSentAt($facker->dateTimeBetween('-6 months'))
                            ->setStatus($facker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                            ->setUser($customer)
                            ->setChrono($chrono);
    
                            $chrono++;
    
                            $manager->persist($invoice);
                }
            }
        }

        $manager->flush();
    }
}
