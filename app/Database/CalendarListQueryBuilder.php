<?php

namespace App\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class CalendarListQueryBuilder
 *
 * Query builder for listing calendars depending on filter and order options
 *
 * @package App\Database
 */
class CalendarListQueryBuilder
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var array
     */
    const OPERATOR_TO_BUILDER_METHOD = [
        'and' => 'where',
        'or' => 'orWhere',
        'in' => 'whereIn'
    ];

    /**
     * CalendarListQueryBuilder constructor.
     *
     * @return $this
     */
    public function __construct()
    {
        $this->query = DB::table('calendars');

        return $this;
    }

    /**
     * @return $this
     */
    protected function applyBase(): self
    {
        $this->query = $this->query
            ->select(['calendars.id AS calendar_id', 'calendars.title', 'calendar_years.year', 'calendar_months.month', 'calendar_days.day'])
            ->join('calendar_years', 'calendars.id', '=', 'calendar_years.calendar_id')
            ->join('calendar_months', 'calendar_years.id', '=', 'calendar_months.year_id')
            ->join('calendar_days', 'calendar_months.id', '=', 'calendar_days.month_id');

        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    protected function applyFilters(array $filters): self
    {
        foreach ($filters as $filterOperator => $filterFieldValue) {
            $builderMethodName = $this->getFilterBuilderMethodNameFromOperator($filterOperator);

            foreach ($filterFieldValue as $field => $value) {
                // Handle multiple values for a field for OR queries
                if ($filterOperator === 'or' && count($value) > 1) {
                    foreach ($value as $orValue) {
                        $this->query = $this->query->{$builderMethodName}($field, $orValue);
                    }

                    continue;
                }

                $this->query = $this->query->{$builderMethodName}($field, (array) $value);
            }
        }

        return $this;
    }

    /**
     * @param string $operator
     * @return string
     */
    protected function getFilterBuilderMethodNameFromOperator(string $operator): string
    {
        return self::OPERATOR_TO_BUILDER_METHOD[$operator];
    }

    /**
     * @return $this
     */
    protected function applyDefaultOrdering(): self
    {
        $this->query = $this->query
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('day', 'desc');

        return $this;
    }

    /**
     * @param array $orderBy
     * @return $this
     */
    protected function applyOrdering(array $orderBy): self
    {
        foreach ($orderBy as $column => $direction) {
            $this->query = $this->query->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * TODO: Could move $orderBy and $filters to separate objects/class instead of generic array
     *
     * @param $orderBy null|array
     * @param $filters null|array
     * @return \Illuminate\Support\Collection
     */
    public function get($orderBy, $filters): Collection
    {
        $this->applyBase();

        if ($orderBy) {
            $this->applyOrdering($orderBy);
        } else {
            $this->applyDefaultOrdering();
        }

        if ($filters) {
            $this->applyFilters($filters);
        }

        return $this->query->get();
    }
}