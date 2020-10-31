<?php

namespace App\Containers\Geral\Tasks\Cadastro\Categoria;

use App\Containers\Geral\Data\Repositories\Cadastro\CategoriaRepository;
use App\Ship\Parents\Tasks\Task;

class RemovidoObterTodosTask extends Task
{
    protected $repository;

    public function __construct(CategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->onlyTrashed();
    }
}
