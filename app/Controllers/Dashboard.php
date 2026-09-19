<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WidgetModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');

        $userModel   = new UserModel();
        $widgetModel = new WidgetModel();

        $user    = $userModel->find($userId);
        $widgets = $widgetModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();

        return view('dashboard/index', [
            'title'   => 'Dashboard',
            'user'    => $user,
            'widgets' => $widgets,
        ]);
    }
}
