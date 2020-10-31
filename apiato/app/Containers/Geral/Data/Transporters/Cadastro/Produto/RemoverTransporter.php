<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Produto;

use App\Ship\Parents\Transporters\Transporter;

class RemoverTransporter extends Transporter
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
            'id'
        ],
        'default' => [
        ]
    ];
}
