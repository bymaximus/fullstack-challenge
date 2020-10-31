<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Produto;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'nome',
            'id_categoria',
            'id_subcategoria',
            'preco',
            'cor',
            'imagem',
        ],
        'required' => [
            'nome',
            'id_categoria',
            'id_subcategoria',
            'preco',
        ],
        'default' => [
        ]
    ];
}
