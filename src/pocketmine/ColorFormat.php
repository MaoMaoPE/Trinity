<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine;

use pocketmine\utils\Terminal;

/**
 * Class used to handle Minecraft chat format, and convert it to other formats like ANSI or HTML
 */
abstract class ColorFormat {
	const ESCAPE = "\xc2\xa7"; //§

	const BLACK = ColorFormat::ESCAPE . "0";
	const DARK_BLUE = ColorFormat::ESCAPE . "1";
	const DARK_GREEN = ColorFormat::ESCAPE . "2";
	const DARK_AQUA = ColorFormat::ESCAPE . "3";
	const DARK_RED = ColorFormat::ESCAPE . "4";
	const DARK_PURPLE = ColorFormat::ESCAPE . "5";
	const GOLD = ColorFormat::ESCAPE . "6";
	const GRAY = ColorFormat::ESCAPE . "7";
	const DARK_GRAY = ColorFormat::ESCAPE . "8";
	const BLUE = ColorFormat::ESCAPE . "9";
	const GREEN = ColorFormat::ESCAPE . "a";
	const AQUA = ColorFormat::ESCAPE . "b";
	const RED = ColorFormat::ESCAPE . "c";
	const LIGHT_PURPLE = ColorFormat::ESCAPE . "d";
	const YELLOW = ColorFormat::ESCAPE . "e";
	const WHITE = ColorFormat::ESCAPE . "f";

	const OBFUSCATED = ColorFormat::ESCAPE . "k";
	const BOLD = ColorFormat::ESCAPE . "l";
	const STRIKETHROUGH = ColorFormat::ESCAPE . "m";
	const UNDERLINE = ColorFormat::ESCAPE . "n";
	const ITALIC = ColorFormat::ESCAPE . "o";
	const RESET = ColorFormat::ESCAPE . "r";

	/**
	 * Splits the string by Format tokens
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public static function tokenize($string){
		return preg_split("/(" . ColorFormat::ESCAPE . "[0123456789abcdefklmnor])/", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	}

	/**
	 * Cleans the string from Minecraft codes and ANSI Escape Codes
	 *
	 * @param string $string
	 * @param bool   $removeFormat
	 *
	 * @return mixed
	 */
	public static function clean($string, $removeFormat = true){
		if($removeFormat){
			return str_replace(ColorFormat::ESCAPE, "", preg_replace(["/" . ColorFormat::ESCAPE . "[0123456789abcdefklmnor]/", "/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/"], "", $string));
		}
		return str_replace("\x1b", "", preg_replace("/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/", "", $string));
	}

