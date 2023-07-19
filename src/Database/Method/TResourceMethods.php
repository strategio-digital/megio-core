<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Method;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Saas\Database\Entity\Auth\Role;

trait TResourceMethods
{
    /**
     * @return Collection<int, Role>
     */
    public function getResources(): Collection
    {
        $resources = new ArrayCollection();
        
        foreach ($this->getRoles() as $role) {
            foreach ($role->getResources() as $resource) {
                if ($resources->contains($resource) === false) {
                    $resources->add($resource);
                }
            }
        }
        
        return $resources;
    }
}