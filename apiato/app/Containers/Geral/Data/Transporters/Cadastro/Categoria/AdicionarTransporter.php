<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Categoria;

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
        ],
        'required' => [
            'nome',
        ],
        'default' => [
        ]
    ];
}
