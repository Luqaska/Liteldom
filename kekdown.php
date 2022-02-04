<?php

/**
 * Kekdown - A very basic regex-based Markdown parser. Supports the
 * following elements (and can be extended via Kekdown::add_rule()):
 *
 * Original author: Johnny Broadway <johnny@johnnybroadway.com>
 * Forked by: Lucas Tjor
 *
 * Original:
 * Website: https://gist.github.com/jbroadway/2836900
 * License: MIT
 */
class Kekdown {
	public static $rules = array (
		'/(#+)\s(.*)/' => 'self::header',                           // headers
		'/\!\[([^\[]+)\]\(([^\)]+)\)/' => '<img src=\'\2\' alt=\'\1\'>',  // image
		'/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\'>\1</a>',  // links
		'/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',            // bold
		'/(\*|_)(.*?)\1/' => '<em>\2</em>',                       // emphasis
		'/\~\~(.*?)\~\~/' => '<del>\1</del>',                     // del
		'/\~(.*?)\~/' => '<sub>\1</sub>',                         // subscript
		'/\^(.*?)\^/' => '<sup>\1</sup>',                         // superscript
		'/\:\"(.*?)\"\:/' => '<q>\1</q>',                         // quote
		'/\=\=(.*?)\=\=/' => '<mark>\1</mark>',                   // highlight
		'/`(.*?)`/' => '<code>\1</code>',                         // inline code
		'/\n\*(.*)/' => 'self::ul_list',                          // ul lists
		'/\n\-\s(.*)/' => 'self::ul_list',                          // ul lists (again)
		'/\n[0-9]+\.(.*)/' => 'self::ol_list',                    // ol lists
		'/\n(&gt;|\>)(.*)/' => 'self::blockquote ',               // blockquotes
		'/\n-{5,}/' => "\n<hr />",                                // horizontal rule
		'/\n([^\n]+)\n/' => 'self::para',                         // add paragraphs
		'/<\/ul>\s?<ul>/' => '',                                  // fix extra ul
		'/<\/ol>\s?<ol>/' => '',                                  // fix extra ol
		'/<\/blockquote><blockquote>/' => "\n"                    // fix extra blockquote
	);

	private static function para ($regs) {
		$line = $regs[1];
		$trimmed = trim ($line);
		if (preg_match ('/^<\/?(ul|ol|li|h|p|bl)/', $trimmed)) {
			return "\n" . $line . "\n";
		}
		return sprintf ("\n<p>%s</p>\n", $trimmed);
	}

	private static function ul_list ($regs) {
		$item = $regs[1];
		return sprintf ("\n<ul>\n\t<li>%s</li>\n</ul>", trim ($item));
	}

	private static function ol_list ($regs) {
		$item = $regs[1];
		return sprintf ("\n<ol>\n\t<li>%s</li>\n</ol>", trim ($item));
	}

	private static function blockquote ($regs) {
		$item = $regs[2];
		return sprintf ("\n<blockquote>%s</blockquote>", trim ($item));
	}

	private static function header ($regs) {
		list ($tmp, $chars, $header) = $regs;
		$level = strlen ($chars);
		return sprintf ('<h%d>%s</h%d>', $level, trim ($header), $level);
	}

	/**
	 * Add a rule.
	 */
	public static function add_rule ($regex, $replacement) {
		self::$rules[$regex] = $replacement;
	}

	/**
	 * Render some Markdown into HTML.
	 */
	public static function render ($text) {
		$text = "\n" . $text . "\n";
		foreach (self::$rules as $regex => $replacement) {
			if (is_callable ( $replacement)) {
				$text = preg_replace_callback ($regex, $replacement, $text);
			} else {
				$text = preg_replace ($regex, $replacement, $text);
			}
		}
		return trim ($text);
	}
}