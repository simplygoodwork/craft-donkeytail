<?php
namespace simplygoodwork\donkeytail\gql;

use craft\gql\GqlEntityRegistry;
use craft\gql\base\GqlTypeTrait;

use craft\gql\base\ObjectType;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\Type;

use craft\gql\arguments\elements\Entry as EntryArguments;
use craft\gql\interfaces\elements\Entry as EntryInterface;
// use craft\gql\resolvers\elements\Entry as EntryResolver;


class PinType extends ObjectType
{
    use GqlTypeTrait;

    public static function getName(): string
    {
        return 'Pin';
    }

    /**
     * @inheritdoc
     */
    public static function getType(): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new self([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'Pin on a Donkeytail Canvas',
        ]));

        return $type;
    }

    public static function getFieldDefinitions(): array
    {
        return TypeManager::prepareFieldDefinitions([
            'x' => [
                'name' => 'x',
                'type' => Type::string(),
                'description' => 'The x percentage of pin',
            ],
            'y' => [
                'name' => 'y',
                'type' => Type::string(),
                'description' => 'The y percentage of pin',
            ],
            'entry' => [
                'name' => 'entry',
                'description' => 'Description of the sale.',
                'type' => EntryInterface::getType(),
                'args' => EntryArguments::getArguments(),
                // 'resolve' => EntryResolver::class . '::resolve',
            ],
        ], self::getName());
    }
}