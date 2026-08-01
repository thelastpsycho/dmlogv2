<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Retrieves issue statistics summary for a time period. Spike/test tool for laravel/mcp evaluation.')]
class QuerySummaryTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response|ResponseFactory
    {
        $period = $request->get('period', 'last_week');

        return Response::structured([
            'period' => $period,
            'total_issues' => \App\Models\Issue::count(),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'period' => $schema->string()->description('Time period: today, last_week, this_week, last_month'),
        ];
    }
}
