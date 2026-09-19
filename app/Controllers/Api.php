<?php

namespace App\Controllers;

use App\Models\WidgetModel;

class Api extends BaseController
{
    public function saveWidget()
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['settings'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $settings   = $data['settings'];
        $userId     = session()->get('user_id');
        $widgetModel = new WidgetModel();

        $journalName = $settings['journal_name'] ?? 'Untitled';

        if (isset($data['id']) && !empty($data['id'])) {
            // Update existing
            $widget = $widgetModel->where('id', $data['id'])
                ->where('user_id', $userId)
                ->first();

            if (!$widget) {
                return $this->response->setJSON(['success' => false, 'message' => 'Widget not found']);
            }

            $widgetModel->update($data['id'], [
                'journal_name' => $journalName,
                'settings'     => json_encode($settings),
            ]);

            return $this->response->setJSON(['success' => true, 'id' => $data['id']]);
        }

        // Insert new
        $widgetId = 'IW-' . bin2hex(random_bytes(6));

        $widgetModel->insert([
            'user_id'       => $userId,
            'journal_name'  => $journalName,
            'widget_id'     => $widgetId,
            'settings'      => json_encode($settings),
            'is_whitelabel' => 0,
        ]);

        $newId = $widgetModel->getInsertID();

        return $this->response->setJSON(['success' => true, 'id' => $newId, 'widget_id' => $widgetId]);
    }

    public function fetch()
    {
        $sintaId = $this->request->getGet('sinta') ?? '';
        $issn    = $this->request->getGet('issn') ?? '';

        $response = [
            'success' => false,
            'data'    => [
                'rank'              => 'S2',
                'impact'            => '0.00',
                'citations'         => '0',
                'scopus'            => '',
                'eissn'             => '',
                'openAlexCitations' => '0',
            ],
            'error' => '',
        ];

        if (empty($sintaId)) {
            $response['error'] = 'Sinta ID tidak boleh kosong.';
            return $this->response->setJSON($response);
        }

        $sintaUrl = "https://sinta.kemdiktisaintek.go.id/journals/profile/$sintaId";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sintaUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $sintaHtml = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($sintaHtml && $httpCode == 200) {
            $extractStat = function ($html, $label) {
                $pattern = '/<div class="stat-num">([^<]+)<\/div>\s*<div class="stat-text">' . preg_quote($label, '/') . '<\/div>/i';
                if (preg_match($pattern, $html, $matches)) {
                    return trim($matches[1]);
                }
                return null;
            };

            if (preg_match('/<h3 class="journal-name"[^>]*>([^<]+)<\/h3>/i', $sintaHtml, $matches)) {
                $response['data']['name'] = html_entity_decode(trim($matches[1]));
            } elseif (preg_match('/<div class="journal-name"[^>]*>([^<]+)<\/div>/i', $sintaHtml, $matches)) {
                $response['data']['name'] = html_entity_decode(trim($matches[1]));
            }

            $rankValue = $extractStat($sintaHtml, 'Current Acreditation');
            if ($rankValue && preg_match('/Sinta (\d+)/i', $rankValue, $m)) {
                $response['data']['rank'] = 'S' . $m[1];
            }

            $impactValue = $extractStat($sintaHtml, 'Impact');
            if ($impactValue) $response['data']['impact'] = $impactValue;

            $gsValue = $extractStat($sintaHtml, 'Google Citations');
            if ($gsValue) $response['data']['citations'] = str_replace([',', '.'], '', $gsValue);

            $scopusValue = $extractStat($sintaHtml, 'Scopus Citations');
            if ($scopusValue) $response['data']['scopus'] = str_replace([',', '.'], '', $scopusValue);

            if (preg_match('/scholar\.google\.com\/citations\?user=([^&"\']+)/i', $sintaHtml, $matches)) {
                $response['data']['scholarId'] = $matches[1];
            }

            if (empty($issn)) {
                if (preg_match('/E-ISSN\s*:\s*(\d{4}-\d{4})/i', $sintaHtml, $matches)) {
                    $issn = $matches[1];
                    $response['data']['eissn'] = $issn;
                } elseif (preg_match('/P-ISSN\s*:\s*(\d{4}-\d{4})/i', $sintaHtml, $matches)) {
                    $issn = $matches[1];
                    $response['data']['eissn'] = $issn;
                }
            } else {
                $response['data']['eissn'] = $issn;
            }

            if (!empty($issn)) {
                $openAlexUrl = "https://api.openalex.org/sources/issn:$issn";
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $openAlexUrl);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch2, CURLOPT_USERAGENT, 'I-Widget-Bot/1.0 (mailto:admin@example.com)');
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
                $alexJson = curl_exec($ch2);
                curl_close($ch2);

                if ($alexJson) {
                    $alexData = json_decode($alexJson, true);
                    if (isset($alexData['cited_by_count'])) {
                        $response['data']['openAlexCitations'] = $alexData['cited_by_count'];
                    }
                }
            }

            $response['success'] = true;
        } else {
            $response['error'] = "Gagal mengambil data dari Sinta. (HTTP $httpCode)";
        }

        return $this->response->setJSON($response);
    }
}
