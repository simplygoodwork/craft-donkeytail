<?php
namespace simplygoodwork\donkeytail\gql;

use craft\gql\GqlEntityRegistry;
use craft\gql\base\GqlTypeTrait;

use craft\gql\base\ObjectType;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\Type;

use craft\gql\arguments\elements\Entry as EntryArguments;
use craft\gql\interfaces\elements\Entry as EntryInterface;

use craft\gql\arguments\elements\Asset as AssetArguments;
use craft\gql\interfaces\elements\Asset as AssetInterface;

use craft\gql\arguments\elements\Category as CategoryArguments;
use craft\gql\interfaces\elements\Category as CategoryInterface;

use craft\gql\types\elements\Element as ElementType;
use craft\gql\interfaces\Element as ElementInterface;
use craft\gql\types\generators\ElementType as GeneratorsElementType;

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
                'description' => 'Pin\'s entry element',
                'type' => EntryInterface::getType(),
                'args' => EntryArguments::getArguments(),
            ],
            'asset' => [
                'name' => 'asset',
                'description' => 'Pin\'s asset element',
                'type' => AssetInterface::getType(),
                'args' => AssetArguments::getArguments(),
            ],
            'category' => [
                'name' => 'category',
                'description' => 'Pin\'s category element',
                'type' => CategoryInterface::getType(),
                'args' => CategoryArguments::getArguments(),
            ],
        ], self::getName());
    }
}