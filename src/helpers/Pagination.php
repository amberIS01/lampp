<?php

class Pagination
{
    public int $page;
    public int $perPage;
    public int $total;
    public int $pages;

    public function __construct(int $total, int $page = 1, int $perPage = 10)
    {
        $this->total   = $total;
        $this->perPage = $perPage;
        $this->pages   = max(1, (int)ceil($total / $perPage));
        $this->page    = max(1, min($page, $this->pages));
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function hasPrev(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->pages;
    }

    public function buildQuery(int $page): string
    {
        $params = $_GET;
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
}
