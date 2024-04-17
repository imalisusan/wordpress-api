<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;

trait CanPaginate
{
    protected ?int $totalNumberOfEntities;

    protected int $currentPaginationPage;

    protected int $itemsPerPage;

    protected int $totalNumberOfPages;

    public function getTotalNumberOfPages(): int
    {
        $itemsPerPage = $this->itemsPerPage;

        if (empty($itemsPerPage)) {
            $this->itemsPerPage = config('app.items_per_page');
        }

        $this->totalNumberOfPages = intval(ceil(intval($this->totalNumberOfEntities) / $itemsPerPage));

        return $this->totalNumberOfPages ?? 0;
    }

    /**
     * Next page
     */
    public function nextPage(): int
    {
        $itemsPerPage = $this->itemsPerPage;

        if (empty($itemsPerPage)) {
            $this->itemsPerPage = config('app.items_per_page');
        }

        if (empty($this->currentPaginationPage)) {
            $this->currentPaginationPage = 1;
        }

        $pages = intval(ceil(intval($this->totalNumberOfEntities) / $itemsPerPage));

        if ($pages === $this->currentPaginationPage) {
            return $this->currentPaginationPage;
        } elseif ($pages > $this->currentPaginationPage) {
            $nextPage = $this->currentPaginationPage + 1;

            return min($nextPage, $pages);
        } elseif ($pages < $this->currentPaginationPage) {
            return 2;
        }

        return 1;
    }

    /**
     * Previous page
     */
    public function previousPage(): int
    {
        $itemsPerPage = $this->itemsPerPage;

        if (empty($itemsPerPage)) {
            $this->itemsPerPage = config('app.items_per_page');
        }

        if (empty($this->currentPaginationPage)) {
            $this->currentPaginationPage = 1;
        }

        $pages = intval(ceil(intval($this->totalNumberOfEntities) / $itemsPerPage));

        if ($this->currentPaginationPage === 1) {
            return $this->currentPaginationPage;
        }

        if ($pages === $this->currentPaginationPage) {
            return $this->currentPaginationPage - 1;
        } elseif ($pages > $this->currentPaginationPage) {
            return $this->currentPaginationPage - 1;
        } else {
            return 1;
        }
    }

    /**
     * Get the current page
     */
    public function getCurrentPage(Request $request): int
    {
        $page = $request->input('page', 1);
        return intval($page) > 0 ? intval($page) : 1;
    }

    /**
     * Make record number
     */
    public function makeRecordNumber(int $recordIndex, int $currentPage, int $numberOfItemsPerPage): float|int
    {
        $startPoint = $currentPage === 1 ? 1 : (($numberOfItemsPerPage * ($currentPage - 1) + 1));

        if ($recordIndex === 0 && $currentPage > 1) {
            return $startPoint;
        }

        return $startPoint + $recordIndex;
    }

    /**
     * Make current page number
     */
    public function makeCurrentPageNumber(int $requestCurrentPage): int
    {
        if ($this->totalNumberOfEntities <= $this->itemsPerPage) {
            return 1;
        }

        if ($this->getTotalNumberOfPages() < $requestCurrentPage) {
            return 1;
        }

        return $requestCurrentPage;
    }
}
