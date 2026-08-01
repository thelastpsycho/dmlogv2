<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiToolsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AiChatController extends Controller
{
    protected AiToolsService $tools;

    public function __construct(AiToolsService $tools)
    {
        $this->tools = $tools;
    }

    public function handle(Request $request, string $name): JsonResponse
    {
        abort_unless($request->user()->hasPermission('ai.chat.view'), 403);

        $result = match ($name) {
            'summary' => $this->tools->summary(
                $request->input('period', 'last_week'),
                $request->input('start_date'),
                $request->input('end_date'),
                (int) $request->input('limit', 25),
            ),
            'room-search' => $this->tools->roomSearch(
                $request->validate(['room_number' => 'required|string'])['room_number'],
                (int) $request->input('limit', 10),
            ),
            'guest-search' => $this->tools->guestSearch(
                $request->validate(['guest_name' => 'required|string'])['guest_name'],
                (int) $request->input('limit', 10),
            ),
            'department-stats' => $this->tools->departmentStats(
                $request->input('period'),
            ),
            'urgent-issues' => $this->tools->urgentIssues(
                (int) $request->input('limit', 20),
            ),
            default => throw new NotFoundHttpException("Unknown AI tool [{$name}]."),
        };

        return response()->json($result);
    }
}
