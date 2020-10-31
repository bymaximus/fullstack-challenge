<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Produto;

use App\Ship\Parents\Transporters\Transporter;

class AtualizarRegistroTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
            'nome',
            'id_categoria',
            'id_subcategoria',
            'preco',
            'cor',
            'imagem',
            'imagem_removida',
        ],
        'required' => [
            'id',
            'nome',
            'id_categoria',
            'id_subcategoria',
            'preco',
        ],
        'default' => [
        ]
    ];
}
