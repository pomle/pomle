<?
namespace Element;

class Paginator
{
	public function __construct($pageCurrent = 0, $pageFirst = 0, $pageLast = 0, $baseURL = '?')
	{
		$this->pageCurrent = (int)$pageCurrent;
		$this->pageFirst = (int)$pageFirst;
		$this->pageLast = (int)$pageLast;

		$this->showCurrentPage = false;
		$this->showPageIndex = true;

		$this->baseURL = $baseURL;
	}

	public function __toString()
	{
		ob_start();

		echo '<div class="paginator">';

		if( $this->pageCurrent > $this->pageFirst )
			printf('<a class="prev" href="%spage=%d">%s</a>', $this->baseURL, $this->pageCurrent - 1, '&laquo;');

		if( $this->pageCurrent < $this->pageLast )
			printf('<a class="next" href="%spage=%d">%s</a>', $this->baseURL, $this->pageCurrent + 1, '&raquo;');

		if( $this->showPageIndex )
		{
			echo '<div class="pageIndex">';

			for($page = $this->pageFirst; $page <= $this->pageLast; $page++)
				printf('<a class="pageNumber%s" href="%spage=%d" rel="nofollow">%d</a>', $this->pageCurrent == $page ? ' isCurrent' : '', $this->baseURL, $page, $page);

			echo '</div>';
		}

		if( $this->showCurrentPage )
			printf('<div class="currentPageNumber">%d</div>', $this->pageCurrent);

		echo '</div>';

		return ob_get_clean();
	}
}