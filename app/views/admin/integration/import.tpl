{* Import data *}
{assign var="page_title" value="Import data"}

<p>Please select what you would like to upload and the format the data is in</p>

<form action="admin/integration/import" enctype="multipart/form-data" method="post">
Import: 
<label><input {if empty($smarty.post.what) OR $smarty.post.what == "products"}checked="checked"{/if} type="radio" value="products" name="what" />Products</label>
<label><input {if $smarty.post.what == "categories"}checked="checked"{/if} type="radio" value="categories" name="what" />Categories</label>

<br />
From a: 
<label><input {if $smarty.post.format != "csv"}checked="checked"{/if} type="radio" value="xml" name="format" />XML</label>
<label><input {if $smarty.post.format == "csv"}checked="checked"{/if} type="radio" value="csv" name="format" />CSV</label>
 file

<br />
<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
Data file: <input type="file" name="import_data" />

<br /><br />
<input type="submit" name="import" value="Import" />
</form>