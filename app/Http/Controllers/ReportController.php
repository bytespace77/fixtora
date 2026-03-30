<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Reports & analytics dashboard (metrics, charts, agent performance).
     */
    public function index()
    {
        $agents = $this->sampleAgents();

        return view('reports.index', compact('agents'));
    }

    /**
     * Placeholder data until ticket/agent stats are wired to the database.
     *
     * @return array<int, array<string, mixed>>
     */
    private function sampleAgents(): array
    {
        return [];
    }
}
