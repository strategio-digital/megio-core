<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\ReadBuilder\ReadBuilder;
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
    
    public function key(): string
    {
        return 'admin';
    }
    
    public function read(ReadBuilder $builder): ReadBuilder
    {
        return $builder->buildByDbSchema(['password']);
    }
    
    public function readAll(ReadBuilder $builder): ReadBuilder
    {
        return $builder->buildByDbSchema(['password']);
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