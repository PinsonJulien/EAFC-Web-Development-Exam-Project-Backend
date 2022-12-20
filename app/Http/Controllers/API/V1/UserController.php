<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\V1\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserCollection;
use App\Filters\V1\UsersFilter;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected array $relations = [/*'studentCourses', */'teacherCourses'];

    function __construct() {}

    /**
     * Display a listing of the courses.
     *
     * @param Request $request
     * @return UserCollection
    */
    public function index(Request $request) {
        $users = $this->filterRequest(new UsersFilter(), User::query(), $request);
        $users = $this->includeRequestedRelations($users, $request, $this->relations);

        return new UserCollection($users->paginate()->appends($request->query()));
    }

    /**
    * Display the specified course.
     *
     * @param  User $user
     * @return UserResource
    */
    public function show(User $user): UserResource
    {
        $user = $this->includeRequestedRelations($user, request(), $this->relations);
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->all();

        // Store the profile picture if it's uploaded.
        $picture = $request->file('picture');
        if ($picture) {
            $path = $picture->storePublicly('public/pictures');
            $data['picture'] = Storage::url($path);
        }

        return new UserResource(User::create($data));
    }
}
