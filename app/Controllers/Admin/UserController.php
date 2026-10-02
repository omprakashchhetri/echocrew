<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Shield\Entities\User;

/**
 * Staff and user management.
 *
 * Only holders of `users.manage-admins` (superadmin) may create, edit, ban or
 * delete accounts in privileged groups or assign those groups.
 */
class UserController extends BaseController
{
    private const PRIVILEGED = ['superadmin', 'admin', 'developer'];

    public function index()
    {
        $users = (new UserModel())->orderBy('id')->findAll();

        return view('admin/users/index', [
            'pageTitle' => 'Users',
            'users'     => $users,
            'canManage' => fn (User $u): bool => $this->canManage($u),
        ]);
    }

    public function create()
    {
        return view('admin/users/form', [
            'pageTitle' => 'New user',
            'user'      => null,
            'groups'    => $this->assignableGroups(),
        ]);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[10]|max_length[255]',
            'group'    => 'required|in_list[' . implode(',', array_keys($this->assignableGroups())) . ']',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users = new UserModel();
        $user  = new User([
            'username' => (string) $this->request->getPost('username'),
            'email'    => (string) $this->request->getPost('email'),
            'password' => (string) $this->request->getPost('password'),
        ]);
        $users->save($user);

        $user = $users->findById($users->getInsertID());
        $user->activate();
        $user->syncGroups((string) $this->request->getPost('group'));

        return redirect()->to('/admin/users')->with('message', 'User created.');
    }

    public function edit($id)
    {
        $user = $this->findManageable($id);

        return view('admin/users/form', [
            'pageTitle' => 'Edit user',
            'user'      => $user,
            'groups'    => $this->assignableGroups(),
        ]);
    }

    public function update($id)
    {
        $user = $this->findManageable($id);

        $self  = (int) $user->id === (int) user_id();
        $rules = [
            'group'    => $self ? 'permit_empty' : 'required|in_list[' . implode(',', array_keys($this->assignableGroups())) . ']',
            'password' => 'permit_empty|min_length[10]|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Nobody changes their own group; it prevents accidental lock-out.
        if (! $self) {
            $user->syncGroups((string) $this->request->getPost('group'));
        }

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $user->password = $password;
            (new UserModel())->save($user);
        }

        return redirect()->to('/admin/users')->with('message', 'User updated.');
    }

    public function ban($id)
    {
        $user = $this->findManageable($id);
        $this->refuseSelf($user);

        $user->isBanned() ? $user->unBan() : $user->ban('Banned by an administrator');

        return redirect()->back()->with('message', $user->isBanned() ? 'User banned.' : 'User reinstated.');
    }

    public function delete($id)
    {
        $user = $this->findManageable($id);
        $this->refuseSelf($user);

        (new UserModel())->delete($user->id, true);

        return redirect()->to('/admin/users')->with('message', 'User deleted.');
    }

    private function findManageable($id): User
    {
        $user = (new UserModel())->findById($id) ?? throw PageNotFoundException::forPageNotFound();

        if (! $this->canManage($user)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Not allowed.');
        }

        return $user;
    }

    private function refuseSelf(User $user): void
    {
        if ((int) $user->id === (int) user_id()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('You cannot do that to your own account.');
        }
    }

    private function canManage(User $target): bool
    {
        return auth()->user()->can('users.manage-admins') || ! $target->inGroup(...self::PRIVILEGED);
    }

    /** @return array<string,string> group key => title */
    private function assignableGroups(): array
    {
        $all     = config('AuthGroups')->groups;
        $isSuper = auth()->user()->can('users.manage-admins');

        $out = [];
        foreach ($all as $key => $group) {
            if ($isSuper || ! in_array($key, self::PRIVILEGED, true)) {
                $out[$key] = $group['title'];
            }
        }

        return $out;
    }
}
