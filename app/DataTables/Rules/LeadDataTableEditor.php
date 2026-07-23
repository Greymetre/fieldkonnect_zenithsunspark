<?php

namespace App\DataTables\Rules;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use App\Models\Lead;

class LeadDataTableEditor extends DataTablesEditor
{
    protected $model = Lead::class;

    /**
     * Get create action validation rules.
     *
     * @return array
     */
    public function createRules()
    {
        return [
            'email' => 'required|email|unique:' . $this->resolveModel()->getTable(),
            'name'  => 'required',
        ];
    }

    /**
     * Get edit action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function editRules(Model $model)
    {
        return [
            'email' => 'sometimes|required|email|' . Rule::unique($model->getTable())->ignore($model->getKey()),
            'name'  => 'sometimes|required',
        ];
    }

    /**
     * Get remove action validation rules.
     *
     * @param Model $model
     * @return array
     */
    public function removeRules(Model $model)
    {
        return [];
    }
}
