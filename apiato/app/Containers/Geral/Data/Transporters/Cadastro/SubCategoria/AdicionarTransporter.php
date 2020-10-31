<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\SubCategoria;

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
        ],
        'required' => [
            'nome',
            'id_categoria',
        ],
        'default' => [
        ]
    ];
}
