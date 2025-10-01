<?php

declare(strict_types=1);

namespace JorenRothman\WpAdminFilters\Filters;

use WP_Query;

final class MetaFilter extends AbstractFilter
{
    public function render(): void
    {
        $current = $this->getCurrentValue();
        $options = $this->args['options'] ?? $this->discoverOptions();

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
        if ($value === '') {
            return;
        }

        $metaQuery = $query->get('meta_query') ?: [];
        $metaQuery[] = [
            'key'     => $this->args['key'] ?? $this->key,
            'value'   => $value,
            'compare' => '=',
        ];
        $query->set('meta_query', $metaQuery);
    }

    private function discoverOptions(): array
    {
        global $wpdb;
        $key = $this->args['key'] ?? $this->key;

        $results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s ORDER BY meta_value ASC",
                $key
            )
        );

        $values = [];

        foreach ($results as $raw) {
            $maybeUnserialized = maybe_unserialize($raw);

            if (is_array($maybeUnserialized)) {
                foreach ($maybeUnserialized as $val) {
                    $values[] = (string) $val;
                }
            } else {
                $values[] = (string) $maybeUnserialized;
            }
        }

        $values = array_unique($values);

        // Resolve post titles if requested
        if (!empty($this->args['resolve_posts'])) {
            $resolved = [];

            foreach ($values as $id) {
                if (!$id) {
                    continue;
                }

                $post = get_post((int) $id);

                if ($post) {
                    $resolved[$id] = $post->post_title;
                }
            }

            sort($resolved);

            return $resolved;
        }

        return array_combine($values, $values);
    }
}
