<?php

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

            $css = <<<'CSS'
                /* Make Select2 match WordPress admin dropdowns */
                    .select2-container {
                        margin-right: 6px;
                        width: 100px
                    }

                    .select2-container .select2-selection--single {
                        height: 30px;
                        border-color: #8c8f94;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__clear {
                        height: 26px;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        line-height: 30px;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 28px;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                        background: #fff url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%206l5%205%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23555%22%2F%3E%3C%2Fsvg%3E) no-repeat right 5px top 55%;
                        background-size: 16px;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__arrow b {
                        display: none !important;
                    }
                CSS;

            wp_add_inline_style('select2', $css);
        });
    }
}
