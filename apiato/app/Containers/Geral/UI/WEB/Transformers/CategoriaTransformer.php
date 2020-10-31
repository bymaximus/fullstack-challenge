<?php

namespace App\Containers\Geral\UI\WEB\Transformers;

use App\Containers\Geral\Models\Categoria;
use App\Ship\Parents\Transformers\Transformer;

class CategoriaTransformer extends Transformer
{
    /**
     * @var  array
     */
    protected $defaultIncludes = [
    ];

    /**
     * @var  array
     */
    protected $availableIncludes = [
    ];

    /**
     * @param Categoria $entity
     * @return array
     */
    public function transform(Categoria $entity)
    {
        return $entity->toArray();
    }
}
