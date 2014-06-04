<h2>Picking / Production List (2013-11-07 - 2013-11-07)</h2>

<table>
<tr>
	<th>Product</th>
	<th>Quantity</th>
</tr>
	{foreach from=$products key=key item=product}
		<tr>
			<td>{$product.product}</td>
			<td>{$product.quantity}</td>
		</tr>
	{/foreach}
</table>