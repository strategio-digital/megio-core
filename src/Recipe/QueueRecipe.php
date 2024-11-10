<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Formatter\CallableFormatter;
use Megio\Collection\WriteBuilder\Field\EnumField;
use Megio\Collection\WriteBuilder\Field\TextAreaField;
use Megio\Collection\WriteBuilder\Rule\JsonStringRule;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Serializer\CallableSerializer;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Database\Entity\Queue;
use Megio\Queue\QueueStatus;
use Megio\Queue\QueueWorker;

class QueueRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Queue::class;
    }
    
    public function key(): string
    {
        return 'queue';
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $this->commonBuilder($builder);
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $this->commonBuilder($builder);
    }
    
    private function commonBuilder(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(persist: true)
            ->add(new EnumField('status', 'status', QueueStatus::class))
            ->add(new EnumField('worker', 'worker', QueueWorker::class, attrs: ['fullWidth' => true]))
            ->add(new TextAreaField(
                name: 'payload',
                label: 'payload',
                rules: [
                    new JsonStringRule()
                ],
                serializers: [
                    new CallableSerializer(fn($payload) => json_decode($payload, true))
                ],
                formatters: [
                    new CallableFormatter(fn($payload) => json_encode($payload, JSON_PRETTY_PRINT))
                ],
                attrs: [
                    'fullWidth' => true,
                ]
            ))
            ->add(new TextAreaField(
                name: 'errorMessage',
                label: 'error message',
                rules: [
                    new NullableRule()
                ],
                attrs: ['fullWidth' => true]
            ));
    }
}