<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\FieldBuilder\FieldBuilder;
use Megio\Collection\FieldBuilder\Field\EmailField;
use Megio\Collection\FieldBuilder\Field\PasswordField;
use Megio\Collection\FieldBuilder\Rule\RequiredRule;
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
    
    public function create(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password', [new RequiredRule()]));
    }
    
    public function update(FieldBuilder $builder): FieldBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password'));
    }
}