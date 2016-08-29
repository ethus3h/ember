<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package fm-wp-theme
 */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="/m.css" rel="stylesheet" type="text/css">
    <meta content="Ember" name="author">
    <meta content="width=device-width, height=device-height, user-scalable=yes" name="viewport">
    <title><?php if(is_front_page()) { echo "Ember Blog"; } else { wp_title( '—', true, 'right' ); } ?></title>
</head>
<body>
	<input class="nav-trigger" id="nav-trigger" type="checkbox">
	<label for="nav-trigger">&nbsp;</label>
	<nav>
		<p class="logo">
			<a class="nodecorate logolink" href=
			"/">ember</a>
		</p>
		<ul>
            <li>Navigation:</li>
			<li class="nav-item nav-item-inactive index">
				<a href="/">Home</a>
			</li>
			<li class="nav-item nav-item-active news">
				<a href="/news">News</a>
			</li>
            <li class="nav-item nav-item-inactive ready-to-use">
				<a href="/ancillary/ready-to-use.htm">Ready-to-use software</a>
			</li>			<li class="nav-item nav-item-inactive specification">
				<a href="/specification">Core: Specification</a>
			</li>
			<li class="nav-item nav-item-inactive community">
				<a href="/community">Core: Community</a>
			</li>
			<li class="nav-item nav-item-inactive computing">
				<a href="/computing">Core: Computing</a>
			</li>
			<li class="nav-item nav-item-inactive library">
				<a href="/library">Core: Library</a>
			</li>
            <li class="nav-item nav-item-inactive work-in-progress">
				<a href="/ancillary/work-in-progress.htm">Other work in progress</a>
			</li>
			<li class="nav-item nav-item-inactive ember">
				<a href="/people">People</a>
			</li>
		</ul>
		<a href="#">&#x1F51D;&#xFE0E;</a>
	</nav>
	<main>
