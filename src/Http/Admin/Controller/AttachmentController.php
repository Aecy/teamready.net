<?php

namespace App\Http\Admin\Controller;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentGenerator;
use App\Domain\Attachment\Repository\AttachmentRepository;
use App\Domain\Attachment\Validator\AttachmentPathValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentController extends BaseController
{

    private ValidatorInterface $validator;
    private AttachmentGenerator $attachmentGenerator;

    public function __construct(
        ValidatorInterface $validator,
        AttachmentGenerator $attachmentGenerator
    )
    {
        $this->validator = $validator;
        $this->attachmentGenerator = $attachmentGenerator;
    }

    public function validateRequest(Request $request): array
    {
        $errors = $this->validator->validate($request->files->get('file'), [
            new Image()
        ]);
        if ($errors->count() === 0) {
            return [true, null];
        }
        return [false, $this->json(['error' => $errors->get(0)->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY)];
    }

    /**
     * @Route("/attachment/folders", name="attachment_folders")
     */
    public function folders(AttachmentRepository $repository): JsonResponse
    {
        return $this->json($repository->findByDate());
    }

    /**
     * @Route("/attachment/files", name="attachment_files", requirements={"path"="tr"})
     */
    public function files(AttachmentRepository $repository, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $path = $request->get('path');
        if ($path === null) {
            $attachments = $repository->findLatest();
        } else {
            if (! AttachmentPathValidator::validate($path)) {
                return $this->json(['error' => 'Invalid path'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $attachments = $repository->findByPath($request->get('path'));
        }
        return $this->json($attachments);
    }

    /**
     * @Route("/attachment/{attachment}", name="attachment_show", methods={"POST"})
     */
    public function update(Attachment $attachment, Request $request, EntityManagerInterface $em): JsonResponse
    {
        [$valid, $response] = $this->validateRequest($request);
        if (! $valid) {
            return $response;
        }
        $attachment->setFile($request->files->get('file'));
        $attachment->setCreatedAt(new \DateTime());
        $em->flush();
        return $this->json([
            'id' => $attachment->getId(),
            'url' => $this->attachmentGenerator->generate($attachment)
        ]);
    }

    public function delete(Attachment $attachment): JsonResponse
    {
        //todo: delete
        return $this->json();
    }

}
