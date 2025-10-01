<?php

use WpAdminFilters\FilterManager;

add_action('plugins_loaded', static function () {
    // Example usage
    $filters = new FilterManager('book');

    $filters->addMetaFilter('isbn', [
        'label' => 'ISBN',
        'key'   => 'isbn',
    ]);

    $filters->addTaxonomyFilter('genre', [
        'label'    => 'Genre',
        'taxonomy' => 'genre',
    ]);

    $filters->addCallbackFilter('has_reviews', [
        'label' => 'Has Reviews',
        'options' => [
            'yes' => 'Yes',
            'no'  => 'No'
        ],
        'apply' => function (\WP_Query $query, $value) {
            if ($value === 'yes') {
                $query->set('meta_query', [[
                    'key'     => 'review_count',
                    'value'   => 0,
                    'compare' => '>',
                ]]);
            }
            if ($value === 'no') {
                $query->set('meta_query', [[
                    'key'     => 'review_count',
                    'value'   => 0,
                    'compare' => '=',
                ]]);
            }
        }
    ]);

    $filters->register();
});
