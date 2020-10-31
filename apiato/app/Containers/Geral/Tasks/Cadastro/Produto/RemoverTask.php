<?php

namespace App\Containers\Geral\Tasks\Cadastro\Produto;

use App\Containers\Geral\Data\Repositories\Cadastro\ProdutoRepository;
use App\Containers\Geral\Models\Produto;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
    protected $repository;

    public function __construct(ProdutoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id)
    {
        try {
            Produto::flushQueryCache();
            /**
             * @var Produto
             */
            $model = $this->repository->find($id);
            if ($model) {
                if ($model->delete()) {
                    return $model;
                } else {
                    throw new Exception('Erro ao remover registro.');
                }
            } else {
                throw new Exception('Registro não encontrado.');
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new DeleteResourceFailedException($err->getMessage());
        }
    }
}
