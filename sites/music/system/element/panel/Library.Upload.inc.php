<h1>Upload</h1>

<?
$UploadForm = new \Element\Upload('/ajax/ReceiveFile.php');

echo $UploadForm;
?>

<script type="text/javascript">
	initDropUpload();
</script>