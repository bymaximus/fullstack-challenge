<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Shop;

use App\Containers\Geral\Models\Produto;
use App\Ship\Parents\Transformers\Transformer;
use Illuminate\Support\Arr;

class ProdutoTransformer extends Transformer
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
     * @param Produto $entity
     * @return array
     */
    public function transform(Produto $entity)
    {
        $cores = [];
        $corLista = $entity->cor_decodificado;
        if ($corLista) {
            foreach ($corLista as $cor) {
                if ($cor &&
                    Arr::has($cor, 'hex') &&
                    $cor['hex']
                ) {
                    $cores[] = $cor['hex'];
                }
            }
        }
        return [
            'objectID' => $entity->id,
            'id_categoria' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria ? $entity->subCategoria->id_categoria : null),
            'id_subcategoria' => $entity->id_subcategoria,
            'name' => $entity->nome,
            'description' => '',
            'price' => (float)$entity->preco,
            'categories' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria && $entity->subCategoria->categoria ? [$entity->subCategoria->categoria->nome] : []),
            'hierarchicalCategories' => [
                'lvl0' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria && $entity->subCategoria->categoria ? $entity->subCategoria->categoria->nome : ''),
                'lvl1' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria && $entity->subCategoria->categoria ? $entity->subCategoria->categoria->nome.(($entity->id_subcategoria && $entity->subCategoria ? ' > '.$entity->subCategoria->nome : '')) : ''),
            ],
            'type' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria && $entity->subCategoria->categoria ? $entity->subCategoria->categoria->nome : ''),
            'cor' => $cores,
            'quantity' => 1,
            'image' => $entity->imagem_data_url,
        ];
    }
}
