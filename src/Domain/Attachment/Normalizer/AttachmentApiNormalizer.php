<?php

namespace App\Domain\Attachment\Normalizer;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentGenerator;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttachmentApiNormalizer implements NormalizerInterface
{

    private AttachmentGenerator $attachmentGenerator;

    public function __construct(AttachmentGenerator $attachmentGenerator)
    {
        $this->attachmentGenerator = $attachmentGenerator;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Attachment $object */
        return [
            'id' => $object->getId(),
            'name' => $object->getFileName(),
            'size' => $object->getFileSize(),
            'url' => $this->attachmentGenerator->generate($object),
            'createdAt' => $object->getCreatedAt()->getTimestamp()
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Attachment && 'json' === $format;
    }

}
