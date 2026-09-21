<?php

namespace App\Controllers;

use App\Models\WidgetModel;

class Home extends BaseController
{
    public function index()
    {
        $editWidgetId = $this->request->getGet('edit');
        $w = null;
        $settings = [];

        if ($editWidgetId && session()->get('user_id')) {
            $widgetModel = new WidgetModel();
            $w = $widgetModel->where('id', $editWidgetId)
                ->where('user_id', session()->get('user_id'))
                ->first();

            if ($w) {
                $decoded = json_decode($w['settings'] ?? '', true);
                $settings = is_array($decoded) ? $decoded : [];
            }
        }

        return view('index', [
            'editWidgetId' => $editWidgetId,
            'w'            => $w,
            'settings'     => $settings,
        ]);
    }

    public function preview()
    {
        return view('preview');
    }
}
