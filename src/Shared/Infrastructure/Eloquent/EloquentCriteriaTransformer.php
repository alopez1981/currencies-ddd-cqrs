<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Infrastructure\Eloquent;

use Hoyvoy\Shared\Domain\Criteria\Criteria;
use Hoyvoy\Shared\Domain\Criteria\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @method apply(\Illuminate\Database\Eloquent\Builder $query, Criteria $criteria)
 */
final class EloquentCriteriaTransformer
{
    public function __construct(
        private Criteria $criteria,
        private Model    $model
    )
    {
    }

    public function builder(): Builder
    {
        $query = $this->model->newQuery();
        return $this->apply($query, $this->criteria);
    }

    public function apply(Builder $query, Criteria $criteria): Builder
    {
        foreach ($criteria->filters()->all() as $filter) {
            $this->applyFilter($query, $filter);
        }

        if ($criteria->order()) {
            $query->orderBy(
                $criteria->order()->orderBy(),
                $criteria->order()->orderType()
            );
        }

        if ($criteria->offset() !== null) $query->offset($criteria->offset());
        if ($criteria->limit() !== null) $query->limit($criteria->limit());

        return $query;
    }

    private function applyFilter(Builder $query, Filter $filter): void
    {
        $field = $filter->field();
        $operator = $filter->operator();
        $value = $filter->value();

        switch ($operator) {
            case '=':
            case '!=':
            case '>':
            case '>=':
            case '<':
            case '<=':
                $query->where($field, $operator, $value);
                break;
            case 'LIKE':
                $query->where($field, 'LIKE', $value);
                break;
            case 'IN':
                $query->whereIn($field, (array)$value);
                break;
            case 'NOT_IN':
                $query->whereNotIn($field, (array)$value);
                break;
            case 'BETWEEN':
                $arr = (array)$value;
                if (count($arr) === 2) $query->whereBetween($field, [$arr[0], $arr[1]]);
                break;
            case 'IS_NULL':
                $query->whereNull($field);
                break;
            case 'IS_NOT_NULL':
                $query->whereNotNull($field);
                break;
            default:
                break;
        }
    }

}
