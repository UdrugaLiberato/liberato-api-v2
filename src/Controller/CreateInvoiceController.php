<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Image\ImageUploader;
use App\Repository\BankAccountRepository;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreateInvoiceController extends AbstractController
{
    private $publicPath;

    public function __construct(
        private BankAccountRepository   $bankAccountRepository,
        private ProjectRepository       $projectRepository,
        private ImageUploader           $imageUploader,
        private MailerInterface         $mailer,
        protected ParameterBagInterface $parameterBag
    )
    {
        $this->publicPath = $this->parameterBag->get('kernel.project_dir');
    }

    public function __invoke(
        Request            $request,
        ManagerRegistry    $doctrine,
        ValidatorInterface $validator,
        SluggerInterface   $slugger
    )
    {
        $entityManager = $doctrine->getManager();
        $uploadedFiles = $request->get("files");
        $files = [];
        $pid = explode("/", $request->get("project"));
        $project = $this->projectRepository->findOneBy(['id' => $pid]);
        $invoice = new Invoice();
        $invoice->setDescription($request->request->get("description"));
        $invoice->setAmount($request->get("amount"));
        $invoice->setProject($project);
        $invoice->setPayedAt(new \DateTimeImmutable($request->get("payedAt")));

        $json = json_decode($uploadedFiles, true);
        foreach ($json as $uploadedFile) {
            $file = $this->imageUploader->convertFile($uploadedFile["src"]);
            $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('invoices_directory'),
                $newFilename
            );
            $files[] = ["filename" => $uploadedFile["title"], "path" => '/media/invoices/' .
                $newFilename];
        }
        $invoice->setFiles($files);
        $project->addInvoice($invoice);

        $account = $this->bankAccountRepository->findAll()[0];
        $oldAmount = $account->getAmount();
        $account->setAmount($oldAmount - $request->get("amount"));
        $this->bankAccountRepository->add($account);
        $entityManager->persist($invoice);
        $entityManager->flush();

        $this->sendMail($invoice);
        return $invoice;
    }

    private function sendMail(Invoice $invoice)
    {

        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Udruga Liberato Racun!')
            ->text('Racuni u prilogu!')
            ->html('<p>Racuni u prilogu!</p>');


        foreach ($invoice->getFiles() as $file) {
//            dd($this->publicPath . '/public' . $file["path"]);
            $email->attachFromPath($this->publicPath . '/public' . $file["path"], $file["filename"]);
        }
        $this->mailer->send($email);

    }
}