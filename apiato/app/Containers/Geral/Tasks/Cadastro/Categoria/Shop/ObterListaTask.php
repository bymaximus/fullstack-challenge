<?php

namespace App\Containers\Geral\Tasks\Cadastro\Categoria\Shop;

use App\Containers\Geral\Data\Repositories\Cadastro\CategoriaRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaTask extends Task
{
    protected $repository;

    public function __construct(CategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->shop()->select('id', 'nome')->oldest('nome')->get();
    }
}
