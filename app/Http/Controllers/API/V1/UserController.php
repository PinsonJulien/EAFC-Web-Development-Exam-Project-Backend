<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\V1\User\Picture\StorePictureUserRequest;
use App\Http\Requests\V1\User\StoreUserRequest;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Responses\Errors\UnprocessableEntityErrorResponse;
use App\Http\Responses\Successes\NoContentSuccessResponse;
use App\Models\SiteRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends V1Controller
{
    public const PICTURE_STORAGE_PATH = 'pictures';

    protected string $model = User::class;
    protected string $resource = UserResource::class;

    function __construct() {}

    /**
     * Returns the specified User.
     *
     * @param  User $user
     * @return UserResource
    */
    public function show(User $user): UserResource
    {
        $user = $this->applyIncludeRelationParameters($user, request());
        return new UserResource($user);
    }

    /**
     * Insert a new User using the request data.
     * A user will be Guest by default.
     * Returns the created User.
     *
     * @param StoreUserRequest $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->all();

        $data['site_role_id'] = SiteRole::GUEST;

        // Store the profile picture if it's uploaded.
        $data['picture'] = $this->savePicture($request);

        return new UserResource(User::create($data));
    }

    /**
     * Update the specified User using the request data.
     * Returns the updated User.
     * Works for both PUT and PATCH requests.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->all();
        if ($request->method() === 'PATCH')
            $data = array_filter($data);
        // Do not delete the role on PUT request.
        else if (!$data['site_role_id'])
            unset($data['site_role_id']);

        $user->update($data);
        return new UserResource($user);
    }

    /**
     * Delete the specified User.
     *
     * @param Request $request
     * @param User $user
     * @return NoContentSuccessResponse|UnprocessableEntityErrorResponse
     */
    public function destroy(Request $request, User $user): NoContentSuccessResponse|UnprocessableEntityErrorResponse
    {
        // Only guest users can be deleted.
        if (!$user->siteRole->isGuest()) {
            $message = "Could not delete the user [".$user->id."] because its siteRole [".$user->site_role_id."] is not Guest [".SiteRole::GUEST."].";
            $errors = [
                'role' => $message,
                'value' => $user->site_role_id,
            ];

            return new UnprocessableEntityErrorResponse($message, $errors);
        }

        // Delete the profile picture
        $this->destroyPicture($request, $user);

        // Delete enrollments
        $user->enrollments()->delete();

        // Delete the user
        $user->delete();

        return new NoContentSuccessResponse();
    }

    /** Picture methods **/

    /**
     * Change the picture of a specified User.
     * The previous picture is deleted from the storage and the new one is registered.
     *
     * @param StorePictureUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function storePicture(StorePictureUserRequest $request, User $user)
    {
        $this->replacePicture($request, $user);
        return new UserResource($user);
    }

    /**
     * Delete the picture of a specified User.
     *
     * @param Request $request
     * @param User $user
     * @return NoContentSuccessResponse
     */
    public function destroyPicture(Request $request, User $user): NoContentSuccessResponse
    {
        // Delete the picture
        $this->replacePicture($request, $user);
        return new NoContentSuccessResponse();
    }

    /**
     * Save a picture that is attached to the request.
     * Return the path of the stored picture.
     * Return null if nothing was attached.
     *
     * @param Request $request
     * @return string|null
     */
    protected function savePicture(Request $request): string|null
    {
        $picture = $request->file('picture');
        if (!$picture)
            return null;

        return $picture->storePublicly(self::PICTURE_STORAGE_PATH);
    }

    /**
     * Replace the picture of a given User using the request.
     * Will delete the picture of a user if the request was not filled.
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    protected function replacePicture(Request $request, User $user): void
    {
        // Delete the existing picture of the user, if it exists.
        $previousPicture = $user->picture;
        if ($previousPicture && Storage::exists($previousPicture)) {
            Storage::delete($previousPicture);
        }

        // If no file was included in the request, it will be null.
        $data = [
            'picture' => $this->savePicture($request),
        ];

        $user->update($data);
    }
}
