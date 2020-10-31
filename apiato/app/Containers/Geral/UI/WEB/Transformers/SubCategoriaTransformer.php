<?php

namespace App\Containers\Geral\UI\WEB\Transformers;

use App\Containers\Geral\Models\SubCategoria;
use App\Ship\Parents\Transformers\Transformer;

class SubCategoriaTransformer extends Transformer
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
     * @param SubCategoria $entity
     * @return array
     */
    public function transform(SubCategoria $entity)
    {
        return $entity->toArray();
    }
}
