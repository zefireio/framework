<?php

namespace Zefire\View;

class Compiler
{
	/**
     * Stores the compiled view.
     *
     * @var string
     */
	protected $data;
	/**
     * Stores sections.
     *
     * @var array
     */
	protected $sections = [];
	/**
     * Stores a list of REGEX patterns.
     *
     * @var array
     */
	protected $patterns = [
		'section' 			=> '#@section\((.*?)\)((.+|\n|\r|\t|\s)*?)(.+|\n|\r|\t|\s)@endsection#im',
		'code' 				=> '#@code\((.*?)\)((.+|\n|\r|\t|\s)*?)(.+|\n|\r|\t|\s)@endcode#im',
		'extend' 			=> '#@extends\((.*)\)#',
		'yield' 			=> '#@yield\((.*?)\)#',
		'translate' 		=> '#{!! trans\((.*)\) !!}#',
		'startRawPhp' 		=> '#@php#',
		'endRawPhp' 		=> '#@endphp#',
		'include' 			=> '#@include\((.*)\)#',
		'csrf' 				=> '#@csrf#',
		'startForeachLoop' 	=> '#@foreach\((.*)(as)(.*)\)#',
		'endForeachLoop' 	=> '#@endforeach#',
		'startForLoop' 		=> '#@for\((.*)\)#',
		'endForLoop' 		=> '#@endfor#',
		'startWhileLoop' 	=> '#@while\((.*)\)#',
		'endWhileLoop' 		=> '#@endwhile#',
		'comment' 			=> '#{{-- (.*?) --}}#',
		'var' 				=> '#{{ (.*?) }}#',
		'escapedVar' 		=> '#{!! (.*?) !!}#',
		'startIfStatement' 	=> '#@if(.*)#',
		'elseStatement' 	=> '#@else#',
		'elseIfStatement' 	=> '#@elseif\((.*?)\)#',
		'endIfStatement' 	=> '#@endif#',
		'runtime' 			=> '#@runtime#'
	];
	/**
     * Compiles a template into a view.
     *
     * @param  string $html
     * @param  mixed  $data
     * @return string
     */
	public function make($html, $data = false)
	{
		$this->data = $data;
		$html = $this->section($html);		
		$html = $this->code($html);
		$html = $this->extends($html);
		$html = $this->yield($html);
		$html = $this->translate($html);
		$html = $this->include($html);
		$html = $this->csrf($html);
		$html = $this->comment($html);
		$html = $this->startRawPhp($html);
		$html = $this->endRawPhp($html);
		$html = $this->var($html);
		$html = $this->escapedVar($html);
		$html = $this->startIfStatement($html);
		$html = $this->elseStatement($html);
		$html = $this->elseIfStatement($html);
		$html = $this->endIfStatement($html);
		$html = $this->startForeachLoop($html);
		$html = $this->endForeachLoop($html);
		$html = $this->startForLoop($html);
		$html = $this->endForLoop($html);
		$html = $this->startWhileLoop($html);
		$html = $this->endWhileLoop($html);
		$html = $this->handleSpecialVar($html);		
		return $html;
	}
	/**
     * Finds all code special characters directives and repalces content with html entites.
     *
     * @param  string $html
     * @return string
     */
	protected function handleSpecialVar($html)
	{
		$html = str_replace('[at]', '&commat;', $html);
		$html = str_replace('[openvar]', '&lcub;&lcub;', $html);
		$html = str_replace('[closevar]', '&rcub;&rcub;', $html);
		$html = str_replace('[openrawvar]', '&lcub;&excl;&excl;', $html);
		$html = str_replace('[closerawvar]', '&excl;&excl;&rcub;', $html);
		$html = str_replace('[php]', '&lt;&quest;php', $html);
		return $html;
	}
	/**
     * Finds all code directives and repalces content with escaped characters.
     *
     * @param  string $html
     * @return string
     */
	protected function code($html)
	{
		preg_match_all($this->patterns['code'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace("@code(" . $matches[1][$key] . ")", '<pre><code class="' . $this->strip($matches[1][$key]) . '">', $html);
			$html = str_replace($matches[2][$key], htmlspecialchars($matches[2][$key]), $html);
			$html = str_replace("@endcode", '</code></pre>', $html);
		}
		return $html;
	}
	/**
     * Finds all section directives and stores them
     * temporarily for future evaluation and re-injection.
     *
     * @param  string $html
     * @return string
     */
	protected function section($html)
	{
		preg_match_all($this->patterns['section'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$this->sections[$this->strip($matches[1][$key])] = $matches[2][$key];	
			$html = str_replace($value, '', $html);
		}
		return $html;
	}	
	/**
     * Finds all extend directives and imports the
     * relative view to evaluate and inject it.
     *
     * @param  string $html
     * @return string
     */
	protected function extends($html)
	{
		preg_match_all($this->patterns['extend'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, $this->make(file_get_contents(\App::templatePath() . $this->toPath($matches[1][$key]) . '.php')), $html);
		}
		return $html;		
	}
	/**
     * Finds all yield directives and imports the
     * relative view to evaluate and inject it.
     *
     * @param  string $html
     * @return string
     */
	protected function yield($html)
	{
		preg_match_all($this->patterns['yield'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, $this->make($this->getSection($this->strip($matches[1][$key]))), $html);
		}
		return $html;
	}	
	/**
     * Finds all translate directives and retrieves the relevant translation.
     *
     * @param  string $html
     * @return string
     */
	protected function translate($html)
	{
		preg_match_all($this->patterns['translate'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, \Translate::get($this->strip($matches[1][$key])), $html);
		}
		return $html;		
	}	
	/**
     * Opens a PHP script tag.
     *
     * @param  string $html
     * @return string
     */
	protected function startRawPhp($html)
	{
		return preg_replace($this->patterns['startRawPhp'], '<?php ', $html);
	}
	/**
     * closes a PHP script tag.
     *
     * @param  string $html
     * @return string
     */
	protected function endRawPhp($html)
	{
		return preg_replace($this->patterns['endRawPhp'], ' ?>', $html);
	}
	/**
     * Finds all include directives and injects the relevant partial.
     *
     * @param  string $html
     * @return string
     */
	protected function include($html)
	{
		preg_match_all($this->patterns['include'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, $this->make(file_get_contents(\App::templatePath() . $this->toPath($matches[1][$key]) . '.php')), $html);
		}
		return $html;		
	}
	/**
     * Finds all csrf directives and injects a hidden input with CSRF token.
     *
     * @param  string $html
     * @return string
     */
	protected function csrf($html)
	{
		return preg_replace($this->patterns['csrf'], '<input type="hidden" name="X-CSRF-TOKEN" value="'. \Session::get('XSRF-TOKEN') .'">', $html);
	}	
	/**
     * Starts a foreach loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function startForeachLoop($html)
	{
		preg_match_all($this->patterns['startForeachLoop'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php foreach (' . $matches[1][$key] . ' as ' . $matches[3][$key] . '): ?>', $html);
		}
		return $html;		
	}
	/**
     * Ends a foreach loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function endForeachLoop($html)
	{
		return preg_replace($this->patterns['endForeachLoop'], '<?php endforeach; ?>', $html);		
	}
	/**
     * Starts a for loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function startForLoop($html)
	{
		preg_match_all($this->patterns['startForLoop'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php for (' . $matches[1][$key] . '): ?>', $html);
		}
		return $html;		
	}
	/**
     * Ends a for loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function endForLoop($html)
	{
		return preg_replace($this->patterns['endForLoop'], '<?php endfor; ?>', $html);		
	}
	/**
     * Starts a while loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function startWhileLoop($html)
	{
		preg_match_all($this->patterns['startWhileLoop'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php while (' . $matches[1][$key] . '): ?>', $html);
		}
		return $html;		
	}
	/**
     * Ends a while loop php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function endWhileLoop($html)
	{
		return preg_replace($this->patterns['endWhileLoop'], '<?php endwhile; ?>', $html);		
	}
	/**
     * Finds all comments directives and converts them to HTML comments.
     *
     * @param  string $html
     * @return string
     */
	protected function comment($html)
	{
		preg_match_all($this->patterns['comment'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<!-- ' . $matches[1][$key] . ' -->', $html);
		}
		return $html;
	}
	/**
     * Evaluates PHP variables.
     *
     * @param  string $html
     * @return string
     */
	protected function var($html)
	{
		preg_match_all($this->patterns['var'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php echo ' . $matches[1][$key] . '; ?>', $html);
		}
		return $html;
	}
	/**
     * Evaluates escaped PHP variables.
     *
     * @param  string $html
     * @return string
     */
	protected function escapedVar($html)
	{
		preg_match_all($this->patterns['escapedVar'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php echo ' . htmlspecialchars($matches[1][$key]) . '; ?>', $html);
		}
		return $html;
	}
	/**
     * Starts an if statement php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function startIfStatement($html)
	{
		preg_match_all($this->patterns['startIfStatement'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php if (' . $matches[1][$key] . '): ?>', $html);
		}
		return $html;		
	}
	/**
     * Evaluates an else statement php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function elseStatement($html)
	{
		return preg_replace($this->patterns['elseStatement'], '<?php else: ?>', $html);		
	}
	/**
     * Evaluates an elseif statement php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function elseIfStatement($html)
	{
		preg_match_all($this->patterns['elseIfStatement'], $html, $matches);
		foreach ($matches[0] as $key => $value) {
			$html = str_replace($value, '<?php elseif(' . $matches[1][$key] . '): ?>', $html);
		}
		return $html;
	}
	/**
     * Ends an if statement php tag.
     *
     * @param  string $html
     * @return string
     */
	protected function endIfStatement($html)
	{
		return preg_replace($this->patterns['endIfStatement'], '<?php endif; ?>', $html);		
	}
	/**
     * Strips single quotes in a path.
     *
     * @param  string $path
     * @return string
     */
	protected function strip($path)
    {
        return str_replace("'", "", $path);
    }
    /**
     * Validates a file path.
     *
     * @param  string $filename
     * @return string
     */
    protected function toPath($filename)
	{
		return str_replace("'", "", str_replace('.', '/', $filename));
	}
	/**
     * Gets a section form compiler.
     *
     * @param  string $key
     * @return string
     */
	protected function getSection($key)
	{
		return (isset($this->sections[$key])) ? $this->sections[$key] : null;
	}
}