<?php
namespace simplygoodwork\donkeytail\gql;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

use simplygoodwork\donkeytail\gql\PinType;
use craft\gql\arguments\elements\Asset as AssetArguments;
use craft\gql\interfaces\elements\Asset as AssetInterface;

// use craft\gql\types\elements\Element;
// class DonkeytailType extends Element
class DonkeytailType
{
    use GqlTypeTrait;

    public static function getName(): string
    {
        return 'Donkeytail';
    }

    public static function getFieldDefinitions(): array
    {
        return [
            'canvas' => [
                'name' => 'canvas',
                'type' => AssetInterface::getType(),
                'args' => AssetArguments::getArguments(),
                'description' => 'The canvas asset.',
            ],
            'pins' => [
                'name' => 'pins',
                'type' => Type::listOf(PinType::getType()),
            ]
        ];
    }
}