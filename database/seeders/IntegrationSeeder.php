<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Integration;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            [
                'name' => 'Slack',
                'category' => 'communication',
                'color' => '#4A154B',
                'desc' => 'Connect channels, send ticket updates and alerts.'
            ],
            [
                'name' => 'Jira',
                'category' => 'developer',
                'color' => '#0052CC',
                'desc' => 'Sync tasks and issues between platforms seamlessly.'
            ],
            [
                'name' => 'GitHub',
                'category' => 'developer',
                'color' => '#24292F',
                'desc' => 'Link commits and pull requests to your tickets.'
            ],
            [
                'name' => 'Google Analytics',
                'category' => 'analytics',
                'color' => '#E37400',
                'desc' => 'Track internal operational web traffic efficiently.'
            ],
            [
                'name' => 'Datadog',
                'category' => 'analytics',
                'color' => '#632CA6',
                'desc' => 'Monitor infrastructure metrics alongside tasks.'
            ],
            [
                'name' => 'Microsoft Teams',
                'category' => 'communication',
                'color' => '#6264A7',
                'desc' => 'Collaborate on tickets and tasks via Teams.'
            ],
        ];

        foreach ($integrations as $integration) {
            Integration::updateOrCreate(
                ['name' => $integration['name']],
                $integration
            );
        }
    }
}
