<?php
/**
 * PropertiesHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper;


class PropertiesHelper
{
    /**
     * Regex to identify properties ${} in text
     *
     * @var string
     */
    protected $propertyRegex = '/\$\{([^\$}]+)\}/';

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param string $text
     * @return mixed
     */
    public function getPropertiesInText($text)
    {
        preg_match_all($this->propertyRegex, $text, $matches);
        return $matches[1];
    }

    /**
     * @param string $text
     * @param array $properties
     * @return mixed|null
     */
    public function replacePropertiesInText($text, array $properties)
    {
        if ($text === null || !$properties) {
            return null;
        }

        $this->properties = $properties;
        $replacedText = $this->getTextWithReplacedTokens($text);

        return $replacedText;
    }

    /**
     * @param string $text
     * @return string
     */
    protected function getTextWithReplacedTokens($text)
    {
        $iteration = 0;
        while (strpos($text, '${') !== false) {
            $text = preg_replace_callback(
                $this->propertyRegex,
                [$this, 'replacePropertyCallback'],
                $text
            );

            // keep track of iterations so we can break out of otherwise infinite loops.
            $iteration++;
            if ($iteration == 5) {
                return $text;
            }
        }
        return $text;

    }

    /**
     * @param $matches
     * @return string
     */
    protected function replacePropertyCallback($matches)
    {
        $propertyName = $matches[1];
        if (!isset($this->properties[$propertyName])) {
            return $matches[0];
        }

        $propertyValue = $this->properties[$propertyName];
        return $propertyValue;
    }


}