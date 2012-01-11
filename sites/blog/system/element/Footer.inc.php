		</section>
		<footer class="footer">
			<div class="social">
				<a class="item lastfm" href="http://www.last.fm/user/pomle"></a>
				<a class="item songkick" href="http://www.songkick.com/users/Pomle"></a>
				<a class="item facebook" href="http://www.facebook.com/pontus.alexander"></a>
				<a class="item youtube" href="http://www.youtube.com/user/pomdeterre"></a>
			</div>
		</footer>
	</div>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>