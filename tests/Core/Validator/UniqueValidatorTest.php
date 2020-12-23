<?php

namespace App\Tests\Core\Validator;

use App\Core\Validator\Unique;
use App\Core\Validator\UniqueValidator;
use App\Tests\ValidatorTestCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class UniqueValidatorTest extends ValidatorTestCase
{

    public function dataProvider(): iterable
    {
        $obj = new FakeObjectWithSlug('my-slug');
        $obj1 = new FakeObjectWithSlug('my-slug', 1);
        $obj2 = new FakeObjectWithSlug('my-slug', 2);
        yield [$obj, null, false];
        yield [$obj1, $obj1, false];
        yield [$obj1, $obj2, true];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testUniqueValidator(
        FakeObjectWithSlug $value,
        ?FakeObjectWithSlug $repositoryResult,
        $expectedViolation
    ): void
    {
        $repository = $this->getMockBuilder(ServiceEntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $value->slug])
            ->willReturn($repositoryResult);
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->with('Demo')
            ->willReturn($repository);
        $existValidator = new UniqueValidator($em);
        $context = $this->getContext($expectedViolation);
        $existValidator->initialize($context);
        $existValidator->validate($value, new Unique(['entityClass' => 'Demo', 'field' => 'slug']));
    }

}
