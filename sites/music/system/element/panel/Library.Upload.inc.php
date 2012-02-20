<h1>Upload</h1>

<form action="/ajax/ReceiveFile.php" method="post">
	<div class="dropUpload">

		<div class="dropArea">
			<span><? echo htmlspecialchars(_("Drop Files Here")); ?></span>
		</div>

		<div class="queue">
			<div class="items">

			</div>
		</div>

		<div class="messages">

		</div>

	</div>
</form>

<script type="text/javascript">
	initDropUpload();
</script>