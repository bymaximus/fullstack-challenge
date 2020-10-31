<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Categoria;

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
        ],
        'required' => [
            'id',
            'nome',
        ],
        'default' => [
        ]
    ];
}
