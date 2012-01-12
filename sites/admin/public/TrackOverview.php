<?
#MENUPATH:Innehåll/Hits
define('ACCESS_POLICY', 'AllowViewTrack');

require '../Init.inc.php';

$pageTitle = _('Hits');

$List = \Element\Antiloop::getAsDomObject('Tracks.Edit');

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
		$IOCall = new \Element\IOCall('Track');

		echo $IOCall->getHead();
		?>
		<fieldset>
			<legend><? echo \Element\Tag::legend('table_row_insert', _('Import')); ?></legend>

			<fieldset>
				<legend><? echo \Element\Tag::legend('lastfm', _('Last FM')); ?></legend>
				<?
				echo \Element\Table::inputs()
					->addRow('Username', \Element\Input::text('username')->size(20))
					;

				$Control = new \Element\IOControl($IOCall);
				$Control
					->addButton(\Element\Button::IO('importLastFM', 'add', _('Importera')))
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