<?php

declare(strict_types=1);

namespace JorenRothman\WpAdminFilters\Filters;

use WP_Query;

abstract class AbstractFilter
{
    protected string $key;
    protected string $label;
    protected array $args;
    protected string $postType;

    public function __construct(string $key, array $args = [], string $postType)
    {
        $this->key   = $key;
        $this->label = $args['label'] ?? ucfirst($key);
        $this->args  = $args;
        $this->postType = $postType;
    }

    abstract public function render(): void;

    abstract public function apply(WP_Query $query): void;

    protected function getCurrentValue(): string
    {
        return isset($_GET[$this->key])
            ? sanitize_text_field(wp_unslash($_GET[$this->key]))
            : '';
    }
}
