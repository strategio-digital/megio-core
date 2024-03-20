<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
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
    
    public function showOne(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function showAll(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function create(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password', [new RequiredRule()]));
    }
    
    public function update(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password'));
    }
}