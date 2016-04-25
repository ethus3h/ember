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
    <meta content="Futuramerlin" name="author">
    <meta content="width=device-width, height=device-height, user-scalable=yes" name="viewport">
    <title><?php if(is_front_page()) { echo "Futuramerlin Blog"; } else { wp_title( '—', true, 'right' ); } ?></title>
</head>
<body>
	<input class="nav-trigger" id="nav-trigger" type="checkbox">
	<label for="nav-trigger">&nbsp;</label>
	<nav>
		<p class="logo">
			<a class="nodecorate logolink" href=
			"/">futuramerlin</a>
		</p>
		<ul>
			<li>Navigation:</li>
			<li class="nav-item nav-item-inactive index">
				<a href="/">Home</a>
			</li>
			<li class="nav-item nav-item-inactive bio">
				<a href="/bio.htm">Bio</a>
			</li>
			<li class="nav-item nav-item-inactive news">
				<a href="/c/category/futuramerlin-news/">News</a>
			</li>
			<li class="nav-item nav-item-selected blog">
				<a href="/c/">Blog</a>
			</li>
			<li class="nav-item nav-item-inactive contact">
				<a href="/contact.htm">Contact</a>
			</li>
			<li class="nav-item nav-item-inactive resume">
				<a href="/resume.htm">Résumé</a>
			</li>
			<li class="nav-item nav-item-inactive ember">
				<a href="/ember">Project: Ember</a>
			</li>
			<li class="nav-item nav-item-inactive music">
				<a href="/music">Music</a>
			</li>
		</ul>
	</nav>
	<main>