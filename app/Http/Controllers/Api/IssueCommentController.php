<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IssueComment\StoreIssueCommentRequest;
use App\Http\Requests\Api\IssueComment\UpdateIssueCommentRequest;
use App\Http\Resources\IssueCommentResource;
use App\Models\IssueComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IssueCommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = IssueComment::query()->with('user', 'issue');

        // Filter by issue
        if ($request->has('issue_id')) {
            $query->where('issue_id', $request->issue_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $query->orderBy('created_at', 'desc');
        $comments = $query->paginate($request->input('per_page', 15));

        return IssueCommentResource::collection($comments);
    }

    public function show(IssueComment $issueComment): IssueCommentResource
    {
        $issueComment->load(['user', 'issue']);
        return new IssueCommentResource($issueComment);
    }

    public function store(StoreIssueCommentRequest $request): IssueCommentResource
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $comment = IssueComment::create($data);
        $comment->load(['user', 'issue']);
        return new IssueCommentResource($comment);
    }

    public function update(UpdateIssueCommentRequest $request, IssueComment $issueComment): IssueCommentResource
    {
        $this->authorize('update', $issueComment);
        $issueComment->update($request->validated());
        $issueComment->load(['user', 'issue']);
        return new IssueCommentResource($issueComment);
    }

    public function destroy(IssueComment $issueComment): JsonResponse
    {
        $this->authorize('delete', $issueComment);
        $issueComment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
