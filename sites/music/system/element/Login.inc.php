<?
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

require HEADER;
?>

<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">

	<div class="message"><? if( isset($_POST['login']) ) echo _('Login failed'); ?></div>

	<table>
		<tr>
			<td><? echo _('Username'); ?></td>
			<td><? echo \Element\Input::text('username', $username); ?></td>
		</tr>
		<tr>
			<td><? echo _('Password'); ?></td>
			<td><? echo \Element\Input::password('password')->addAttr('autocomplete', 'off'); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="control">
				<button type="submit" name="login" value="1"><? echo _('Login'); ?></button>
			</td>
		</tr>
	</table>
</form>

<?
require FOOTER;

die();