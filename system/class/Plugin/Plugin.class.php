<?
namespace Plugin;

interface IPlugin
{
	public function getHTML();
}

abstract class Plugin implements IPlugin
{
	final public function __construct($tag)
	{
		$this->source = $tag;

		if( preg_match_all('%([A-Za-z]+)="(.*)"%U', $tag, $attributes) )
			foreach($attributes[0] as $index => $attr)
				$this->$attributes[1][$index] = $attributes[2][$index];
	}
}