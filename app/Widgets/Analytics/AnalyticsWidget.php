<?php

namespace App\Widgets\Analytics;

use App\Widgets\BaseWidget;

class AnalyticsWidget extends BaseWidget
{
    public function render(): string
    {
        $online = setting('stats_online', 0);
        $today = setting('stats_today', 0);
        $days3 = setting('stats_3days', 0);
        $days7 = setting('stats_7days', 0);
        $month = setting('stats_month', 0);
        $year = setting('stats_year', 0);
        $total = setting('stats_total', 0);

        $style = $this->get('style', 'default');
        $showTitle = $this->get('show_title', true);
        $title = $this->get('title', 'Thống kê truy cập');
        $columns = $this->get('columns', 2);

        return view('widgets.analytics.analytics', compact(
            'online', 'today', 'days3', 'days7', 'month', 'year', 'total',
            'style', 'showTitle', 'title', 'columns'
        ))->render();
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Thống kê truy cập',
            'description' => 'Hiển thị số liệu thống kê truy cập website',
            'category' => 'analytics',
            'icon' => 'chart-bar',
            'settings' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Tiêu đề',
                    'default' => 'Thống kê truy cập',
                ],
                'show_title' => [
                    'type' => 'checkbox',
                    'label' => 'Hiển thị tiêu đề',
                    'default' => true,
                ],
                'style' => [
                    'type' => 'select',
                    'label' => 'Kiểu hiển thị',
                    'options' => [
                        'default' => 'Mặc định',
                        'cards' => 'Thẻ card',
                        'compact' => 'Gọn gàng',
                        'modern' => 'Hiện đại',
                    ],
                    'default' => 'default',
                ],
                'columns' => [
                    'type' => 'select',
                    'label' => 'Số cột',
                    'options' => [
                        '1' => '1 cột',
                        '2' => '2 cột',
                        '3' => '3 cột',
                        '4' => '4 cột',
                    ],
                    'default' => '2',
                ],
            ],
        ];
    }

    public function css(): string
    {
        return '
        <style>
        .analytics-widget {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .analytics-widget.style-cards .stat-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .analytics-widget.style-compact .stat-item {
            padding: 0.75rem;
            border-left: 4px solid #3b82f6;
            background: #f8fafc;
        }
        .analytics-widget.style-modern .stat-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }
        .analytics-widget.style-modern .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .stat-number {
            font-size: 1.875rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
            margin-top: 0.25rem;
        }
        </style>';
    }
}
