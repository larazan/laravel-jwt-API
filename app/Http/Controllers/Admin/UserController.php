<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->data['currentAdminMenu'] = 'role-user';
        $this->data['currentAdminSubMenu'] = 'user';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['users'] = User::latest()->paginate(10);

        return view('admin.users.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['roles'] = Role::pluck('name', 'id');
        $this->data['roleID'] = null;

        return view('admin.users.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|min:1'
        ]);

        $request->merge(['password' => bcrypt($request->get('password'))]);

        $user = DB::transaction(function() use ($request) {
            $roleId = $request['role_id'];
            $user = User::create($request);
			$user->roles()->sync($roleId);
        });

        if ($user) {
            Session::flash('success', 'User has been created');
        } else {
            Session::flash('error', 'Unable to create user');
        }

        return view('admin.users.index', $this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->data['user'] = $user;
        $this->data['roles'] = Role::pluck('name', 'id');
        $this->data['roleID'] = $user->roles->pluck('id');

        return view('admin.users.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|min:1'
        ]);

        // Get the user
        $user = User::findOrFail($id);

        $saved = false;
		DB::transaction(
			function () use ($user, $request) {
				// check for password change
                if($request->get('password')) {
                    $user->password = bcrypt($request->get('password'));
                }

                $roleId = $request['role_id'];
                $user->save();
			    $user->roles()->sync($roleId);

				// return true;
                Session::flash('success', 'User has been saved');
			}
		);

        return redirect('admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
       
        if ($user->delete()) {
            Session::flash('success', 'User has been deleted');
        }
        return redirect('admin/users');
    }
}
