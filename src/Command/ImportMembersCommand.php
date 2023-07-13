<?php

namespace App\Command;

use App\Entity\Member;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:import-members',
    description: 'Import members from a CSV file',
)]
class ImportMembersCommand extends Command
{
  public function __construct(
      private readonly string $projectDir,
      private readonly EntityManagerInterface $entityManager,
  ) {
    parent::__construct();
  }

  protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    $inputFile = $this->projectDir . '/public/members.csv';
    $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
    $csvRowAsArray = $decoder->decode(file_get_contents($inputFile), 'csv');
    $repository = $this->entityManager->getRepository(Member::class);
      foreach ($csvRowAsArray as $item) {
        $member = $repository->findOneBy(['OIB' => $item['oib']]);
        if (!$member) {
          $member = new Member();
        }
        $member->setFirstname($item['firstname']);
        $member->setLastname($item['lastname']);
        $member->setEmail($item['email']);
        $member->setIsStudent($item['student'] === "DA");
        $member->setIsActive(true);
        $member->setPhone($item['phone']);
        $member->setAddress($item['address']);
        $member->setCity($item['city']);
        $member->setDob(new \DateTimeImmutable($item['dob']));
        $member->setOIB($item['oib']);
        $member->setDisabledPercent($item['disabled_percentage']);
        $member->setJoinDate(new \DateTimeImmutable($item['join_date']));

        $this->entityManager->persist($member);
        $this->entityManager->flush();
    }


      return Command::SUCCESS;
    }
}
