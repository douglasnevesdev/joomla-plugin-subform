<?php
defined('_JEXEC') or die;


$fieldValue = $field->value;

if ($fieldValue === '')
{
	return;
}

// Get the values
$fieldValues = json_decode($fieldValue, true);

if (empty($fieldValues))
{
	return;
}

$html = '<ul>';

foreach ($fieldValues as $value)
{
	$html .= '<li>' . implode(', ', $value) . '</li>';
}

$html .= '</ul>';

echo $html;

