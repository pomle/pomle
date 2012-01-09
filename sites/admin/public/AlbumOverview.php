<?
#MENUPATH:Innehåll/Album
#URLPATH:AlbumOverview.php
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

$pageTitle = _('Album');

$List = \Element\Antiloop::getAsDomObject('Albums.Edit');

require HEADER;
?>
<div class="tabs">
	<?
	$Tabs = new \Element\Tabs();
	echo $Tabs
		->addTab('overview', _('Översikt'), 'eye')
		->addTab('tools', _('Verktyg'), 'wrench')
		;
	?>
	<div class="tab" id="overview">
		<? echo $List; ?>
	</div>

	<div class="tab" id="tools">
		<?
		$IOCall = new \Element\IOCall('Album');

		echo $IOCall->getHead();
		?>
		<fieldset>
			<legend><? echo \Element\Tag::legend('table_row_insert', _('Import')); ?></legend>

			<fieldset>
				<legend><? echo \Element\Tag::legend('facebook', _('Facebook')); ?></legend>
				<?
				echo \Element\Table::inputs()
					->addRow('Access Token', \Element\Input::text('accessToken')->size(50))
					->addRow('Facebook ID', \Element\Input::text('facebookID')->size(30))
					;

				$Control = new \Element\IOControl($IOCall);
				$Control
					->addButton(\Element\Button::IO('importFacebook', 'add', _('Importera')))
					;

				echo $Control;
				?>
			</fieldset>
		</fieldset>
		<?
		echo $IOCall->getFoot();
		?>
	</div>
</div>
<?
require FOOTER;