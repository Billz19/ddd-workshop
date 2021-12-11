<?php

namespace App\Library\Request;

use App\Packages\Exceptions\InvalidArgumentError;
use JetBrains\PhpStorm\ArrayShape;

trait PaginationQuery {

    /**
     * @var int
     */
    private int $page = 1;

    /**
     * @var int
     */
    private int $perPage = 20;

    /**
     * @var int
     */
    private int $start;

    /**
     * @var int
     */
    private int $limit;

    /**
     * @var int
     */
    private int $count;

    /**
     * return @PaginationQuery $start
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * set $start to the @PaginationQuery
     */
    private function setStart(int $start): void
    {
        if ($start < 0 || (isset($this->count) && $start > $this->count)) {
            throw new InvalidArgumentError(message: 'Invalid range supplied');
        }
        $this->start = $start;
    }

    /**
     * return @PaginationQuery $limit
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * set $limit to the @PaginationQuery
     */
    private function setLimit(int $limit = 20): void
    {
        if ($limit > $this->count) {
            $limit = $this->count;
        }
        $this->limit = $limit;
    }

    /**
     * return @PaginationQuery $perPage
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * set $perPage to the @PaginationQuery
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * return @PaginationQuery $page
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * set $page to the @PaginationQuery
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * set $count, $start and $limit to the @PaginationQuery
     * check validation of the pagination range
     */
    public function configurePagination(int $count)
    {
        $this->count = $count;
        // setting pagination start
        $this->setStart(($this->page - 1) * ($this->perPage));
        // setting pagination limit
        $this->setLimit( $this->start + $this->perPage);
    }

    /**
     * calculate and return @PaginationQuery total pages
     */
    public function getTotalPages(): int
    {
        return (int)($this->getTotal() / $this->perPage);
    }

    /**
     * calculate and return @PaginationQuery total items
     */
    public function getTotal(): int
    {
        return $this->count ?: 1;
    }

    /**
     * calculate and return @PaginationQuery next page
     */
    public function getNextPage(): int
    {
        return $this->page < $this->getTotalPages() ? $this->page + 1 : $this->page;
    }

    /**
     * calculate and return @PaginationQuery previous page
     */
    public function getPrevPage(): int
    {
        return ($this->page - 1) ?: 1;
    }

}
