<?php

declare(strict_types=1);

namespace JorenRothman\WpAdminFilters\Filters;

use WP_Query;

final class TaxonomyFilter extends AbstractFilter
{
    public function render(): void
    {
        $current = $this->getCurrentValue();
        $taxonomy = $this->args['taxonomy'] ?? $this->key;
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

        echo '<label for="' . esc_attr($this->key) . '" class="screen-reader-text">'
            . esc_html($this->label) . '</label>';

        echo '<select name="' . esc_attr($this->key) . '" id="' . esc_attr($this->key) . '" class="admin-filter-select2">';
        echo '<option value="">' . esc_html($this->label) . '</option>';

        foreach ($terms as $term) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($term->slug),
                selected($current, $term->slug, false),
                esc_html($term->name)
            );
        }

        echo '</select>';
    }

    public function apply(WP_Query $query): void
    {
        $value = $this->getCurrentValue();
        if ($value === '') {
            return;
        }

        $taxQuery = $query->get('tax_query') ?: [];
        $taxQuery[] = [
            'taxonomy' => $this->args['taxonomy'] ?? $this->key,
            'field'    => 'slug',
            'terms'    => $value,
        ];
        $query->set('tax_query', $taxQuery);
    }
}
