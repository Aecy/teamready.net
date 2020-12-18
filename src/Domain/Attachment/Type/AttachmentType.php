<?php

namespace App\Domain\Attachment\Type;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentGenerator;
use App\Domain\Attachment\Validator\AttachmentExist;
use App\Domain\Attachment\Validator\AttachmentNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttachmentType extends TextType implements DataTransformerInterface
{

    private EntityManagerInterface $em;
    private AttachmentGenerator $generator;

    public function __construct(
        EntityManagerInterface $em,
        AttachmentGenerator $generator
    )
    {
        $this->em = $em;
        $this->generator = $generator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-avatar'] = $this->generator->generate($form->getData());
        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'is' => 'input-attachment',
            ],
            'constraints' => [
                new AttachmentExist()
            ]
        ]);
        parent::configureOptions($resolver);
    }

    /**
     * @param ?Attachment $attachment
     */
    public function transform($attachment): ?int
    {
        if ($attachment instanceof Attachment) {
            return $attachment->getId();
        }
        return null;
    }

    /**
     * @param int $value
     */
    public function reverseTransform($value): ?Attachment
    {
        if (empty($value)) {
            return null;
        }
        return $this->em->getRepository(Attachment::class)->find($value) ?: new AttachmentNotFound($value);
    }
}
