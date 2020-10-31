<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Produto;

use App\Ship\Parents\Transporters\Transporter;

class ObterSubCategoriasTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
        ],
        'required' => [
            'id',
        ],
        'default' => [
            // provide default values for specific properties here
        ]
    ];
}
