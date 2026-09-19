<?php

namespace App\Controllers;

use App\Models\WidgetModel;

class Journal extends BaseController
{
    public function edit($id = null)
    {
        $userId = session()->get('user_id');
        $widgetModel = new WidgetModel();

        if ($id) {
            $widget = $widgetModel->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$widget) {
                return redirect()->to('/dashboard')->with('error', 'Widget tidak ditemukan.');
            }
        } else {
            $widget = null;
        }

        return view('dashboard/edit', [
            'title'  => 'Edit Journal',
            'widget' => $widget,
        ]);
    }

    public function preview($id = null)
    {
        $userId = session()->get('user_id');
        $widgetModel = new WidgetModel();

        $widget = $widgetModel->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$widget) {
            return redirect()->to('/dashboard');
        }

        return view('dashboard/preview', [
            'title'  => 'Preview Widget',
            'widget' => $widget,
        ]);
    }

    public function save()
    {
        $userId = session()->get('user_id');
        $widgetModel = new WidgetModel();

        $data = $this->request->getPost();

        $insert = [
            'user_id'       => $userId,
            'journal_name'  => $data['journal_name'] ?? 'Untitled',
            'widget_id'     => 'PNP-' . bin2hex(random_bytes(6)),
            'settings'      => json_encode($data),
            'is_whitelabel' => 0,
        ];

        $widgetModel->insert($insert);

        return redirect()->to('/dashboard')->with('success', 'Widget berhasil dibuat.');
    }
    public function builder($id = null)
    {
        $userId = session()->get('user_id');
        $widgetModel = new WidgetModel();

        if ($id) {
            $widget = $widgetModel->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$widget) {
                return redirect()->to('/dashboard');
            }

            // Redirect ke landing page dengan parameter edit
            return redirect()->to('/?edit=' . $id);
        }

        return redirect()->to('/');
    }
    public function delete($id)
    {
        $userId = session()->get('user_id');
        $widgetModel = new WidgetModel();

        $widget = $widgetModel->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($widget) {
            $widgetModel->delete($id);
            return redirect()->to('/dashboard')->with('success', 'Widget berhasil dihapus.');
        }

        return redirect()->to('/dashboard')->with('error', 'Widget tidak ditemukan.');
    }
}
