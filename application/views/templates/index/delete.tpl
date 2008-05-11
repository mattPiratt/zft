	<h1>{$title}</h1>

	{if $album->id > 0 }
	<form action="{$baseUrl}/index/delete" method="post">
		<p>Are you sure that you want to delete "{$album->title}" by
			"{$album->artist}" ?</p>
		<div>
			<input type="hidden" name="id" value="{$album->id}" />
			<input type="submit" name="del" value="Yes" />
			<input type="submit" name="del" value="No" />
		</div>
	</form>
	{else}
	<p>Cannot find album.</p>
	{/if}