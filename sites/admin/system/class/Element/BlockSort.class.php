<?
namespace Element;

global $css, $js;
$css[] = '/css/Element/BlockSort.css';
$js[] = '/js/Element/BlockSort.js';

class BlockSort
{
	public function __construct($prefix = 'sortOrder')
	{
		$this->prefix = $prefix;
		$this->items = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="blockSort">
			<div class="items">
				<?
				$i = 0;
				foreach($this->items as $Element)
				{
					?>
					<div class="item">
						<?
						printf('<input class="sortID" type="hidden" name="%s[]" value="%s">', htmlspecialchars($this->prefix), $Element->sortID);
						echo $Element;
						?>
					</div>
					<?
				}
				?>
			</div>
		</div>

		<?
		return ob_get_clean();
	}


	public function addItem(\Element\Common\Root $Element, $id)
	{
		$Element->sortID = $id;
		$this->items[] = $Element;
	}
}