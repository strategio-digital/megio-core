<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\Builder\Builder;
use Megio\Collection\Builder\Field\Email;
use Megio\Collection\Builder\Field\Password;
use Megio\Collection\Builder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Database\Entity\Admin;

class AdminRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Admin::class;
    }
    
    public function name(): string
    {
        return 'admin';
    }
    
    public function readOne(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function readAll(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function create(Builder $builder): Builder
    {
        return $builder
            ->add(new Email('email', 'E-mail', [new RequiredRule()]))
            ->add(new Password('password', 'Password', [new RequiredRule()]));
    }
    
    public function update(Builder $builder): Builder
    {
        return $builder
            ->add(new Email('email', 'E-mail', [new RequiredRule()]))
            ->add(new Password('password', 'Password'));
    }
}