<?php

namespace JorenRothman\WpAdminFilters\Filters;

use WP_Query;

final class CallbackFilter extends AbstractFilter
{
    public function render(): void
    {
        $current = $this->getCurrentValue();
        $options = $this->args['options'] ?? [];

        echo '<label for="' . esc_attr($this->key) . '" class="screen-reader-text">'
            . esc_html($this->label) . '</label>';

        echo '<select name="' . esc_attr($this->key) . '" id="' . esc_attr($this->key) . '" class="admin-filter-select2">';
        echo '<option value="">' . esc_html($this->label) . '</option>';

        foreach ($options as $val => $text) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr((string) $val),
                selected($current, (string) $val, false),
                esc_html($text)
            );
        }

        echo '</select>';
    }

    public function apply(WP_Query $query): void
    {
        $value = $this->getCurrentValue();
        if ($value === '' || empty($this->args['apply'])) {
            return;
        }

        call_user_func($this->args['apply'], $query, $value);
    }
}
