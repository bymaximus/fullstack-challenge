<?php

namespace App\Containers\Geral\Tasks\Cadastro\Produto;

use App\Containers\Geral\Data\Repositories\Cadastro\ProdutoRepository;
use App\Containers\Geral\Models\Produto;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Intervention\Image\Facades\Image;
use Exception;

class AdicionarTask extends Task
{
    protected $repository;

    public function __construct(ProdutoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(array $data)
    {
        try {
            if ($data) {
                if (isset($data['cor'])) {
                    if (is_array($data['cor'])) {
                        $cores = $data['cor'];
                        $data['cor'] = [];
                        foreach ($cores as $cor) {
                            if (is_array($cor)) {
                                $data['cor'][] = $cor;
                            } elseif (is_string($cor)) {
                                $data['cor'][] = json_decode($cor, true);
                            }
                        }
                    }
                }
                if (isset($data['imagem'])) {
                    if ($data['imagem']) {
                        $assinatura = Image::make($data['imagem'])->encode('png');
                        $data['imagem'] = (string)($assinatura->widen(450, function ($constraint) {
                            $constraint->upsize();
                        })->encode('png'));
                        $assinatura->destroy();
                    } else {
                        $data['imagem'] = null;
                        unset($data['imagem']);
                    }
                }
            }
            Produto::flushQueryCache();
            /**
             * @var Produto
             */
            $model = $this->repository->create($data);
            if ($model) {
                if ($model->isValid()) {
                    return $model;
                } else {
                    return $model->saveOrFail();
                }
            } else {
                $modelErros = $model->getErrors();
                if ($modelErros &&
                    $modelErros instanceof \Illuminate\Support\MessageBag
                ) {
                    $modelErros = $modelErros->getMessages();
                }
                if (!$modelErros) {
                    throw new Exception('Registro não criado.');
                } else {
                    $erros = [];
                    foreach ($modelErros as $campo => $campoErros) {
                        if (is_array($campoErros)) {
                            $erros[] = $campoErros[0];
                        } else {
                            $erros[] = $campoErros;
                        }
                    }
                    if ($erros) {
                        throw new Exception('Os dados fornecidos são inválidos. Erros: ' . join(' ', $erros));
                    }
                    throw new Exception('Os dados fornecidos são inválidos.');
                }
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new CreateResourceFailedException($err->getMessage());
        }
    }
}