	/**
	 * Returns an JSON-formatted string with colors/markup
	 *
	 * @param string|array $string
	 *
	 * @return string
	 */
	public static function toJSON($string){
		if(!is_array($string)){
			$string = self::tokenize($string);
		}
		$newString = [];
		$pointer =& $newString;
		$color = "white";
		$bold = false;
		$italic = false;
		$underlined = false;
		$strikethrough = false;
		$obfuscated = false;
		$index = 0;

		foreach($string as $token){
			if(isset($pointer["text"])){
				if(!isset($newString["extra"])){
					$newString["extra"] = [];
				}
				$newString["extra"][$index] = [];
				$pointer =& $newString["extra"][$index];
				if($color !== "white"){
					$pointer["color"] = $color;
				}
				if($bold !== false){
					$pointer["bold"] = true;
				}
				if($italic !== false){
					$pointer["italic"] = true;
				}
				if($underlined !== false){
					$pointer["underlined"] = true;
				}
				if($strikethrough !== false){
					$pointer["strikethrough"] = true;
				}
				if($obfuscated !== false){
					$pointer["obfuscated"] = true;
				}
				++$index;
			}
			switch($token){
				case ColorFormat::BOLD:
					if($bold === false){
						$pointer["bold"] = true;
						$bold = true;
					}
					break;
				case ColorFormat::OBFUSCATED:
					if($obfuscated === false){
						$pointer["obfuscated"] = true;
						$obfuscated = true;
					}
					break;
				case ColorFormat::ITALIC:
					if($italic === false){
						$pointer["italic"] = true;
						$italic = true;
					}
					break;
				case ColorFormat::UNDERLINE:
					if($underlined === false){
						$pointer["underlined"] = true;
						$underlined = true;
					}
					break;
				case ColorFormat::STRIKETHROUGH:
					if($strikethrough === false){
						$pointer["strikethrough"] = true;
						$strikethrough = true;
					}
					break;
				case ColorFormat::RESET:
					if($color !== "white"){
						$pointer["color"] = "white";
						$color = "white";
					}
					if($bold !== false){
						$pointer["bold"] = false;
						$bold = false;
					}
					if($italic !== false){
						$pointer["italic"] = false;
						$italic = false;
					}
					if($underlined !== false){
						$pointer["underlined"] = false;
						$underlined = false;
					}
					if($strikethrough !== false){
						$pointer["strikethrough"] = false;
						$strikethrough = false;
					}
					if($obfuscated !== false){
						$pointer["obfuscated"] = false;
						$obfuscated = false;
					}
					break;

				//Colors
				case ColorFormat::BLACK:
					$pointer["color"] = "black";
					$color = "black";
					break;
				case ColorFormat::DARK_BLUE:
					$pointer["color"] = "dark_blue";
					$color = "dark_blue";
					break;
				case ColorFormat::DARK_GREEN:
					$pointer["color"] = "dark_green";
					$color = "dark_green";
					break;
				case ColorFormat::DARK_AQUA:
					$pointer["color"] = "dark_aqua";
					$color = "dark_aqua";
					break;
				case ColorFormat::DARK_RED:
					$pointer["color"] = "dark_red";
					$color = "dark_red";
					break;
				case ColorFormat::DARK_PURPLE:
					$pointer["color"] = "dark_purple";
					$color = "dark_purple";
					break;
				case ColorFormat::GOLD:
					$pointer["color"] = "gold";
					$color = "gold";
					break;
				case ColorFormat::GRAY:
					$pointer["color"] = "gray";
					$color = "gray";
					break;
				case ColorFormat::DARK_GRAY:
					$pointer["color"] = "dark_gray";
					$color = "dark_gray";
					break;
				case ColorFormat::BLUE:
					$pointer["color"] = "blue";
					$color = "blue";
					break;
				case ColorFormat::GREEN:
					$pointer["color"] = "green";
					$color = "green";
					break;
				case ColorFormat::AQUA:
					$pointer["color"] = "aqua";
					$color = "aqua";
					break;
				case ColorFormat::RED:
					$pointer["color"] = "red";
					$color = "red";
					break;
				case ColorFormat::LIGHT_PURPLE:
					$pointer["color"] = "light_purple";
					$color = "light_purple";
					break;
				case ColorFormat::YELLOW:
					$pointer["color"] = "yellow";
					$color = "yellow";
					break;
				case ColorFormat::WHITE:
					$pointer["color"] = "white";
					$color = "white";
					break;
				default:
					$pointer["text"] = $token;
					break;
			}
		}

		if(isset($newString["extra"])){
			foreach($newString["extra"] as $k => $d){
				if(!isset($d["text"])){
					unset($newString["extra"][$k]);
				}
			}
		}

		return json_encode($newString, JSON_UNESCAPED_SLASHES);
	}

