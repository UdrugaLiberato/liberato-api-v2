<?php

namespace App\DataTransformer\Project;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Project\ProjectOutput;
use App\Entity\Project;

class ProjectOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        $files = $object->getInvoices()->map(function ($invoice) {
            return $invoice->getFiles();
        });
        return new ProjectOutput(
            $object->getName(),
            $object->getDescription(),
            $object->getStart()->format("Y-m-d H:i:s"),
            $object->getEnd()->format("Y-m-d H:i:s"),
            $object->getMoneyNeeded(),
            $object->getMoneyGathered(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s'),
            $object->getDonationGivers(),
            $files
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ProjectOutput::class === $to && $data instanceof Project;
    }
}