<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'name' => $this->name,
            'room_number' => $this->room_number,
            'checkin_date' => $this->checkin_date?->format('Y-m-d'),
            'checkout_date' => $this->checkout_date?->format('Y-m-d'),
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'source' => $this->source,
            'nationality' => $this->nationality,
            'contact' => $this->contact,
            'recovery' => $this->recovery,
            'recovery_cost' => $this->recovery_cost,
            'training' => $this->training,
            'priority' => $this->priority,
            'status' => $this->status,
            'assigned_to_user_id' => $this->assigned_to_user_id,
            'closed_at' => $this->closed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'issueTypes' => IssueTypeResource::collection($this->whenLoaded('issueTypes')),
            'createdBy' => UserResource::make($this->whenLoaded('createdBy')),
            'updatedBy' => UserResource::make($this->whenLoaded('updatedBy')),
            'assignedTo' => UserResource::make($this->whenLoaded('assignedTo')),
            'closedBy' => UserResource::make($this->whenLoaded('closedBy')),
            'verifiedBy' => UserResource::make($this->whenLoaded('verifiedBy')),
            'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
            'comments' => IssueCommentResource::collection($this->whenLoaded('comments')),
            'comments_count' => $this->whenCounted('comments'),
            'activityLogs' => ActivityLogResource::collection($this->whenLoaded('activityLogs')),
        ];
    }
}
