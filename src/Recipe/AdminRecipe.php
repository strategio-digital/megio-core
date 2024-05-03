<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\Column\EmailColumn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\SearchBuilder\Searchable;
use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Collection\WriteBuilder\Field\Base\EmptyValue;
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
    
    public function search(SearchBuilder $builder, CollectionRequest $request): SearchBuilder
    {
        $builder
            ->keepDefaults()
            ->addSearchable(new Searchable('email'))
            ->addSearchable(new Searchable('lastLogin'));
        
        return $builder;
    }
    
    public function read(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(['password']);
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(['password'], persist: true)
            ->add(new EmailColumn('email', 'E-mail', true));
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder
            ->add(new EmailField('email', 'E-mail', [new RequiredRule()]))
            ->add(new PasswordField('password', 'Password', [new RequiredRule()]));
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
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