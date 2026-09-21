<?php

namespace App\Controllers;

use App\Models\WidgetModel;

class Widget extends BaseController
{
    public function loader()
    {
        $id = $this->request->getGet('id');

        if (!$id) {
            return $this->response->setBody('Widget ID required');
        }

        $widgetModel = new WidgetModel();
        $widget = $widgetModel->where('widget_id', $id)->first();

        if (!$widget) {
            return $this->response->setBody('Widget not found');
        }

        if ($widget['expired_at'] && $widget['expired_at'] < date('Y-m-d')) {
            return $this->response->setBody('Widget expired');
        }

        $settings = json_decode($widget['settings'], true) ?? [];

        // Redirect mode (jika ada targetUrl / urls)
        $dataParam   = strtolower(trim($this->request->getGet('data') ?? ''));
        $redirectUrl = '';

        if (!empty($dataParam) && isset($settings['urls'][$dataParam])) {
            $redirectUrl = $settings['urls'][$dataParam];
        } elseif (!empty($settings['targetUrl'])) {
            $redirectUrl = $settings['targetUrl'];
        }

        if ($redirectUrl !== '') {
            if ($widget['is_whitelabel'] == 1) {
                $redirectUrl .= (strpos($redirectUrl, '?') !== false ? '&' : '?') . 'wl=1';
            }
            return redirect()->to($redirectUrl);
        }

        // Render iframe widget
        $widgetType = $settings['widget_type'] ?? 'table';
        $baseUrl    = rtrim(base_url(), '/') . '/';
        $color      = ltrim($settings['primary_color'] ?? '#0d9488', '#');
        $name       = $widget['journal_name'] ?? '';

        $iframeSrc = '';

        if ($widgetType === 'chart') {
            $issn    = $settings['eissn'] ?? '';
            $gsId    = $settings['scholar_id'] ?? '';
            $sintaId = $settings['sinta_id'] ?? '';
            $iframeSrc = "{$baseUrl}widgets/graph?issn={$issn}&id={$gsId}&sinta={$sintaId}&warna={$color}";
            $height = 600;
        } elseif ($widgetType === 'scimago') {
            $scimagoId = $settings['scimago_id'] ?? '';
            $iframeSrc = "{$baseUrl}widgets/scimago?id={$scimagoId}&warna={$color}&name=" . urlencode($name);
            $height = 480;
        } elseif ($widgetType === 'indexing') {
            $params = ['warna' => $color];
            foreach (($settings['urls'] ?? []) as $k => $v) {
                if (in_array($k, ['sinta', 'scopus', 'scimago', 'doaj', 'dimensions', 'garuda', 'scholar', 'scilit']) && $v) {
                    $params[$k] = $v;
                }
            }
            $iframeSrc = "{$baseUrl}widgets/indexing?" . http_build_query($params);
            $height = 380;
        } elseif ($widgetType === 'tools') {
            $params = ['warna' => $color];
            foreach (($settings['urls'] ?? []) as $k => $v) {
                if (in_array($k, ['turnitin', 'mendeley', 'grammarly', 'quillbot']) && $v) {
                    $params[$k] = $v;
                }
            }
            $iframeSrc = "{$baseUrl}widgets/tools?" . http_build_query($params);
            $height = 220;
        } elseif ($widgetType === 'template') {
            $iframeSrc = "{$baseUrl}widgets/templates?warna={$color}&name=" . urlencode($name);
            $height = 200;
        } else {
            // table (default)
            $dataObj = [
                'n'  => $name,
                's'  => $settings['sinta_id'] ?? '',
                'g'  => $settings['scholar_id'] ?? '',
                'c'  => $settings['scopus_citations'] ?? '',
                'i'  => $settings['eissn'] ?? '',
                'ti' => $settings['statcounter_id'] ?? '',
                'ai' => $settings['statcounter_accid'] ?? '',
                'li' => $settings['statcounter_lid'] ?? '',
                'sc' => $settings['statcounter_sc'] ?? '',
                'ct' => $settings['statcounter_count'] ?? '',
                'st' => $settings['table_style'] ?? 'text',
                'p'  => $settings['primary_color'] ?? '#0d9488',
                't'  => $settings['text_color'] ?? '#ffffff',
            ];
            $encoded = base64_encode(json_encode($dataObj));
            $iframeSrc = "{$baseUrl}widgets/statistik?d={$encoded}";
            $height = 400;
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<style>body{margin:0;padding:0;overflow:hidden;}iframe{width:100%;border:0;}</style>'
            . '</head><body>'
            . '<iframe src="' . htmlspecialchars($iframeSrc) . '" height="' . $height . '" scrolling="no"></iframe>'
            . '</body></html>';

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($html);
    }

    public function statistik()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/statistik'));
    }

    public function graph()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/graph'));
    }

    public function scimago()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/scimago'));
    }

    public function indexing()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/indexing'));
    }

    public function tools()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/tools'));
    }

    public function templates()
    {
        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('widgets/templates'));
    }
}
