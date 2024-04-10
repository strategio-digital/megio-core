<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\RecipeRequest;
use Megio\Collection\WriteBuilder\Field\Base\EmptyValue;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
use Megio\Collection\CollectionRecipe;
use Megio\Database\Entity\Admin;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function read(ReadBuilder $builder, RecipeRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(['password']);
    }
    
    public function readAll(ReadBuilder $builder, RecipeRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(['password']);
    }
    
    public function create(WriteBuilder $builder, RecipeRequest $request): WriteBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password', [new RequiredRule()]));
    }
    
    public function update(WriteBuilder $builder, RecipeRequest $request): WriteBuilder
    {
        $pwf = new PasswordField(name: 'password', label: 'Heslo');
        
        // Do not show password on form rendering
        if ($request->isFormRendering()) {
            $pwf->setValue(new EmptyValue());
        }
        
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add($pwf);
    }
}