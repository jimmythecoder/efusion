{* Delete Product Review *}
{assign var="page_title" value="Delete a product review"}

<p>Please confirm that you wish to delete the following product review...</p>

<ol id="user-submitted-reviews">
<li>
	<p class="review-comment">{$review.comment|escape:"html"}</p>
	<small>Reviewed {$review.reviewed_at|history_date_format}</small>
</li>
</ol>


<form action="admin/reviews/delete/{$review.id}" method="post">
	<input type="submit" name="delete" value="Confirm Delete" />
	<input type="submit" name="cancel" value="Cancel" />
</form>