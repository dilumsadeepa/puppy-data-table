<?php

namespace Dilum\PuppyDataTable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class DataTable
{
    protected Builder $query;
    protected array $addColumns = [];
    protected array $editColumns = [];
    protected array $filterColumns = [];
    protected array $orderColumns = [];
    protected array $searchable = [];
    protected array $rawColumns = [];

    protected array $withRelations = [];

    public static function of(Builder $query): static
    {
        $instance = new static();
        $instance->query = $query;
        return $instance;
    }

    public function addColumn(string $name, $callback): static
    {
        $this->addColumns[$name] = $callback;
        return $this;
    }

    public function editColumn(string $name, $callback): static
    {
        $this->editColumns[$name] = $callback;
        return $this;
    }

    public function filterColumn(string $name, \Closure $callback): static
    {
        $this->filterColumns[$name] = $callback;
        return $this;
    }

    public function orderColumn(string $name, \Closure $callback): static
    {
        $this->orderColumns[$name] = $callback;
        return $this;
    }

    public function searchable(array $columns): static
    {
        $this->searchable = $columns;
        return $this;
    }

    public function rawColumns(array $columns): static
    {
        $this->rawColumns = $columns;
        return $this;
    }

    public function with(array $relations): static
    {
        $this->withRelations = $relations;
        $this->query->with($relations);
        return $this;
    }

    public function export(string $type = 'csv')
    {
        $data = $this->collectAll()->toArray();
        if ($type === 'csv') {
            $filename = 'export_' . time() . '.csv';
            $handle = fopen(storage_path('app/' . $filename), 'w+');
            if (!empty($data)) {
                fputcsv($handle, array_keys((array) $data[0]));
                foreach ($data as $row) {
                    fputcsv($handle, (array) $row);
                }
            }
            fclose($handle);
            return response()->download(storage_path('app/' . $filename))->deleteFileAfterSend(true);
        }

        // Excel via maatwebsite/excel - user must install package
        if ($type === 'excel') {
            return Excel::download(new \Dilum\PuppyDataTable\Exports\ArrayExport($data), 'export_' . time() . '.xlsx');
        }

        throw new \InvalidArgumentException('Unsupported export type: ' . $type);
    }

    protected function collectAll(): Collection
    {
        $rows = $this->query->get();
        return $this->applyColumns($rows);
    }

    protected function applyColumns(Collection $rows): Collection
    {
        return $rows->map(function ($row) {
            $arr = collect($row->toArray());

            foreach ($this->addColumns as $key => $cb) {
                $arr[$key] = is_callable($cb) ? $cb($row) : $cb;
            }

            foreach ($this->editColumns as $key => $cb) {
                if ($arr->has($key)) {
                    $arr[$key] = is_callable($cb) ? $cb($row) : $cb;
                }
            }

            return $arr->toArray();
        })->map(function ($item) {
            // ensure arrays are standard objects for export
            return (object) $item;
        });
    }

    public function toResponse(Request $request)
    {
        $perPage = (int) max(1, $request->get('per_page', 10));
        $page = (int) max(1, $request->get('page', 1));
        $q = (string) $request->get('q', '');
        $sortBy = (string) $request->get('sort_by', '');
        $sortDir = strtolower((string) $request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Apply searchable global search
        if ($q !== '' && !empty($this->searchable)) {
            $this->query->where(function ($sub) use ($q) {
                foreach ($this->searchable as $col) {
                    $sub->orWhere($col, 'LIKE', "%{$q}%");
                }
            });
        }

        // Apply filterColumns (callbacks)
        if (!empty($this->filterColumns)) {
            foreach ($this->filterColumns as $name => $cb) {
                $cb($this->query, $request->get($name));
            }
        }

        // Apply ordering
        if ($sortBy) {
            if (isset($this->orderColumns[$sortBy])) {
                ($this->orderColumns[$sortBy])($this->query, $sortDir);
            } else {
                $this->query->orderBy($sortBy, $sortDir);
            }
        }

        // total + pagination
        $total = (clone $this->query)->count();
        $rows = $this->query->forPage($page, $perPage)->get();

        // apply columns transformations
        $rows = $this->applyColumns($rows);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ],
        ]);
    }
}
