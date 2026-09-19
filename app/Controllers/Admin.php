<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WidgetModel;

class Admin extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel   = new UserModel();
        $widgetModel = new WidgetModel();

        return view('admin/index', [
            'title'   => 'Admin Panel',
            'users'   => $userModel->orderBy('created_at', 'DESC')->findAll(),
            'widgets' => $widgetModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function approve($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $userModel->update($id, ['status' => 'active']);

        return redirect()->to('/admin')->with('success', 'User diaktifkan.');
    }

    public function ban($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $userModel->update($id, ['status' => 'banned']);

        return redirect()->to('/admin')->with('success', 'User diblokir.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('/admin')->with('success', 'User dihapus.');
    }
    public function journals()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $widgetModel = new WidgetModel();

        return view('admin/journals', [
            'title'   => 'Kelola Jurnal',
            'widgets' => $widgetModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }
    public function deleteJournal($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $widgetModel = new WidgetModel();
        $widgetModel->delete($id);

        return redirect()->to('/admin/journals')->with('success', 'Widget dihapus.');
    }
}
