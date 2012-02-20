<?
require '../Init.inc.php';

require HEADER;
?>
<section id="library">

</section>

<section id="playlists">

	<div class="tabs">

		<div class="index">
			<a class="tab" href="#" data-panel="activePlaylist"><? echo _('Active'); ?></a>
			<a class="tab" href="#" data-panel="playlistBrowser"><? echo _('Browse'); ?></a>
		</div>

		<div class="panels">
			<div class="tab" id="activePlaylist">
				<?
				$Fetch = new \Fetch\UserTrack($User);

				$userTracks = $Fetch->getRecent();

				$Playlist = \Element\Playlist::createFromUserTracks($userTracks);

				echo $Playlist;
				?>
			</div>

			<div class="tab" id="playlistBrowser">

			</div>
		</div>

	</div>

</section>
<?
require FOOTER;