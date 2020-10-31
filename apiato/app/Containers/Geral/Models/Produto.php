<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use App\Containers\Geral\Models\SubCategoria;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Arr;
use Intervention\Image\Facades\Image;
use Laravel\Scout\Searchable;
use Exception;

/**
 * @property integer $id
 * @property integer $id_subcategoria
 * @property string $nome
 * @property float $preco
 * @property string $cor
 * @property string $imagem
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Produto extends MainModel
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
    protected $table = 'produto';

    protected $attributes = [
        'preco' => 0.0,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_subcategoria',
        'nome',
        'preco',
        'cor',
        'imagem',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_subcategoria' => 'ID SubCategoria',
        'nome' => 'Nome',
        'preco' => 'Preço',
        'cor' => 'Cor',
        'imagem' => 'Imagem',
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
                'id_subcategoria' => [
                    'required',
                    'numeric',
                    Rule::exists('sub_categoria', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('produto', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                                ->where('id_subcategoria', '=', $self->id_subcategoria);
                    }),
                ],
                'preco' => 'required|numeric|min:0.01',
                'imagem' => 'nullable|max:' . (2048 * 1024),
                'cor' => 'nullable|json',
            ];
        } else {
            return [
                'id_subcategoria' => [
                    'required',
                    'numeric',
                    Rule::exists('sub_categoria', 'id'),
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('produto', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                            ->where('id_subcategoria', '=', $self->id_subcategoria)
                            ->where('id', '<>', $self->id);
                    }),
                ],
                'preco' => 'required|numeric|min:0.01',
                'imagem' => 'nullable|max:' . (2048 * 1024),
                'cor' => 'nullable|json',
            ];
        }
    }

    /*public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->subCategoria->filter(function ($item) {
                return $item->shouldBeSearchable();
            })->searchable();
        });
    }*/

    public function subCategoria()
    {
        return $this->belongsTo('App\Containers\Geral\Models\SubCategoria', 'id_subcategoria', 'id')->withTrashed();
    }

    public function categoria()
    {
        if ($this->id_subcategoria &&
            $this->subCategoria &&
            ! $this->subCategoria->trashed() &&
            $this->subCategoria->id_categoria &&
            $this->subCategoria->categoria &&
            ! $this->subCategoria->categoria->trashed()
        ) {
            return $this->subCategoria->categoria;
        }
        return null;
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.id_subcategoria')) {
            $data['new_values']['id_subcategoria'] = $this->getAttribute('id_subcategoria') . ($this->subCategoria ? ' (' . $this->subCategoria->nome . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_subcategoria')) {
            $data['old_values']['id_subcategoria'] = $this->getOriginal('id_subcategoria');
            if ($this->getOriginal('id_subcategoria') && ($subCategoriaAntigo = SubCategoria::withTrashed()->find($this->getOriginal('id_subcategoria')))) {
                $data['old_values']['id_subcategoria'] .= ' (' . $subCategoriaAntigo->nome . ')';
            }
        }
        if (Arr::has($data, 'new_values.imagem')) {
            Arr::forget($data, 'new_values.imagem');
        }
        if (Arr::has($data, 'old_values.imagem')) {
            Arr::forget($data, 'old_values.imagem');
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

    public function getImagemDataUrlAttribute()
    {
        if ($this->imagem) {
            return (string)(Image::make($this->imagem)->encode('data-url'));
        }
        return null;
    }

    public function setCorAttribute($value)
    {
        if (is_array($value) &&
            !is_string($value)
        ) {
            $this->attributes['cor'] = json_encode($value);
        } else {
            $this->attributes['cor'] = $value;
        }
    }

    public function getCorDecodificadoAttribute()
    {
        if ($this->cor) {
            if (!is_array($this->cor) &&
                is_string($this->cor)
            ) {
                return json_decode($this->cor, true);
            } else {
                return $this->cor;
            }
        }
        return null;
    }
}
