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

        return view('reports.analytics', compact('agents'));
    }

    /**
     * Placeholder data until ticket/agent stats are wired to the database.
     *
     * @return array<int, array<string, mixed>>
     */
    private function sampleAgents(): array
    {
        return [
            [
                'name' => 'Marcus Thorne',
                'role' => 'Senior Technical Support',
                'resolved' => 342,
                'avg_response' => '12m',
                'load' => 85,
                'csat' => '4.9/5.0',
                'status' => 'online',
                'initials' => 'MT',
                'color' => '#3b6ea8',
            ],
            [
                'name' => 'Elena Rodriguez',
                'role' => 'Product Specialist',
                'resolved' => 289,
                'avg_response' => '18m',
                'load' => 65,
                'csat' => '4.7/5.0',
                'status' => 'online',
                'initials' => 'ER',
                'color' => '#2a7a5e',
            ],
            [
                'name' => 'Siddharth Varma',
                'role' => 'API Specialist',
                'resolved' => 215,
                'avg_response' => '9m',
                'load' => 40,
                'csat' => '5.0/5.0',
                'status' => 'away',
                'initials' => 'SV',
                'color' => '#5a3e8a',
            ],
        ];
    }
}
