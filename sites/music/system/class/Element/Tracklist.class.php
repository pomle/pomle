<?
namespace Element;

class Tracklist extends UserTrackList
{
	public function __toString()
	{
		ob_start();
		?>
		<div class="userTrackList tracklist">

			<div class="control">
				<a href="#" class="item play"><img src="/img/Library_Icon_PlayQueueReplace.png" title="<? echo htmlspecialchars(_('Replace queue and start playing')); ?>"></a>
				<a href="#" class="item append"><img src="/img/Library_Icon_PlayQueueAppend.png" title="<? echo htmlspecialchars(_('Append to queue')); ?>"></a>
			</div>

			<?
			echo $this->getItemsHTML($this->userTrackItems);
			?>

		</div>
		<?
		return ob_get_clean();
	}
}