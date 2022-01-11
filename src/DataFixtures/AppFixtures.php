<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $chrono = 1;
            $hash = $this->encoder->hashPassword($user, 'password');
            $user->setLastName($faker->lastName)
                ->setFirstName($faker->firstName())
                ->setEmail($faker->email)
                ->setPassword($hash);

            $manager->persist($user);

            for ($c = 0; $c < mt_rand(5, 20); $c++) {
                $customer = new Customer();
                $customer->setFirstname($faker->firstName)
                    ->setLastname($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail($faker->email)
                    ->setUser($user);

                $manager->persist($customer);

                for ($i = 0; $i < mt_rand(3, 10); $i++) {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SEND', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);


                    $chrono++;

                    $manager->persist($invoice);
                }
            }
        }



        $manager->flush();
    }
}
