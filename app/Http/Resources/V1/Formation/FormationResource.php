<?php

namespace App\Http\Resources\V1\Formation;

use App\Http\Resources\V1\Cohort\CohortResource;
use App\Http\Resources\V1\Course\CourseResource;
use App\Http\Resources\V1\EducationLevel\EducationLevelResource;
use App\Http\Resources\V1\Enrollment\EnrollmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'educationLevel' => new EducationLevelResource($this->educationLevel),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'cohorts' => CohortResource::collection($this->whenLoaded('cohorts')),
        ];
    }
}