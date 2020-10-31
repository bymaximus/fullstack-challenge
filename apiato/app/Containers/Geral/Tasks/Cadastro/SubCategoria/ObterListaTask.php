<?php

namespace App\Containers\Geral\Tasks\Cadastro\SubCategoria;

use App\Containers\Geral\Data\Repositories\Cadastro\SubCategoriaRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaTask extends Task
{
    protected $repository;

    public function __construct(SubCategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->with('categoria')->select('id', 'id_categoria', 'nome')->oldest('nome')->get();
    }
}
