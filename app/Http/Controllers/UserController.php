<?php

namespace App\Http\Controllers;

use App\Http\Filters\Name;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index(Request $request)
    {
        $users = app(Pipeline::class)
            ->send($this->user->query())
            ->through([
                Name::class,
            ])
            ->thenReturn()
            ->paginate($request->per_page);

        return new UserCollection($users);
    }

    public function store(CreateUserRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $request->merge([
                'password' => bcrypt('password'),
            ]);

            return $this->user->create($request->all());
        });
    }

    public function show($id)
    {
        $user = $this->user->findOrFail($id);

        return new UserDetail($user);
    }

    public function update(CreateUserRequest $request, $id)
    {
        $user = $this->user->findOrFail($id);

        $user->update($request->all());

        return $user;
    }

    public function destroy($id)
    {
        $user = $this->user->findOrFail($id);

        $user->delete();

        return $user;
    }
}
