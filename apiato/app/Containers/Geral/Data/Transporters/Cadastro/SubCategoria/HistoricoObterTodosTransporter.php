<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\SubCategoria;

use App\Ship\Parents\Transporters\Transporter;

class HistoricoObterTodosTransporter extends Transporter
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
        ]
    ];
}