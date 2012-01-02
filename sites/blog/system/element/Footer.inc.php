		</div>
		<footer class="footer">
			[ <a href="http://www.last.fm/user/pomle">Last.fm</a> | <a href="http://www.facebook.com/pontus.alexander">Facebook</a> ]
		</footer>
	</div>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>