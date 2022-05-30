<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Image\ImageUploader;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreateInvoiceController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ImageUploader     $imageUploader
    )
    {
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
            $files[] = ["filename" => $uploadedFile["title"], "path" => '/public/media/invoices/' .
                $newFilename];
        }
        $invoice->setFiles($files);
        $project->addInvoice($invoice);
        $entityManager->persist($invoice);
        $entityManager->flush();

        return $invoice;
    }
}