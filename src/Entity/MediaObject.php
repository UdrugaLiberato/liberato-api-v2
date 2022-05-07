<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateMediaObjectAction;
use App\DTO\Media\MediaOutput;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'controller' => CreateMediaObjectAction::class,
            'deserialize' => false,
            'validation_groups' => ['Default', 'media_object_create'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'array',
                                        'items' => [
                                            'format' => 'binary',
                                            'type' => 'string'
                                        ]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    iri: 'http://schema.org/MediaObject',
    itemOperations: ['get'],
    normalizationContext: ['groups' => ['media_object:read']],
    output: MediaOutput::class
)]
class MediaObject
{
    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="filePath")
     */
    #[Groups(['media_object_create'])]
    public ?File $file = null;
    #[
        ORM\Column(nullable: true),
    ]
    public ?string $filePath = null;
    #[Groups(['media_object:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}