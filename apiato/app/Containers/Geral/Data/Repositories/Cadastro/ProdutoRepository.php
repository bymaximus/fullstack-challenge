<?php

namespace App\Containers\Geral\Data\Repositories\Cadastro;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class ProdutoRepository
 */
class ProdutoRepository extends Repository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
    ];
}
