<?php

namespace App\Containers\Geral\UI\WEB\Controllers;

use App\Containers\Geral\UI\WEB\Transformers\Shop\CategoriaTransformer;
use App\Containers\Geral\UI\WEB\Transformers\Shop\ProdutoTransformer;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Produto\Shop\ObterRegistroRequest;
use App\Containers\Geral\Helpers\Helpers;
use App\Containers\Geral\Models\Produto;
use App\Containers\Geral\Models\Categoria;
use App\Containers\Geral\Models\SubCategoria;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Illuminate\Http\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\Log;
use App\Ship\Exceptions\NotFoundException;

/**
 * Class ShopController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers
 */
class ShopController extends CommonController
{
    public function shopPage(Request $request)
    {
        return view('geral::shop');
    }


    public function search(Request $request)
    {
        $requestData = $request->all();
        if ($requestData &&
            is_array($requestData)
        ) {
            $results = [];
            foreach ($requestData as $rowRequest) {
                if ($rowRequest &&
                    is_array($rowRequest) &&
                    isset($rowRequest['indexName']) &&
                    $rowRequest['indexName'] == 'instant_search' &&
                    isset($rowRequest['params']) &&
                    $rowRequest['params'] &&
                    is_array($rowRequest['params'])
                ) {
                    $query = null;
                    $facets = [];
                    $pagina = 0;
                    $totalPaginas = 0;
                    $totalProdutos = 0;
                    $listaProdutos = [];
                    $produtos = null;
                    $produtosQuery = null;
                    $categoria = null;
                    $categorias = null;
                    $subCategoria = null;
                    $subCategorias = null;
                    $pegaCategoria = false;
                    $pegaSubCategoria = false;
                    $limit = 9;

                    if (isset($rowRequest['params']['hitsPerPage']) &&
                        $rowRequest['params']['hitsPerPage']
                    ) {
                        $limit = $rowRequest['params']['hitsPerPage'];
                    }
                    if (isset($rowRequest['params']['page']) &&
                        $rowRequest['params']['page']
                    ) {
                        $pagina = $rowRequest['params']['page'];
                    }
                    if (isset($rowRequest['params']['facets']) &&
                        $rowRequest['params']['facets'] &&
                        is_array($rowRequest['params']['facets'])
                    ) {
                        foreach ($rowRequest['params']['facets'] as $facet) {
                            if ($facet == 'hierarchicalCategories.lvl1') {
                                $pegaSubCategoria = true;
                            } elseif ($facet == 'hierarchicalCategories.lvl0') {
                                $pegaCategoria = true;
                            }
                        }
                    }

                    if ($pegaCategoria) {
                        $categorias = Apiato::call('Geral@Cadastro\Categoria\Shop\ObterListaAction', []);
                    }
                    if (isset($rowRequest['params']['facetFilters']) &&
                        $rowRequest['params']['facetFilters'] &&
                        is_array($rowRequest['params']['facetFilters'])
                    ) {
                        foreach ($rowRequest['params']['facetFilters'] as $filter) {
                            if (is_array($filter) &&
                                $filter[0]
                            ) {
                                if (Helpers::string()->startsWith($filter[0], 'hierarchicalCategories.lvl0:')) {
                                    $textoCategoria = substr($filter[0], strpos($filter[0], ':') + 1);
                                    if ($textoCategoria) {
                                        if ($categorias) {
                                            $categoria = $categorias->where('nome', '=', $textoCategoria)->first();
                                        } else {
                                            $categoria = Categoria::withoutTrashed()->where('nome', '=', $textoCategoria)->first();
                                        }
                                    } else {
                                        $categoria = null;
                                    }
                                    Log::info('CAT '.$categoria);
                                } elseif (Helpers::string()->startsWith($filter[0], 'hierarchicalCategories.lvl1:')) {
                                    $textoSubCategoria = substr($filter[0], strpos($filter[0], ':') + 1);
                                    if ($textoSubCategoria) {
                                        $subCategoria = SubCategoria::withoutTrashed()->where('nome', '=', $textoSubCategoria)->first();
                                    } else {
                                        $subCategoria = null;
                                    }
                                }
                            }
                        }
                    }

                    if ($pegaCategoria &&
                        $categorias
                    ) {
                        $facets['hierarchicalCategories.lvl0'] = [];
                        foreach ($categorias as $row) {
                            if ($row &&
                                ! $row->trashed() &&
                                $row->nome
                            ) {
                                $facets['hierarchicalCategories.lvl0'][$row->nome] = $row->id;
                            }
                        }
                    }
                    if ($pegaSubCategoria) {
                        if (! $categoria) {
                            $subCategorias = Apiato::call('Geral@Cadastro\SubCategoria\Shop\ObterListaAction', []);
                        } else {
                            $subCategorias = $categoria->subCategoria;
                        }
                        if ($subCategorias) {
                            $facets['hierarchicalCategories.lvl1'] = [];
                            foreach ($subCategorias as $row) {
                                if ($row &&
                                    ! $row->trashed() &&
                                    $row->nome
                                ) {
                                    $facets['hierarchicalCategories.lvl1'][$row->nome] = $row->id;
                                }
                            }
                        }
                    }
                    if (isset($rowRequest['params']['query']) &&
                        $rowRequest['params']['query'] &&
                        Helpers::string()->trim($rowRequest['params']['query'])
                    ) {
                        $query = Helpers::string()->trim($rowRequest['params']['query']);
                    }

                    if ($subCategoria &&
                        ! $subCategoria->trashed()
                    ) {
                        $produtosQuery = Produto::withoutTrashed()->where('id_subcategoria', '=', $subCategoria->id);
                    } elseif ($categoria &&
                        ! $categoria->trashed()
                    ) {
                        $produtosQuery = Produto::withoutTrashed()->has('subCategoria', '>=', 1, 'and', function ($query) use ($categoria) {
                            $query->where('id_categoria', '=', $categoria->id)
                                ->whereNull('dt_remocao');
                        });
                    } else {
                        $produtosQuery = Produto::withoutTrashed();
                    }
                    if ($query && $produtosQuery) {
                        $produtosQuery = $produtosQuery->where('nome', 'like', '%'.$query.'%');
                    }
                    if ($produtosQuery && $limit) {
                        $totalProdutos = $produtosQuery->count();
                        if ($pagina) {
                            $produtosQuery = $produtosQuery->skip($limit * $pagina);
                        }
                        $produtos = $produtosQuery->limit($limit);
                    }
                    if ($produtos) {
                        $produtos = $produtos->get();
                    }
                    if ($produtos) {
                        foreach ($produtos as $produto) {
                            if ($produto &&
                                ! $produto->trashed() &&
                                $produto->id_subcategoria &&
                                $produto->subCategoria &&
                                ! $produto->subCategoria->trashed() &&
                                $produto->subCategoria->id_categoria &&
                                $produto->subCategoria->categoria &&
                                ! $produto->subCategoria->categoria->trashed()
                            ) {
                                $listaProdutos[] = [
                                    'name' => $produto->nome,
                                    'description' => $produto->nome,
                                    'categories' => [$produto->subCategoria->categoria->nome],
                                    'hierarchicalCategories' => [
                                        'lvl0' => $produto->subCategoria->categoria->nome,
                                        'lvl1' => $produto->subCategoria->categoria->nome . ' > '.$produto->subCategoria->nome
                                    ],
                                    'type' => $produto->subCategoria->categoria->nome,
                                    'price' => $produto->preco,
                                    'image' => $produto->imagem_data_url,
                                    'objectID' => $produto->id,
                                ];
                            }
                        }
                    }
                    if ($listaProdutos) {
                        $totalPaginas = ceil($totalProdutos/$limit);
                    }
                    $results[] = [
                        'hits' => $listaProdutos,
                        'nbHits' => count($listaProdutos),
                        'page' => $pagina,
                        'nbPages' => $totalPaginas,
                        'hitsPerPage' => $limit,
                        'facets' => $facets,
                        'facets_stats' => [],
                        'exhaustiveFacetsCount' => true,
                        'exhaustiveNbHits' => true,
                        'query' => '',
                        'queryAfterRemoval' => '',
                        'params' => '',
                        'index' => $rowRequest['indexName'],
                        'processingTimeMS' => 1
                    ];
                    continue;
                }
                $results[] = [];
            }
            return response()->json([
                'results' => $results
            ], 200);
        }
        return response()->json([], 200);
    }

