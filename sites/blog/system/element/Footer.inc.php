		</section>
		<footer class="footer">
			<div class="related">
				<a class="item" href="http://www.last.fm/user/pomle"><img src="/img/Icon_LastFM.png" alt="Last.fm" height="24" width="24"></a><a class="item" href="http://www.facebook.com/pontus.alexander"><img src="/img/Icon_Facebook.png" alt="Facebook" height="24" width="24"></a><a class="item" href="http://www.youtube.com/user/pomdeterre"><img src="/img/Icon_YouTube.png" alt="YouTube" height="24" width="24"></a>
			</div>
		</footer>
	</div>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>