	/**
	 * Returns an HTML-formatted string with colors/markup
	 *
	 * @param string|array $string
	 *
	 * @return string
	 */
	public static function toHTML($string){
		if(!is_array($string)){
			$string = self::tokenize($string);
		}
		$newString = "";
		$tokens = 0;
		foreach($string as $token){
			switch($token){
				case ColorFormat::BOLD:
					$newString .= "<span style=font-weight:bold>";
					++$tokens;
					break;
				case ColorFormat::OBFUSCATED:
					//$newString .= "<span style=text-decoration:line-through>";
					//++$tokens;
					break;
				case ColorFormat::ITALIC:
					$newString .= "<span style=font-style:italic>";
					++$tokens;
					break;
				case ColorFormat::UNDERLINE:
					$newString .= "<span style=text-decoration:underline>";
					++$tokens;
					break;
				case ColorFormat::STRIKETHROUGH:
					$newString .= "<span style=text-decoration:line-through>";
					++$tokens;
					break;
				case ColorFormat::RESET:
					$newString .= str_repeat("</span>", $tokens);
					$tokens = 0;
					break;

				//Colors
				case ColorFormat::BLACK:
					$newString .= "<span style=color:#000>";
					++$tokens;
					break;
				case ColorFormat::DARK_BLUE:
					$newString .= "<span style=color:#00A>";
					++$tokens;
					break;
				case ColorFormat::DARK_GREEN:
					$newString .= "<span style=color:#0A0>";
					++$tokens;
					break;
				case ColorFormat::DARK_AQUA:
					$newString .= "<span style=color:#0AA>";
					++$tokens;
					break;
				case ColorFormat::DARK_RED:
					$newString .= "<span style=color:#A00>";
					++$tokens;
					break;
				case ColorFormat::DARK_PURPLE:
					$newString .= "<span style=color:#A0A>";
					++$tokens;
					break;
				case ColorFormat::GOLD:
					$newString .= "<span style=color:#FA0>";
					++$tokens;
					break;
				case ColorFormat::GRAY:
					$newString .= "<span style=color:#AAA>";
					++$tokens;
					break;
				case ColorFormat::DARK_GRAY:
					$newString .= "<span style=color:#555>";
					++$tokens;
					break;
				case ColorFormat::BLUE:
					$newString .= "<span style=color:#55F>";
					++$tokens;
					break;
				case ColorFormat::GREEN:
					$newString .= "<span style=color:#5F5>";
					++$tokens;
					break;
				case ColorFormat::AQUA:
					$newString .= "<span style=color:#5FF>";
					++$tokens;
					break;
				case ColorFormat::RED:
					$newString .= "<span style=color:#F55>";
					++$tokens;
					break;
				case ColorFormat::LIGHT_PURPLE:
					$newString .= "<span style=color:#F5F>";
					++$tokens;
					break;
				case ColorFormat::YELLOW:
					$newString .= "<span style=color:#FF5>";
					++$tokens;
					break;
				case ColorFormat::WHITE:
					$newString .= "<span style=color:#FFF>";
					++$tokens;
					break;
				default:
					$newString .= $token;
					break;
			}
		}

		$newString .= str_repeat("</span>", $tokens);

		return $newString;
	}

	/**
	 * Returns a string with colorized ANSI Escape codes
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function toANSI($string){
		if(!is_array($string)){
			$string = self::tokenize($string);
		}

		$newString = "";
		foreach($string as $token){
			switch($token){
				case ColorFormat::BOLD:
					$newString .= Terminal::$FORMAT_BOLD;
					break;
				case ColorFormat::OBFUSCATED:
					$newString .= Terminal::$FORMAT_OBFUSCATED;
					break;
				case ColorFormat::ITALIC:
					$newString .= Terminal::$FORMAT_ITALIC;
					break;
				case ColorFormat::UNDERLINE:
					$newString .= Terminal::$FORMAT_UNDERLINE;
					break;
				case ColorFormat::STRIKETHROUGH:
					$newString .= Terminal::$FORMAT_STRIKETHROUGH;
					break;
				case ColorFormat::RESET:
					$newString .= Terminal::$FORMAT_RESET;
					break;

				//Colors
				case ColorFormat::BLACK:
					$newString .= Terminal::$COLOR_BLACK;
					break;
				case ColorFormat::DARK_BLUE:
					$newString .= Terminal::$COLOR_DARK_BLUE;
					break;
				case ColorFormat::DARK_GREEN:
					$newString .= Terminal::$COLOR_DARK_GREEN;
					break;
				case ColorFormat::DARK_AQUA:
					$newString .= Terminal::$COLOR_DARK_AQUA;
					break;
				case ColorFormat::DARK_RED:
					$newString .= Terminal::$COLOR_DARK_RED;
					break;
				case ColorFormat::DARK_PURPLE:
					$newString .= Terminal::$COLOR_PURPLE;
					break;
				case ColorFormat::GOLD:
					$newString .= Terminal::$COLOR_GOLD;
					break;
				case ColorFormat::GRAY:
					$newString .= Terminal::$COLOR_GRAY;
					break;
				case ColorFormat::DARK_GRAY:
					$newString .= Terminal::$COLOR_DARK_GRAY;
					break;
				case ColorFormat::BLUE:
					$newString .= Terminal::$COLOR_BLUE;
					break;
				case ColorFormat::GREEN:
					$newString .= Terminal::$COLOR_GREEN;
					break;
				case ColorFormat::AQUA:
					$newString .= Terminal::$COLOR_AQUA;
					break;
				case ColorFormat::RED:
					$newString .= Terminal::$COLOR_RED;
					break;
				case ColorFormat::LIGHT_PURPLE:
					$newString .= Terminal::$COLOR_LIGHT_PURPLE;
					break;
				case ColorFormat::YELLOW:
					$newString .= Terminal::$COLOR_YELLOW;
					break;
				case ColorFormat::WHITE:
					$newString .= Terminal::$COLOR_WHITE;
					break;
				default:
					$newString .= $token;
					break;
			}
		}

		return $newString;
	}

}
