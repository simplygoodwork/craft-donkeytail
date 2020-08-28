<?php
namespace simplygoodwork\donkeytail\gql;

use Craft;
use craft\gql\GqlEntityRegistry;
use craft\gql\base\GqlTypeTrait;

use craft\gql\base\ObjectType;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\Type;

use craft\gql\arguments\elements\Entry as EntryArguments;
use craft\gql\interfaces\elements\Entry as EntryInterface;

use craft\gql\arguments\elements\Asset as AssetArguments;
use craft\gql\interfaces\elements\Asset as AssetInterface;

use craft\gql\arguments\elements\User as UserArguments;
use craft\gql\interfaces\elements\User as UserInterface;

use craft\gql\arguments\elements\Category as CategoryArguments;
use craft\gql\interfaces\elements\Category as CategoryInterface;

use craft\commerce\gql\arguments\elements\Product as ProductArguments;
use craft\commerce\gql\interfaces\elements\Product as ProductInterface;

use craft\commerce\gql\arguments\elements\Variant as VariantArguments;
use craft\commerce\gql\interfaces\elements\Variant as VariantInterface;

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
        $fieldDefinitions = [
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
            'user' => [
                'name' => 'user',
                'description' => 'Pin\'s user element',
                'type' => UserInterface::getType(),
                'args' => UserArguments::getArguments(),
            ],
            'category' => [
                'name' => 'category',
                'description' => 'Pin\'s category element',
                'type' => CategoryInterface::getType(),
                'args' => CategoryArguments::getArguments(),
            ]
        ];

        if (Craft::$app->plugins->isPluginEnabled('commerce')) {
            $fieldDefinitions = array_merge($fieldDefinitions, [
                'product' => [
                    'name' => 'product',
                    'description' => 'Pin\'s product element',
                    'type' => ProductInterface::getType(),
                    'args' => ProductArguments::getArguments(),
                ],
                'variant' => [
                    'name' => 'variant',
                    'description' => 'Pin\'s product variant element',
                    'type' => VariantInterface::getType(),
                    'args' => VariantArguments::getArguments(),
                ]
            ]);
        }

        return TypeManager::prepareFieldDefinitions($fieldDefinitions, self::getName());
    }
}