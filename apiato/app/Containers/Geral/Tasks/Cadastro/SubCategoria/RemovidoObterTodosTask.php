<?php

namespace App\Containers\Geral\Tasks\Cadastro\SubCategoria;

use App\Containers\Geral\Data\Repositories\Cadastro\SubCategoriaRepository;
use App\Ship\Parents\Tasks\Task;

class RemovidoObterTodosTask extends Task
{
    protected $repository;

    public function __construct(SubCategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->onlyTrashed()->with('categoria');
    }
}