    public function categorias(Request $request)
    {
        $results = [
            'categorias' => []
        ];
        $categorias = Apiato::call('Geral@Cadastro\Categoria\Shop\ObterListaAction', []);
        if ($categorias) {
            foreach ($categorias as $categoria) {
                if ($categoria &&
                    ! $categoria->trashed() &&
                    $categoria->nome
                ) {
                    $subCategorias = [];
                    if ($categoria->subCategoria) {
                        foreach ($categoria->subCategoria as $subCategoria) {
                            if ($subCategoria &&
                                ! $subCategoria->trashed() &&
                                $subCategoria->nome
                            ) {
                                $subCategorias[] = [
                                    'id' => $subCategoria->id,
                                    'nome' => $subCategoria->nome,
                                ];
                            }
                        }
                    }
                    $results['categorias'][] = [
                        'id' => $categoria->id,
                        'nome' => $categoria->nome,
                        'subcategorias' => $subCategorias,
                    ];
                }
            }
        }
        return response()->json($results, 200);
    }

    /**
     * Show record
     *
     * @param ObterRegistroRequest $request
     */
    public function produto(ObterRegistroRequest $request)
    {
        $model = Apiato::call('Geral@Cadastro\Produto\ObterRegistroAction', [$request]);
        if ($model &&
            $model->id > 0
        ) {
            $lists = [];
            $lists['registro'] = null;
            $lists['related_items'] = [];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer());

            if ($model->id_subcategoria &&
                $model->subCategoria &&
                $model->subCategoria->id_categoria
            ) {
                $related = Produto::withoutTrashed()->where('id', '<>', $model->id)->has('subCategoria', '>=', 1, 'and', function ($query) use ($model) {
                    $query->where('id_categoria', '=', $model->subCategoria->id_categoria)
                        ->whereNull('dt_remocao');
                })->limit(10)->get();
                if ($related) {
                    foreach ($related as $produto) {
                        if ($produto &&
                            ! $produto->trashed() &&
                            $produto->id_subcategoria &&
                            $produto->subCategoria &&
                            ! $produto->subCategoria->trashed() &&
                            $produto->subCategoria->id_categoria &&
                            $produto->subCategoria->categoria &&
                            ! $produto->subCategoria->categoria->trashed()
                        ) {
                            $lists['related_items'][] = [
                                'name' => $produto->nome,
                                'price' => $produto->preco,
                                'image' => $produto->imagem_data_url,
                                'objectID' => $produto->id,
                            ];
                        }
                    }
                }
            }

            $resource = new Item($model, new ProdutoTransformer);
            $lists['registro'] = $manager->createData($resource)->toArray();

            return response()->json($lists, 200);
        } else {
            throw new NotFoundException();
        }
    }
}
