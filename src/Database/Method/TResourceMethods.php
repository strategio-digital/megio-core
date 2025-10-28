<?php
declare(strict_types=1);

namespace Megio\Database\Method;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Megio\Database\Entity\Auth\Resource;

trait TResourceMethods
{
    /**
     * @return Collection<int, Resource>
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
