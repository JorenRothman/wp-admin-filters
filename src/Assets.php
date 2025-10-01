<?php

declare(strict_types=1);

namespace JorenRothman\WpAdminFilters;

final class Assets
{
    public static function register(): void
    {
        add_action('admin_enqueue_scripts', static function (): void {
            wp_enqueue_style(
                'select2',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                [],
                '4.1.0'
            );

            wp_enqueue_script(
                'select2',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ['jquery'],
                '4.1.0',
                true
            );

            $js = <<<'JS'
jQuery(function ($) {
    $('select.admin-filter-select2').select2({
        width: 'resolve',
        allowClear: true,
        placeholder: function() {
            return $(this).data('placeholder') || 'Filter...';
        },
        dropdownParent: $('.wrap')
    });
});
JS;

            wp_add_inline_script('select2', $js);
        });
    }
}
