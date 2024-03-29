<?php

namespace App\Http\Admin\Data;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @method hydrate(object $entity, EntityManagerInterface $em)
 */
interface CrudDataInterface
{

    public function getEntity(): object;

    public function getFormClass(): string;

}
