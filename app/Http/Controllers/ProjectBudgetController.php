<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Currency;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectBudgetController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projectBudget';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('projects', $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        abort_403(user()->permission('manage_project_budget') != 'all');

        $this->projects = Project::with(['currency', 'expenses', 'members'])
            ->selectRaw('projects.*, (SELECT COALESCE(SUM(price * quantity), 0) FROM purchase_order_items WHERE purchase_order_id IN (SELECT id FROM purchase_orders WHERE project_id = projects.id)) as material_cost')
            ->get()
            ->map(function ($project) {
                $totalExpenses = $project->expenses->sum('total');
                $project->total_spent = $totalExpenses + ($project->material_cost ?? 0);
                $project->budget_remaining = ($project->budget_amount ?? 0) - $project->total_spent;
                $project->budget_percentage = $project->budget_amount > 0 ? round(($project->total_spent / $project->budget_amount) * 100, 2) : 0;

                return $project;
            });

        return view('project-budget.index', $this->data);
    }

    public function edit($id)
    {
        abort_403(user()->permission('manage_project_budget') != 'all');

        $this->project = Project::findOrFail($id);
        $this->currencies = Currency::all();

        return view('project-budget.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        abort_403(user()->permission('manage_project_budget') != 'all');

        $request->validate([
            'budget_amount' => 'required|numeric|min:0',
            'budget_currency_id' => 'required|exists:currencies,id',
        ]);

        $project = Project::findOrFail($id);
        $project->budget_amount = $request->budget_amount;
        $project->budget_currency_id = $request->budget_currency_id;
        $project->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('project-budget.index')]);
    }
}
