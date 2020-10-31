<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\SubCategoria;

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
        ],
        'required' => [
            'id',
            'nome',
            'id_categoria',
        ],
        'default' => [
        ]
    ];
}
