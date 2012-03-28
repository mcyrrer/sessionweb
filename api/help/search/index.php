<?php

session_start();
require_once('../../../include/validatesession.inc');


echo "<center>";
echo "<img src='../../../pictures/dialog-question-large.png' alt=''>";

echo "<h2>Free text search help</h2>";
echo "<p>+</p>

<p>A leading plus sign indicates that this word must be present in each row that is returned.</p>

<p>-</p>

<p>A leading minus sign indicates that this word must not be present in any of the rows that are returned.

Note: The - operator acts only to exclude rows that are otherwise matched by other search terms. Thus, a boolean-mode search that contains only terms preceded by - returns an empty result. It does not return ?all rows except those containing any of the excluded terms.?</p>

<p>(no operator)</p>


<p>By default (when neither + nor - is specified) the word is optional, but the rows that contain it are rated higher. This mimics the behavior of MATCH() ... AGAINST() without the IN BOOLEAN MODE modifier.</p>

<p>> <</p>

<p>These two operators are used to change a word's contribution to the relevance value that is assigned to a row. The > operator increases the contribution and the < operator decreases it. See the example following this list.</p>

<p>( )</p>

<p>Parentheses group words into subexpressions. Parenthesized groups can be nested.</p>

<p>~</p>

<p>A leading tilde acts as a negation operator, causing the word's contribution to the row's relevance to be negative. This is useful for marking ?noise? words. A row containing such a word is rated lower than others, but is not excluded altogether, as it would be with the - operator.</p>

<p>*</p>

<p>The asterisk serves as the truncation (or wildcard) operator. Unlike the other operators, it should be appended to the word to be affected. Words match if they begin with the word preceding the * operator.</p>";
echo "</center>";
?>