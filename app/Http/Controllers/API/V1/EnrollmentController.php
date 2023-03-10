<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\V1\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\V1\Enrollment\UpdateEnrollmentRequest;
use App\Http\Resources\V1\Enrollment\EnrollmentResource;
use App\Http\Responses\Errors\ConflictErrorResponse;
use App\Http\Responses\Errors\LockedErrorResponse;
use App\Http\Responses\Successes\NoContentSuccessResponse;
use App\Models\CohortMember;
use App\Models\CohortRole;
use App\Models\Enrollment;
use App\Models\SiteRole;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

/**
 * Enrollment controller for the V1 of the API
 */
class EnrollmentController extends V1Controller
{
    protected string $model = Enrollment::class;
    protected string $resource = EnrollmentResource::class;
    protected array $joinedRelations = ['user', 'formation'];

    /**
     * Returns the specified Enrollment.
     *
     * @param  Enrollment $enrollment
     * @return EnrollmentResource
     */
    public function show(Enrollment $enrollment): EnrollmentResource
    {
        $enrollment = $this->applyIncludeRelationParameters($enrollment, request());
        $enrollment->load($this->joinedRelations);
        return new EnrollmentResource($enrollment);
    }

    /**
     * Insert a new Enrollment for a user and formation.
     * By default, the status is set to PENDING
     * The insertion fails if there's already an enrollment pending for the user to the specified formation.
     * Returns the created enrollment.
     *
     * @param StoreEnrollmentRequest $request
     * @return JsonResponse|ConflictErrorResponse
     */
    public function store(StoreEnrollmentRequest $request): JsonResponse|ConflictErrorResponse
    {
        $data = $request->all();
        $defaultStatus = Status::PENDING;
        $userId = $data['user_id'];
        $formationId = $data['formation_id'];

        $hasPending = Enrollment::where([
            ['user_id', '=', $userId],
            ['formation_id', '=', $formationId],
            ['status_id', '=', $defaultStatus],
        ])->exists();

        if ($hasPending) {
            $message = "Could not register enrollment for the formation [".$formationId."] for the user [".$userId."], already one pending.";
            $errors = [
                'status' => $message,
            ];

            return new ConflictErrorResponse($message, $errors);
        }

        $resource = new EnrollmentResource(
            Enrollment::create([
                'user_id' => $userId,
                'formation_id' => $formationId,
                'status_id' => $defaultStatus,
            ])->load($this->joinedRelations)
        );

        return $resource->response()->setStatusCode(HTTPResponse::HTTP_CREATED);
    }

    /**
     * Update the specified Enrollment using the request data.
     * Cannot be updated if its status is not PENDING.
     * When an Enrollment is APPROVED, automatically create a CohortMember for the student.
     * Returns the updated Enrollment.
     * Works for both PUT and PATCH requests.
     *
     * @param UpdateEnrollmentRequest $request
     * @param Enrollment $enrollment
     * @return EnrollmentResource|LockedErrorResponse
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): EnrollmentResource|LockedErrorResponse
    {
        // Cannot update non pending enrollments.
        if ($enrollment->status_id !== Status::PENDING)
            return $this->generateStatusLockedResponse($enrollment->id, 'update');

        $data = $request->all();
        $statusId = $data['status_id'] ?? null;

        if ($request->method() === 'PATCH')
            $data = array_filter($data);
        // Do not delete the status on PUT request.
        else if (!$data['status_id'])
            unset($data['status_id']);

        $enrollment->update($data);
        $enrollment->loadMissing($this->joinedRelations);

        if ($statusId != Status::APPROVED)
            return new EnrollmentResource($enrollment);

        // If the status was approved
        $user = $enrollment->user;

        // Update User site role to User if Guest.
        if ($user->isGuestSiteRole())
            $user->changeSiteRole(SiteRole::USER);

        // try to join the user to the formation's current year cohort.
        $cohort = $enrollment->formation->getCurrentAcademicYearCohort();
        $cohortMember = $cohort->findCohortMemberByUser($user);

        if (!$cohortMember) {
            // Add the user to the cohort, with the student role.
            $cohortMember = new CohortMember([
                'cohort_id' => $cohort->id,
                'user_id' => $user->id,
                'cohort_role_id' => CohortRole::STUDENT,
            ]);
            $cohortMember->save();
        }

        return new EnrollmentResource($enrollment);
    }

    /**
     * Delete the specified Enrollment.
     * Can only be deleted if the status is still PENDING.
     *
     * @param Request $request
     * @param Enrollment $enrollment
     * @return NoContentSuccessResponse|ConflictErrorResponse|LockedErrorResponse
     */
    public function destroy(Request $request, Enrollment $enrollment): NoContentSuccessResponse|ConflictErrorResponse|LockedErrorResponse
    {
        // Cannot delete non pending enrollments.
        if ($enrollment->status_id !== Status::PENDING)
            return $this->generateStatusLockedResponse($enrollment->id, 'delete');

        return $this->commonDestroy($request, $enrollment);
    }

    /**
     * Generates a Locked error response for status lock.
     *
     * @param int $enrollmentId
     * @param string $action
     * @return LockedErrorResponse
     */
    protected function generateStatusLockedResponse(int $enrollmentId, string $action): LockedErrorResponse
    {
        $message = "Cannot ".$action." the Enrollment [".$enrollmentId."] because its status is not [pending]";
        $errors = [
            "status" => $message
        ];

        return new LockedErrorResponse($message, $errors);
    }
}
