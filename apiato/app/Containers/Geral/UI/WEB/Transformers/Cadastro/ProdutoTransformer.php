<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

use App\Containers\Geral\Models\Produto;
use App\Ship\Parents\Transformers\Transformer;

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
        return [
            'id_categoria' => ($entity->id_subcategoria && $entity->subCategoria && $entity->subCategoria->id_categoria ? $entity->subCategoria->id_categoria : null),
            'id_subcategoria' => $entity->id_subcategoria,
            'nome' => $entity->nome,
            'preco' => (float)$entity->preco,
            'cor' => $entity->cor_decodificado,
            'imagem' => $entity->imagem_data_url,
        ];
    }
}
