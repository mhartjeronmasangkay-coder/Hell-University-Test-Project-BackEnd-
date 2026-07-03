<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Illuminate\Http\UploadedFile;

class Upload extends ScalarType
{
    public string $name = 'Upload';

    public function serialize($value)
    {
        throw new Error('Upload scalar cannot be serialized as output.');
    }

    public function parseValue($value)
    {
        if (! $value instanceof UploadedFile) {
            throw new Error('Upload scalar must be an uploaded file.');
        }

        return $value;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        throw new Error('Upload scalar cannot be used as a literal.');
    }
}