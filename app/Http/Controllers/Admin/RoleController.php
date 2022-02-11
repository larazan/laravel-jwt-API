<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Role;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    public function __construct() {
        $this->data['currentAdminMenu'] = 'role-user';
        $this->data['currentAdminSubMenu'] = 'role';
        $this->data['statuses'] = Role::STATUSES;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['roles'] = Role::orderBy('name', 'DESC')->paginate(10);

        return view('admin.roles.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['role'] = null;

		return view('admin.roles.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $params = $request->except('_token');       

		if (Role::create($params)) {
			Session::flash('success', 'Role has been created');
		} else {
			Session::flash('error', 'Role could not be created');
		}

		return redirect('admin/roles');
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
        $role = Role::findOrFail($id);

		$this->data['role'] = $role;

		return view('admin.roles.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $params = $request->except('_token');

		$role = Role::findOrFail($id);
		if ($role->update($params)) {
			Session::flash('success', 'Role has been updated.');
		}

		return redirect('admin/roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role  = Role::findOrFail($id);

		if ($role->delete()) {
			Session::flash('success', 'Role has been deleted');
		}

		return redirect('admin/roles');
    }
}
