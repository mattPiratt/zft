	<h1>{$title}</h1>

	<p><a href="{$baseUrl}/index/add">Add new album</a></p>
	<table>
		<tr>
			<th>Title</th>
			<th>Artist</th>
			<th>&nbsp;</th>
		</tr>
		{foreach from=$albums item=album}
		<tr>
			<td>{$album->title}</td>
			<td>{$album->artist}</td>
			<td>
				<a href="{$baseUrl}/index/edit/id/{$album->id}">Edit</a>
				<a href="{$baseUrl}/index/delete/id/{$album->id}">Delete</a>
			</td>
		</tr>
		{/foreach}
	</table>