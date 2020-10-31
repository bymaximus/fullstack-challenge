<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Produto;

use App\Ship\Parents\Transporters\Transporter;

class RemovidoHistoricoObterRegistroTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
            'id_historico',
        ],
        'required' => [
            'id',
            'id_historico',
        ],
        'default' => [
        ]
    ];
}
