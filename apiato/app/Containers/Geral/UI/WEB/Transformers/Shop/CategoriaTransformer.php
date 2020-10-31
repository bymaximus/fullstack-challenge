<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Shop;

use Illuminate\Database\Eloquent\Collection;
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
     * @param Collection $entity
     * @return array
     */
    public function transform(Collection $rows)
    {
        $categorias = [];
        if ($rows) {
            foreach ($rows as $row) {
                $categorias[$row->nome] = $row->id;
            }
        }
        return $categorias;
    }
}
