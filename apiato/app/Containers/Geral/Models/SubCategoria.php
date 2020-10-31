<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Models\Categoria;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;
use Exception;

/**
 * @property integer $id
 * @property integer $id_categoria
 * @property string $nome
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class SubCategoria extends MainModel
{
    use QueryCacheable;

    //use Searchable;

    public $cacheFor = 180;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_categoria';

    //protected $touches = ['categoria'];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_categoria',
        'nome',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_categoria' => 'ID Categoria',
        'nome' => 'Nome',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];


    public function getRules()
    {
        $self = $this;
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'id_categoria' => [
                    'required',
                    'numeric',
                    Rule::exists('categoria', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('sub_categoria', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                            ->where('id_categoria', '=', $self->id_categoria);
                    }),
                ],
            ];
        } else {
            return [
                'id_categoria' => [
                    'required',
                    'numeric',
                    Rule::exists('categoria', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('sub_categoria', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id_categoria', '=', $self->id_categoria)
                              ->where('id', '<>', $self->id);
                    }),
                ],
            ];
        }
    }

    public function categoria()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Categoria', 'id_categoria', 'id')->withTrashed();
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.id_categoria')) {
            $data['new_values']['id_categoria'] = $this->getAttribute('id_categoria') . ($this->categoria ? ' (' . $this->categoria->nome . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_categoria')) {
            $data['old_values']['id_categoria'] = $this->getOriginal('id_categoria');
            if ($this->getOriginal('id_categoria') && ($categoriaAntigo = Categoria::withTrashed()->find($this->getOriginal('id_categoria')))) {
                $data['old_values']['id_categoria'] .= ' (' . $categoriaAntigo->nome . ')';
            }
        }

        return $data;
    }

    /*public function toSearchableArray()
    {
        $array = $this->toArray();

        $array = $this->transform($array);

        $array['subCategoria'] = $this->subCategoria->map(function ($data) {
            return $data['nome'];
        })->toArray();

        return $array;
    }*/
}
