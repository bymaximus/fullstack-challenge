<?php

namespace App\Containers\Geral\UI\WEB\Controllers\Cadastro;

use App\Containers\Geral\UI\WEB\Controllers\CommonController;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\ObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\RemoverRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\HistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\HistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\RemovidoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\RemovidoHistoricoObterTodosRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\RemovidoHistoricoObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\SubCategoria\ObterListasRequest;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\SubCategoriaTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Cadastro\SubCategoriaHistoricoTransformer;
use App\Containers\Geral\UI\WEB\Transformers\CategoriaTransformer;
use Apiato\Core\Foundation\Facades\Apiato;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Containers\Geral\Exceptions\AuthenticationException;
use Apiato\Core\Traits\ResponseTrait;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Exception;

/**
 * Class SubCategoriaController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers\Cadastro
 */
class SubCategoriaController extends CommonController
{
    use ResponseTrait;

    /**
     * Show all entities
     *
     * @param ObterTodosRequest $request
     */
    public function index(ObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $categoriaFiltrado = false;
        return Apiato::call('Geral@Cadastro\SubCategoria\ObterTodosAction', [$request])->filterColumn('id_categoria', function ($query, $keyword) use (&$categoriaFiltrado) {
            if (request()->has('columns.2.search.value')) {
                $requestKeyword = request()->input('columns.2.search.value');
                if ($requestKeyword &&
                    is_numeric($requestKeyword) &&
                    $requestKeyword == $keyword &&
                    !$categoriaFiltrado
                ) {
                    $categoriaFiltrado = true;
                    $query->where('id_categoria', '=', $keyword);
                }
            }
        })->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->addColumn('categoria.nome', function ($registro) {
            return $registro->id_categoria && $registro->categoria ? $registro->categoria->nome : '';
        })->addColumn('categoria.removido', function ($registro) {
            return $registro->id_categoria && $registro->categoria && $registro->categoria->trashed() ? true : false;
        })->blacklist(['dt_criacao', 'dt_alteracao', 'categoria.nome', 'categoria.removido'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param HistoricoObterTodosRequest $request
     */
    public function indexHistorico(HistoricoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        return Apiato::call('Geral@Cadastro\SubCategoria\HistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->blacklist(['created_at', 'event'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param RemovidoObterTodosRequest $request
     */
    public function indexRemovido(RemovidoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        $categoriaFiltrado = false;
        return Apiato::call('Geral@Cadastro\SubCategoria\RemovidoObterTodosAction', [$request])->filterColumn('id_categoria', function ($query, $keyword) use (&$categoriaFiltrado) {
            if (request()->has('columns.2.search.value')) {
                $requestKeyword = request()->input('columns.2.search.value');
                if ($requestKeyword &&
                    is_numeric($requestKeyword) &&
                    $requestKeyword == $keyword &&
                    !$categoriaFiltrado
                ) {
                    $categoriaFiltrado = true;
                    $query->where('id_categoria', '=', $keyword);
                }
            }
        })->editColumn('dt_criacao', function ($registro) {
            return $registro->dt_criacao ? with(new Carbon($registro->dt_criacao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_alteracao', function ($registro) {
            return $registro->dt_alteracao ? with(new Carbon($registro->dt_alteracao))->format('d/m/Y H:i:s') : '';
        })->editColumn('dt_remocao', function ($registro) {
            return $registro->dt_remocao ? with(new Carbon($registro->dt_remocao))->format('d/m/Y H:i:s') : '';
        })->addColumn('categoria.nome', function ($registro) {
            return $registro->id_categoria && $registro->categoria ? $registro->categoria->nome : '';
        })->addColumn('categoria.removido', function ($registro) {
            return $registro->id_categoria && $registro->categoria && $registro->categoria->trashed() ? true : false;
        })->blacklist(['dt_criacao', 'dt_alteracao', 'dt_remocao', 'categoria.nome', 'categoria.removido'])->make(true);
    }

    /**
     * Show all entities
     *
     * @param RemovidoHistoricoObterTodosRequest $request
     */
    public function indexRemovidoHistorico(RemovidoHistoricoObterTodosRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        return Apiato::call('Geral@Cadastro\SubCategoria\RemovidoHistoricoObterTodosAction', [$request])->filterColumn('user.usuario', function ($query, $keyword) {
        })->editColumn('created_at', function ($registro) {
            return $registro->created_at ? with(new Carbon($registro->created_at))->format('d/m/Y H:i:s') : '';
        })->editColumn('event', function ($registro) {
            return $registro->evento;
        })->addColumn('user.usuario', function ($registro) {
            return $registro->user ? $registro->user->usuario : '';
        })->blacklist(['created_at', 'event'])->make(true);
    }

    /**
     * Delete
     *
     * @param RemoverRequest $request
     */
    public function remover(RemoverRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\RemoverAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro removido com sucesso.',
                'status' => 'removido',
                'id' => $model->id
            ], 200);
        } else {
            throw new DeleteResourceFailedException('Registro não encontrado.', null, 500);
        }
    }

    /**
     * Add new record
     *
     * @param AdicionarRequest $request
     */
    public function adicionar(AdicionarRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\AdicionarAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro adicionado com sucesso.',
                'status' => 'criado',
                'id' => $model->id
            ], 201);
        } else {
            throw new CreateResourceFailedException('Registro não criado.', null, 500);
        }
    }

    /**
     * Show record
     *
     * @param ObterRegistroRequest $request
     */
    public function obterRegistro(ObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];
            $lists['categorias'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $registros = Apiato::call('Geral@Cadastro\Categoria\ObterListaAction', [$request]);
            if ($registros) {
                $resource = new Collection($registros, new CategoriaTransformer);
                $lists['categorias'] = current($manager->createData($resource)->toArray());
            }

            $resource = new Item($model, new SubCategoriaTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Show record
     *
     * @param HistoricoObterRegistroRequest $request
     */
    public function obterHistoricoRegistro(HistoricoObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new SubCategoriaHistoricoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Show record
     *
     * @param RemovidoHistoricoObterRegistroRequest $request
     */
    public function obterRemovidoHistoricoRegistro(RemovidoHistoricoObterRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\HistoricoObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            $resource = new Item($model, new SubCategoriaHistoricoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return $this->json($lists);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Update
     *
     * @param AtualizarRegistroRequest $request
     */
    public function atualizar(AtualizarRegistroRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $model = Apiato::call('Geral@Cadastro\SubCategoria\AtualizarRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            return response()->json([
                'message' => 'Registro atualizado com sucesso.',
                'status' => 'atualizado',
                'id' => $model->id
            ], 200);
        } else {
            throw new UpdateResourceFailedException('Registro não encontrado.', null, 500);
        }
    }


    /**
     * Show record
     *
     * @param ObterListasRequest $request
     */
    public function obterListas(ObterListasRequest $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $lists = [];
        $lists['categorias'] = [];

        $registros = Apiato::call('Geral@Cadastro\Categoria\ObterListaAction', [$request]);
        if ($registros) {
            $resource = new Collection($registros, new CategoriaTransformer);
            $lists['categorias'] = current($manager->createData($resource)->toArray());
        }

        return $this->json($lists);
    }
}
