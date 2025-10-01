<?php declare(strict_types=1);

namespace JorenRothman\WpAdminFilters;

use JorenRothman\WpAdminFilters\Filters\AbstractFilter;
use JorenRothman\WpAdminFilters\Filters\CallbackFilter;
use JorenRothman\WpAdminFilters\Filters\MetaFilter;
use JorenRothman\WpAdminFilters\Filters\TaxonomyFilter;
use WP_Query;

final class FilterManager
{
    private string $postType;
    /** @var AbstractFilter[] */
    private array $filters = [];

    public function __construct(string $postType)
    {
        $this->postType = $postType;
    }

    /**
     * @param string $key
     * @param array $args
     * @return FilterManager
     */
    public function addMetaFilter(string $key, array $args = []): self
    {
        $this->filters[] = new MetaFilter($key, $this->postType, $args);
        return $this;
    }

    public function addTaxonomyFilter(string $key, array $args = []): self
    {
        $this->filters[] = new TaxonomyFilter($key, $this->postType, $args);
        return $this;
    }

    public function addCallbackFilter(string $key, array $args = []): self
    {
        $this->filters[] = new CallbackFilter($key, $this->postType, $args);
        return $this;
    }

    public function register(): void
    {
        add_action('restrict_manage_posts', fn() => $this->renderFilters(), 5);
        add_action('pre_get_posts', fn(WP_Query $query) => $this->applyFilters($query));
        Assets::register();
    }

    private function renderFilters(): void
    {
        global $typenow;
        if ($typenow !== $this->postType) {
            return;
        }

        foreach ($this->filters as $filter) {
            $filter->render();
        }
    }

    private function applyFilters(WP_Query $query): void
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $screen = get_current_screen();
        if ($screen === null || $screen->post_type !== $this->postType) {
            return;
        }

        foreach ($this->filters as $filter) {
            $filter->apply($query);
        }
    }
}
