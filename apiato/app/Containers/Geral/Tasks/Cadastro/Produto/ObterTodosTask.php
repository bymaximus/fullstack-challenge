<?php

namespace App\Containers\Geral\Tasks\Cadastro\Produto;

use App\Containers\Geral\Data\Repositories\Cadastro\ProdutoRepository;
use App\Ship\Parents\Tasks\Task;

class ObterTodosTask extends Task
{
    protected $repository;

    public function __construct(ProdutoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->with('subCategoria');
    }
}
