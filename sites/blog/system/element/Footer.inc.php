	</section>
	<footer class="footer pageContainer">
		<div class="related">
			<a class="item" href="http://www.last.fm/user/pomle"><img src="/img/Icon_LastFM.png" alt="Last.fm" height="24" width="24"></a><a class="item" href="http://www.facebook.com/pontus.alexander"><img src="/img/Icon_Facebook.png" alt="Facebook" height="24" width="24"></a>
		</div>
	</footer>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>