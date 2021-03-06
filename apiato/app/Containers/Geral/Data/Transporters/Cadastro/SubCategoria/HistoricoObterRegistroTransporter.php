<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\SubCategoria;

use App\Ship\Parents\Transporters\Transporter;

class HistoricoObterRegistroTransporter extends Transporter
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
